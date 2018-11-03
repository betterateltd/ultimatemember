<?php $roles_array = array();

foreach ( UM()->roles()->get_roles() as $key => $value ) {
	$_um_roles = UM()->query()->get_meta_value( '_um_roles', $key );
	if ( ! empty( $_um_roles ) ) {
		$roles_array[] = $_um_roles;
	}
}

$show_these_users = get_post_meta( get_the_ID(), '_um_show_these_users', true );
if ( $show_these_users ) {
	$show_these_users = implode( "\n", str_replace( "\r", "", $show_these_users ) );
}

$sorting_fields = UM()->members()->get_sorting_fields();

$post_id = get_the_ID();
$_um_sorting_fields = get_post_meta( $post_id, '_um_sorting_fields', true );
$_um_view_types = get_post_meta( $post_id, '_um_view_types', true ); ?>

<div class="um-admin-metabox">

	<?php
	/**
	 * UM hook
	 *
	 * @type filter
	 * @title um_admin_directory_sort_users_select
	 * @description Extend Sort Types for Member Directory
	 * @input_vars
	 * [{"var":"$sort_types","type":"array","desc":"Sort Types"}]
	 * @change_log
	 * ["Since: 2.0"]
	 * @usage add_filter( 'um_admin_directory_sort_users_select', 'function_name', 10, 1 );
	 * @example
	 * <?php
	 * add_filter( 'um_admin_directory_sort_users_select', 'my_directory_sort_users_select', 10, 1 );
	 * function my_directory_sort_users_select( $sort_types ) {
	 *     // your code here
	 *     return $sort_types;
	 * }
	 * ?>
	 */
	$sort_options = apply_filters( 'um_admin_directory_sort_users_select', array(
		'user_registered_desc'	=> __( 'New users first', 'ultimate-member' ),
		'user_registered_asc'	=> __( 'Old users first', 'ultimate-member' ),
		'last_login'			=> __( 'Last login', 'ultimate-member' ),
		'display_name'			=> __( 'Display Name', 'ultimate-member' ),
		'first_name'			=> __( 'First Name', 'ultimate-member' ),
		'last_name'				=> __( 'Last Name', 'ultimate-member' ),
		'random'				=> __( 'Random', 'ultimate-member' ),
		'other'					=> __( 'Other (custom field)', 'ultimate-member' ),
	) );

	$fields = array(
		array(
			'id'		=> '_um_mode',
			'type'		=> 'hidden',
			'value'		=> 'directory',
		),
		array(
			'id'		=> '_um_view_types',
			'type'		=> 'select',
			'multi'		=> true,
			'label'		=> __( 'View type(s)', 'ultimate-member' ),
			'tooltip'	=> __( 'View type a specific parameter in the directory', 'ultimate-member' ),
			'options'	=> array(
				'grid'      => __( 'Grid', 'ultimate-member' ),
				'list'      => __( 'List', 'ultimate-member' ),
			),
			'value'		=> $_um_view_types,
		),
		array(
			'id'		=> '_um_default_view',
			'type'		=> 'select',
			'label'		=> __( 'Default view type', 'ultimate-member' ),
			'tooltip'	=> __( 'Default directory view type', 'ultimate-member' ),
			'options'	=> array(
				'grid'      => __( 'Grid', 'ultimate-member' ),
				'list'      => __( 'List', 'ultimate-member' ),
			),
			'value'		=> UM()->query()->get_meta_value( '_um_default_view', null, '' ),
			'conditional' => array( '_um_view_types', 'length', 2 )
		),
		array(
			'id'		=> '_um_roles',
			'type'		=> 'select',
			'label'		=> __( 'User Roles to Display', 'ultimate-member' ),
			'tooltip'	=> __( 'If you do not want to show all members, select only user roles to appear in this directory', 'ultimate-member' ),
			'options'	=> UM()->roles()->get_roles(),
			'multi'		=> true,
			'value'		=> $roles_array,
		),
		array(
			'id'		=> '_um_has_profile_photo',
			'type'		=> 'checkbox',
			'label'		=> __( 'Only show members who have uploaded a profile photo', 'ultimate-member' ),
			'tooltip'	=> __( 'If \'Use Gravatars\' as profile photo is enabled, this option is ignored', 'ultimate-member' ),
			'value'		=> UM()->query()->get_meta_value( '_um_has_profile_photo' ),
		),
		array(
			'id'		=> '_um_has_cover_photo',
			'type'		=> 'checkbox',
			'label'		=> __( 'Only show members who have uploaded a cover photo', 'ultimate-member' ),
			'value'		=> UM()->query()->get_meta_value( '_um_has_cover_photo' ),
		),
		array(
			'id'		=> '_um_sorting_fields',
			'type'		=> 'multi_selects',
			'label'		=> __( 'Choose field(s) to enable in sorting', 'ultimate-member' ),
			'value'		=> $_um_sorting_fields,
			'options'   => $sorting_fields,
			'add_text'		=> __( 'Add New Field','ultimate-member' ),
			'show_default_number'	=> 0,
		),
		array(
			'id'		=> '_um_sortby',
			'type'		=> 'select',
			'label'		=> __( 'Default sort users by', 'ultimate-member' ),
			'tooltip'	=> __( 'Default sorting users by a specific parameter in the directory', 'ultimate-member' ),
			'options'	=> $sort_options,
			'value'		=> UM()->query()->get_meta_value( '_um_sortby' ),
			'conditional' => array( '_um_sorting_fields', '!=', '' )
		),
		array(
			'id'		    => '_um_sortby_custom',
			'type'		    => 'text',
			'label'		    => __( 'Meta key', 'ultimate-member' ),
			'tooltip'	    => __( 'To sort by a custom field, enter the meta key of field here', 'ultimate-member' ),
			'value'		    => UM()->query()->get_meta_value( '_um_sortby_custom', null, 'na' ),
			'conditional'   => array( '_um_sortby', '=', 'other' )
		),
		array(
			'id'		    => '_um_show_these_users',
			'type'		    => 'textarea',
			'label'		    => __( 'Only show specific users (Enter one username per line)', 'ultimate-member' ),
			'value'		    => $show_these_users,
		)
	);

	/**
	 * UM hook
	 *
	 * @type filter
	 * @title um_admin_extend_directory_options_general
	 * @description Extend Directory options fields
	 * @input_vars
	 * [{"var":"$fields","type":"array","desc":"Directory options fields"}]
	 * @change_log
	 * ["Since: 2.0"]
	 * @usage add_filter( 'um_admin_directory_sort_users_select', 'function_name', 10, 1 );
	 * @example
	 * <?php
	 * add_filter( 'um_admin_directory_sort_users_select', 'my_directory_sort_users_select', 10, 1 );
	 * function my_directory_sort_users_select( $sort_types ) {
	 *     // your code here
	 *     return $sort_types;
	 * }
	 * ?>
	 */
	$fields = apply_filters( 'um_admin_extend_directory_options_general', $fields );

	UM()->admin_forms( array(
		'class'		=> 'um-member-directory-general um-half-column',
		'prefix_id'	=> 'um_metadata',
		'fields' 	=> $fields
	) )->render_form(); ?>

	<div class="um-admin-clear"></div>

</div>