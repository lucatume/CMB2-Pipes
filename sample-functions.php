<?php
add_action( 'cmb2_init', 'post_meta_boxes' );
function post_meta_boxes() {

	$post_meta_box = new_cmb2_box( array(
		'id'           => 'post-metabox',
		'title'        => __( 'A Meta Box', 'cmb2' ),
		'object_types' => array( 'post' )
	) );

	$post_meta_box->add_field( array(
		'name' => __( 'The post title', 'cmb2' ),
		'id'   => 'title',
		'type' => 'text_small',
	) );
}

add_filter( 'cmb2_override_title_meta_save', 'save_the_post_title', 10, 4 );
function save_the_post_title( $override, $args, $field_args, CMB2_Field $field ) {
	remove_filter( 'cmb2_override_title_meta_save', __FUNCTION__ );
	$args       = (object) $args;
	$post_title = $args->value;
	$post_id    = $args->id;
	wp_update_post( array( 'ID' => $post_id, 'post_title' => $post_title ) );

	// do override, do not save the meta
	return true;
}

add_filter( 'cmb2_override_title_meta_value', 'get_the_post_title', 10, 4 );
function get_the_post_title( $override, $object_id, $args, CMB2_Field $field ) {
	return get_the_title( $object_id );
}


