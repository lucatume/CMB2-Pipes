<?php
add_action( 'cmb2_init', 'post_meta_boxes' );
function post_meta_boxes() {

	$post_meta_box = new_cmb2_box( array(
		'id'           => 'post-metabox',
		'title'        => __( 'A Meta Box', 'cmb2' ),
		// a custom post type
		'object_types' => array( 'post' )
	) );

	$post_meta_box->add_field( array(
		'name' => __( 'A representative image', 'cmb2' ),
		// a `_thumbnail_id` meta will be saved...
		'id'   => '_thumbnail',
		'type' => 'file',
		'options' => ['url'=> false]
	) );

	$post_meta_box->add_field( array(
		'name' => __( 'The quote', 'cmb2' ),
		// no meta called `quote` will be written, not really relevant
		'id'   => cmb2_pipe('quote_title','<>', 'post_title'),
		'type' => 'text',
	) );

	$post_meta_box->add_field( array(
		'name' => __( 'Quoted author', 'cmb2' ),
		// once again not relevant, no meta called `quote_author` will be saved
		'id'   => cmb2_p2p_pipe('quote_author','<>', 'author_to_quote', 'to'),
		'type' => 'select',
		'options' => ['Oscar Wilde', 'Samuel L. Jackson', 'Sun Tzu' ]
	) );
}
