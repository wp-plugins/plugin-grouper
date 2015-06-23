<?php
/**
 * Init
 *
 * project	Plugin Grouper
 * version	1.0.0
 * Author: Sujin 수진 Choi
 * Author URI: http://www.sujinc.com/
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class  PIGPR_Init {
	private $is_group_query;

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function __construct() {
		# AJAX
		add_action( 'wp_ajax_PIGPR_CREATE_GROUP', array( $this, 'create_group' ) );
		add_action( 'wp_ajax_PIGPR_INPUT_INTO_GROUP', array( $this, 'input_into_group' ) );
		add_action( 'wp_ajax_PIGPR_DELETE_FROM_GROUP', array( $this, 'delete_from_group' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		# Condition
		global $pagenow;
		if ( !is_admin() || $pagenow != 'plugins.php' ) return false;

		$this->is_group_query = !empty( $_GET['plugin_group'] );

		# Hooks
 		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( "views_plugins", array( $this, 'views_plugins' ) );
		add_filter( "views_plugins-network", array( $this, 'views_plugins' ) );
/*
		add_filter( "manage_plugins_columns", array( $this, 'manage_columns' ) );
		add_action( 'manage_plugins_custom_column', array( $this, 'manage_custom_column' ), 15, 3 );
*/
		add_action( 'pre_current_active_plugins', array( $this, 'print_modal' ) );

		add_filter( "plugin_row_meta", array( $this, 'print_groups' ), 15, 3 );

		if ( $this->is_group_query ) {
			add_filter( 'all_plugins', array( $this, 'all_plugins' ) );
			add_action( 'admin_footer', array( $this, 'print_group_information' ) );
			add_action( 'wp_loaded', array( $this, 'delete_group' ) );
		}
	}

	/**
	 * Filter[plugin_row_meta] : Print Groups set on each Plugins.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param: (array) $plugin_meta, (string) $plugin_file, (array) $plugin_data
	 * @return: (array) $plugin_meta
	 *
	 * see	/wp-admin/includes/class-wp-list-table.php
	 */
	function print_groups( $plugin_meta, $plugin_file, $plugin_data ) {
		$groups = get_option( 'plugin_groups_match' );
		$slug = sanitize_title( $plugin_data['Name'] );

		echo '<div class="groups">';

		if ( !empty( $groups[$slug] ) ) {
			foreach( $groups[$slug] as $key => $name ) {
				printf( '<a href="%s?plugin_group=%s" data-id="%s">%s</a>', $this->get_plugins_url(), $key, $key, $name );
			}
		}
		echo '</div>';

		return $plugin_meta;
	}

	/**
	 * Action[admin_footer] : Print Selected Group Name in Input From.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function print_group_information() {
		$groups = get_option( 'plugin_groups' );
		printf( '<input type="hidden" id="plugin_group_name" value="%s" />', $groups[$_GET['plugin_group']] );
	}

	/**
	 * Action[init] : Delete Selected Group.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function delete_group() {
		$action = $_GET['action'];
		$group_id = $_GET['group_id'];

		if ( $action != 'delete_group' || empty( $group_id ) ) return false;

		$groups = get_option( 'plugin_groups' );
		$plugin_groups_match = get_option( 'plugin_groups_match' );
		$plugin_groups_match_replace = $plugin_groups_match;
		$groups_plugin_match = get_option( 'groups_plugin_match' );

		unset( $groups[$group_id] );
		unset( $groups_plugin_match[$group_id] );

		foreach ( $plugin_groups_match as $plugin_key => $plugin_groups ) {
			foreach( $plugin_groups as $group_key => $value ) {
				if ( $group_key == $group_id ) {
					unset( $plugin_groups_match_replace[$plugin_key][$group_key] );
				}
			}
		}

		update_option( 'plugin_groups', $groups );
		update_option( 'plugin_groups_match', $plugin_groups_match_replace );
		update_option( 'groups_plugin_match', $groups_plugin_match );

		wp_redirect( $this->get_plugins_url() );
		die;
	}

	/**
	 * Ajax: Create Group.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function create_group() {
		$groups = get_option( 'plugin_groups' );

		$name = $_POST[ 'group_name' ];
		$key = sanitize_title( $name );

		if ( $key && empty( $groups[$key] ) ) {
			$groups[$key] = $name;
			update_option( 'plugin_groups', $groups );

			echo json_encode( array( $key, $name ) );
		}

		wp_die();
	}

	/**
	 * Ajax: Input Plugin into Group.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function input_into_group() {
		$groups = get_option( 'plugin_groups_match' );
		$plugins = get_option( 'groups_plugin_match' );

		$group_id = $_POST[ 'group_id' ];
		$group_name = $_POST[ 'group_name' ];
		$plugin_id = $_POST[ 'plugin_id' ];

		if ( !is_array( $groups ) ) $groups = array();
		if ( !is_array( $groups[$plugin_id] ) ) $groups[$plugin_id] = array();

		if ( !is_array( $plugins ) ) $plugins = array();
		if ( !is_array( $plugins[$group_id] ) ) $plugins[$group_id] = array();

		$groups[$plugin_id][$group_id] = $group_name;
		$plugins[$group_id][$plugin_id] = $plugin_id;

		update_option( 'plugin_groups_match', $groups );
		update_option( 'groups_plugin_match', $plugins );

		echo json_encode( array( $this->get_plugins_url() . '?plugin_group=' . $group_id ) );

		wp_die();
	}

	/**
	 * Ajax: Delete Plugin from Group.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function delete_from_group() {
		$groups = get_option( 'plugin_groups_match' );
		$plugins = get_option( 'groups_plugin_match' );

		$group_id = $_POST[ 'group_id' ];
		$group_name = $_POST[ 'group_name' ];
		$plugin_id = $_POST[ 'plugin_id' ];

		unset( $groups[$plugin_id][$group_id] );
		update_option( 'plugin_groups_match', $groups );

		unset( $plugins[$group_id][$plugin_id] );
		update_option( 'groups_plugin_match', $plugins );

		wp_die();
	}

	/**
	 * Filter[all_plugins]: Change Plugin List Items on Group View Screen.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param: (array) $plugins
	 * @return: (array) $plugins
	 */
	function all_plugins( $plugins ) {
		if ( $this->is_group_query ) {
			$plugins = array();
			$get_plugins = get_plugins();
			$groups_plugin_match = get_option( 'groups_plugin_match' );

			foreach( $get_plugins as $key => $value ) {
				$slug = sanitize_title( $value['Name'] );

				if ( !empty( $groups_plugin_match[$_GET['plugin_group']][$slug] ) ) {
					$plugins[$key] = $value;
				}
			}
		}

		return $plugins;
	}

	/**
	 * Action[pre_current_active_plugins]: Print Basic Grouping Option Element.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function print_modal() {
		$groups = get_option( 'plugin_groups' );
		include_once( PIGPR_TEMPLATE_DIR . 'modal_table.php' );
	}

	/**
	 * Filter[admin_enqueue_scripts]
	 *
	 * @since 1.0.0
	 * @access public
	 */
	function enqueue_scripts() {
		# Adding Grouping Actions on Dropdown Menu
		wp_enqueue_script( 'plugin-grouper', PIGPR_ASSETS_URL . 'plugin-grouper.js', array( 'jquery' ), '1.0.0' );
		wp_enqueue_style( 'plugin-grouper', PIGPR_ASSETS_URL . 'plugin-grouper.css' );
	}

	/**
	 * Filter[views_plugins] : Adding Plugin Category on Table Filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param: (array) $views List Tabs
	 * @return: (array) List Tabs
	 *
	 * see	/wp-admin/includes/class-wp-list-table.php
	 */
	function views_plugins( $views ) {
		$groups = get_option( 'plugin_groups' );
		if ( empty( $groups ) ) return $views;
		if ( $this->is_group_query ) {
			unset($views);
			$views['all'] = '<a href="plugins.php?plugin_status=all">All</a>';

			foreach( $groups as $key => $value ) {
				$class = ( $_GET['plugin_group'] == $key ) ? 'current' : '';
				$views[$key] = '<a href="plugins.php?plugin_group='.$key.'" class="'.$class.'">'.$value.'</a>';
			}
		} else {
			echo "<ul class='subsubsub'>\n";
			echo "<li><strong>Groups</strong> |</li>";
			foreach( $groups as $key => $value ) {
				$groups[ $key ] = "\t<li class='$key'><a href='plugins.php?plugin_group=$key'>$value</a>";
			}
			echo implode( " |</li>\n", $groups ) . "</li>\n";
			echo "</ul>";
			echo "<div class='clear'></div>";

		}

		return $views;
	}

	/**
	 * Get Admin Plugins URL * inc. network site.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @return: (string) $url
	 */
	private function get_plugins_url() {
		$url = get_bloginfo( 'home' ) . '/wp-admin/';
		$url .= ( is_network_admin() ) ? 'network/' : '';
		$url .= 'plugins.php';

		return $url;
	}
}

new PIGPR_Init();







/*
	function manage_columns( $columns ) {
		$columns[ 'group' ] = 'Group';
		return $columns;
	}

	function manage_custom_column( $column_name, $plugin_file, $plugin_data ) {
		switch ( $column_name ) {
			case 'group' :
				$groups = get_option( 'plugin_groups_match' );
				$slug = sanitize_title( $plugin_data['Name'] );

				foreach( $groups[$slug] as $key => $name ) {
					?>
					<a href="<?php echo $this->get_plugins_url() . '?plugin_group=' . $key  ?>" data-id="<?php echo $key ?>"><?php echo $name ?></a>
					<?php
				}

				break;
		}
	}
*/



