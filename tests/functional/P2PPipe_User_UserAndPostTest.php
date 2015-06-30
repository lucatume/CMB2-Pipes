<?php


class P2PPipe_User_UserAndPostTest extends \WP_UnitTestCase {

	public static function setUpBeforeClass() {
		load_p2p();
	}

	public function setUp() {
		parent::setUp();
		p2p_register_connection_type( [
			'name' => 'user_to_post',
			'from' => 'user',
			'to'   => 'post'
		] );
		p2p_register_connection_type( [
			'name' => 'post_to_user',
			'from' => 'post',
			'to'   => 'user'
		] );
	}

	public function writingDirections() {
		return [ [ '<>' ], [ '>' ] ];
	}

	public function readngDirections() {
		return [ [ '<>' ], [ '<' ] ];
	}

	/**
	 * @test
	 * it should allow piping to a user to post p2p relation
	 * @dataProvider writingDirections
	 */
	public function it_should_allow_piping_to_a_user_to_post_p_2_p_relation( $direction ) {
		$user_id = $this->factory->user->create();
		$post_id = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'user',
			'field_args'  => [
				'name' => __( 'Relate a user with a post', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'user_field', $direction, 'user_to_post' ),
				'type' => 'text' // not relevant
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $post_id );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$in_db = $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_from = $user_id" );
		$this->assertEquals( [ $post_id ], $in_db );
	}

	/**
	 * @test
	 * it should allow piping multiple user to posts relations
	 * @dataProvider writingDirections
	 */
	public function it_should_allow_piping_multiple_user_to_posts_relations( $direction ) {
		$user_id = $this->factory->user->create();

		$post_id_1 = $this->factory->post->create();
		$post_id_2 = $this->factory->post->create();
		$post_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'user',
			'field_args'  => [
				'name'       => __( 'Relate a user with a post', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'user_field', $direction, 'user_to_post' ),
				'type'       => 'text', // not relevant
				'repeatable' => true
			]
		];

		$field = new CMB2_Field( $args );
		$set   = [ $post_id_1, $post_id_2, $post_id_3 ];

		$field->save_field( $set );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$in_db = $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_from = $user_id" );
		$this->assertEqualSets( $set, $in_db );
	}

	/**
	 * @test
	 * it should allow piping to a post to user p2p relation
	 * @dataProvider writingDirections
	 */
	public function it_should_allow_piping_to_a_post_to_user_p_2_p_relation( $direction ) {
		$user_id = $this->factory->user->create();
		$post_id = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'user',
			'field_args'  => [
				'name' => __( 'Relate a user with a post', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'user_field', $direction, 'post_to_user', 'to' ),
				'type' => 'text' // not relevant
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $post_id );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$in_db = $wpdb->get_col( "select p2p_from from $wpdb->p2p where p2p_to = $user_id" );
		$this->assertEquals( [ $post_id ], $in_db );
	}

	/**
	 * @test
	 * it should allow piping multiple post to user relations
	 * @dataProvider writingDirections
	 */
	public function it_should_allow_piping_multiple_user_to_post_relations( $direction ) {
		$user_id = $this->factory->user->create();

		$post_id_1 = $this->factory->post->create();
		$post_id_2 = $this->factory->post->create();
		$post_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'user',
			'field_args'  => [
				'name'       => __( 'Relate a user with a post', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'user_field', $direction, 'post_to_user', 'to' ),
				'type'       => 'text', // not relevant
				'repeatable' => true
			]
		];

		$field = new CMB2_Field( $args );
		$set   = [ $post_id_1, $post_id_2, $post_id_3 ];

		$field->save_field( $set );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$in_db = $wpdb->get_col( "select p2p_from from $wpdb->p2p where p2p_to = $user_id" );
		$this->assertEqualSets( $set, $in_db );
	}

	/**
	 * @test
	 * it should not write any user meta when direction is read
	 * @dataProvider readngDirections
	 */
	public function it_should_not_write_any_user_meta_when_direction_is_read( $direction ) {
		$user_id = $this->factory->user->create();
		$post_id = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'user',
			'field_args'  => [
				'name' => __( 'Relate a user with a post', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'user_field', $direction, 'post_to_user' ),
				'type' => 'text' // not relevant
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $post_id );

		/** @var \wpdb $wpdb */
		$this->assertEmpty( get_user_meta( $user_id, 'user_field' ) );
	}

	/**
	 * @test
	 * it should not write any user meta when direction is reading and p2p direction is to
	 * @dataProvider readngDirections
	 */
	public function it_should_not_write_any_user_meta_when_direction_is_read_and_p2p_direction_is_to( $direction ) {
		$user_id = $this->factory->user->create();
		$post_id = $this->factory->post->create();

		$args = [
			'object_id'   => $user_id,
			'object_type' => 'user',
			'field_args'  => [
				'name' => __( 'Relate a user with a post', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'user_field', $direction, 'post_to_user', 'to' ),
				'type' => 'text' // not relevant
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $post_id );

		/** @var \wpdb $wpdb */
		$this->assertEmpty( get_user_meta( $user_id, 'user_field' ) );
	}
}