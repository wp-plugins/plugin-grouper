jQuery( document ).ready( function( $ ) {
	$( '.wp-list-table.plugins .row-actions span:last-child' ).append( ' | ' );
	$( '.wp-list-table.plugins tbody tr' ).each( function() {
		var id = $(this).attr( 'id' );
		$(this).find( '.row-actions' ).append( '<span class="group"><a href="#" class="action-grouping" data-id="'+id+'">Group</a></span>' );
	});

	if ( $( 'input#plugin_group_name' ).length ) {
		change_header( $( 'input#plugin_group_name' ).val() );
	}

	// 개별 그룹 액션
	$( '.action-grouping' ).click( function(e) {
		e.preventDefault();

		if ( $(this).hasClass( 'group_open' ) ) {
			close_grouping();
			return true;
		}
		close_grouping();

		var id = $(this).attr( 'data-id' );
		var html = $( '#Grouping-Row' ).html()

		$( '.wp-list-table.plugins tr#'+escape_special_character( id ) ).after('<tr class="inactive grouping" data-id="'+id+'"><td colspan="1000">'+html+'</td></tr>');

		bind_button_close();
		bind_button_create();
		checkbox_action();
		checkbox_checking( id );

		$(this).addClass( 'group_open' );
		return true;
	});

	// 체크박스를 클릭했을 때
	function checkbox_action() {
		$( '.wp-list-table.plugins tr.grouping ul li input[type="checkbox"]' ).click( function() {
			// 그룹에 추가
			if ( $(this).is(":checked") ) {
				var plugin_id = $(this).parent().parent().parent().parent().attr( 'data-id' );

				var group_id = $(this).attr( 'data-id' );
				var group_name = $(this).attr( 'data-name' );

				var data = {
					'action': 'PIGPR_INPUT_INTO_GROUP',
					'plugin_id' : plugin_id,
					'group_id' : group_id,
					'group_name' : group_name
				};

				disable_grouping();

				$.post( ajaxurl, data, function( response ) {
					enable_grouping();

					var html = '<a href="'+response[0]+'" data-id="'+escape_special_character( group_id )+'">'+group_name+'</a>';
					$( '.wp-list-table.plugins tr#' + escape_special_character( plugin_id ) + ' td.column-description .groups' ).append( html );
				}, 'json' );
			} else {
			// 그룹에서 제외
				var plugin_id = $(this).parent().parent().parent().parent().attr( 'data-id' );
				var group_id = $(this).attr( 'data-id' );
				var group_name = $(this).attr( 'data-name' );

				var data = {
					'action': 'PIGPR_DELETE_FROM_GROUP',
					'plugin_id' : plugin_id,
					'group_id' : group_id,
					'group_name' : group_name
				};

				disable_grouping();

				$.post( ajaxurl, data, function( response ) {
					enable_grouping();

					$( '.wp-list-table.plugins tr#' + escape_special_character( plugin_id ) + ' td.column-description .groups a[data-id="'+escape_special_character( group_id )+'"]' ).remove();
				}, 'json' );
			}
		});
	}

	function close_grouping() {
		$( '.wp-list-table.plugins tr.grouping' ).remove();
		$( '.group_open' ).removeClass( 'group_open' );
	}

	function disable_grouping() {
		$( '.wp-list-table.plugins .loading_spinner' ).show();
		$( '.wp-list-table.plugins tr.grouping ul li input[type="checkbox"]' ).attr( 'disabled', true );
	}

	function enable_grouping() {
		$( '.wp-list-table.plugins .loading_spinner' ).hide();
		$( '.wp-list-table.plugins tr.grouping ul li input[type="checkbox"]' ).removeAttr( 'disabled' );
	}

	function bind_button_close() {
		$( '.wp-list-table.plugins .btn-close_group' ).click( function(e) {
			e.preventDefault();
			close_grouping();
			return true;
		});
	}

	function bind_button_create() {
		$( '.wp-list-table.plugins .btn-create_group' ).click( function(e) {
			e.preventDefault();
			if ( $( '.wp-list-table.plugins .inp-create_group' ).val().length ) {
				var data = {
					'action': 'PIGPR_CREATE_GROUP',
					'group_name' : $( '.wp-list-table.plugins .inp-create_group' ).val()
				};

				disable_grouping();

				$.post( ajaxurl, data, function( response ) {
					enable_grouping();

					var html = '<li><input id="group-'+response[0]+'" type="checkbox" data-id="'+response[0]+'"  data-name="'+response[1]+'">'+response[1]+'</li>';
					$( '.wp-list-table.plugins tr.grouping ul' ).append( html );
					$( '#Grouping-Row ul' ).append( html );

					checkbox_action();
					$( '.wp-list-table.plugins .inp-create_group' ).val('');
				}, 'json' );
			}
			return true;
		});
	}

	function checkbox_checking( plugin_id ) {
		$( '.wp-list-table.plugins tr.grouping ul li input' ).removeAttr( 'checked' );

		$( '.wp-list-table.plugins tr#' + escape_special_character( plugin_id ) + ' td.column-description .groups a' ).each( function() {
			var id = $(this).attr( 'data-id' );
			$( '.wp-list-table.plugins tr.grouping ul li input[data-id="' + escape_special_character( id ) +'"]' ).attr( 'checked', true );
		});
	}

	function change_header( plugin_group_name ) {
		var plugin_group_id = getUrlParameter( 'plugin_group' );

		var html = 'Plugin Group : ' + plugin_group_name;
		html += ' <a href="#" class="add-new-h2 btn-delete_group">Delete Group</a>';
		$( '#wpbody .wrap > h2' ).html( html );

		$( '.btn-delete_group' ).click( function(e) {
			e.preventDefault();
			window.location.href = window.location.href + "&action=delete_group&group_id=" + plugin_group_id;
		});
	}

	function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1);
		var sURLVariables = sPageURL.split('&');
		for (var i = 0; i < sURLVariables.length; i++)  {
			var sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == sParam)  {
				return sParameterName[1];
			}
		}
	}

	function escape_special_character( text ) {
		text = text.replace( /([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g,'\\$1' );
		return text;
	}
});






/*
	// 벌크 액션
	$( '.tablenav select' ).append( '<option value="group-selected">Grouping</option>' );
	$( '#bulk-action-form #doaction' ).click( function(e) {
		if ( $( '#bulk-action-selector-top' ).val() == 'group-selected' ) {
			e.preventDefault();
		}
	});
	$( '#bulk-action-form #doaction2' ).click( function() {
		if ( $( '#bulk-action-selector-bottom' ).val() == 'group-selected' ) {
			e.preventDefault();
		}
	});
*/
