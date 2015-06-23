<div id="Grouping-Row" style="display:none;">
	<h4>Grouping</h4>
	<ul>
		<?php foreach( $groups as $key => $value ) { ?>
			<li><input id="group-<?php echo $key ?>" type="checkbox" data-id="<?php echo $key ?>" data-name="<?php echo $value ?>"><?php echo $value ?></li>
		<?php } ?>
	</ul>

	<img class="loading_spinner" src="<?php bloginfo('home') ?>/wp-admin/images/loading.gif" style="display:none;" />

	<div class="clear"></div>

	<input type="text" class="inp-create_group" />
	<a href="#" class="button button-primary btn-create_group">Create Group</a>
	<a href="#" class="button btn-close_group">Close</a>
</div>
