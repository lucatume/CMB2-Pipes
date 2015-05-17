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
		'id'   => cmb2_pipe('the_title','>', 'post_title'),
		'type' => 'text_small',
	) );
}


