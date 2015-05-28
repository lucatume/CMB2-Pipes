<?php


class P2PPipeUserAndPostTest extends \WP_UnitTestCase {

	public static function setUpBeforeClass() {
		load_p2p();
		p2p_register_connection_type( [
			'name' => 'user_to_post',
			'from' => 'user',
			'to'   => 'post'
		] );
	}

	/**
	 * @test
	 * it should allow piping to a user to post p2p relation
	 */
	public function it_should_allow_piping_to_a_user_to_post_p_2_p_relation() {
		$user_id = $this->factory->user->create();
		$post_id = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'Relate a user with a post', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'user_field', '<>', 'user_to_post' ),
				'type' => 'text' // not relevant
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $post_id );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$this->assertEquals( [ $post_id ], $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_from = $user_id" ) );
	}

	/**
	 * @test
	 * it should allow piping multiple user to posts relations
	 */
	public function it_should_allow_piping_multiple_user_to_posts_relations() {
		$user_id = $this->factory->user->create();

		$post_id_1 = $this->factory->post->create();
		$post_id_2 = $this->factory->post->create();
		$post_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'Relate a user with a post', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'user_field', '<>', 'user_to_post' ),
				'type' => 'text' // not relevant
			]
		];

		$field = new CMB2_Field( $args );

		$set = array( $post_id_1, $post_id_2, $post_id_3 );
		$field->save_field( $set );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$this->assertEqualSets( $set, $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_from = $user_id" ) );
	}

}