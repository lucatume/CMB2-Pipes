<?php


class TAD_Pipe_P2PPipeTest extends \WP_UnitTestCase {

	protected $backupGlobals = false;

	public static function setUpBeforeClass() {
		self::load_p2p();

		p2p_register_connection_type( [
			'name' => 'post_to_post',
			'from' => 'post',
			'to'   => 'post'
		] );
	}

	private static function load_p2p() {
		require_once PLUGIN_FOLDER . '/posts-to-posts/posts-to-posts.php';

		$p2p_core = PLUGIN_FOLDER . '/posts-to-posts/core';

		require_once $p2p_core . '/util.php';
		require_once $p2p_core . '/api.php';
		require_once $p2p_core . '/autoload.php';

		P2P_Autoload::register( 'P2P_', $p2p_core );

		P2P_Storage::install();
		P2P_Storage::init();

		P2P_Query_Post::init();
		P2P_Query_User::init();

		P2P_URL_Query::init();
	}

	public function setUp() {
		parent::setUp();

	}

	public function tearDown() {
		// your tear down methods here

		// then
		parent::tearDown();
	}

	/**
	 * @test
	 * it should just return the field_id if the p2p plugin is not activated
	 */
	public function it_should_just_return_the_field_id_if_the_p_2_p_plugin_is_not_activated() {
		deactivate_plugins( 'posts-to-posts/posts-to-posts.php' );

		$this->assertEquals( 'foo', cmb2_p2p_pipe( 'foo', '<>', 'post_to_post' ) );
	}

	/**
	 * @test
	 * it should just return the field if the p2p type is not registered
	 */
	public function it_should_just_return_the_field_if_the_p_2_p_type_is_not_registered() {
		$this->assertEquals( 'foo', cmb2_p2p_pipe( 'foo', '<>', 'post_to_post' ) );
	}

	/**
	 * @test
	 * it should relate the post with another post
	 */
	public function it_should_relate_the_post_with_another_post() {
		$from_id = $this->factory->post->create();
		$to_id   = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $to_id );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$this->assertEquals( 1, $wpdb->get_var( "select count(p2p_id) from $wpdb->p2p where p2p_from = $from_id and p2p_to = $to_id and p2p_type = 'post_to_post'" ) );
	}

	/**
	 * @test
	 * it should not write the meta if direction is read and write
	 */
	public function it_should_not_write_the_meta_if_direction_is_read_and_write() {
		$from_id = $this->factory->post->create();
		$to_id   = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $to_id );

		$this->assertEmpty( get_post_meta( $from_id, 'related' ) );
	}

	/**
	 * @test
	 * it should create p2p connection if direction is write only
	 */
	public function it_should_create_p_2_p_connection_if_direction_is_write_only() {
		$from_id = $this->factory->post->create();
		$to_id   = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $to_id );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$this->assertEquals( 1, $wpdb->get_var( "select count(p2p_id) from $wpdb->p2p where p2p_from = $from_id and p2p_to = $to_id and p2p_type = 'post_to_post'" ) );
	}

	/**
	 * @test
	 * it should write meta if direction is not write only
	 */
	public function it_should_write_meta_if_direction_is_not_write_only() {
		$from_id = $this->factory->post->create();
		$to_id   = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $to_id );

		$this->assertEmpty( get_post_meta( $from_id, 'related' ) );
	}

	/**
	 * @test
	 * it should read value from p2p connection if direction id read only
	 */
	public function it_should_read_value_from_p_2_p_connection_if_direction_id_read_only() {
		$from_id = $this->factory->post->create();
		$to_id   = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<', 'post_to_post' ),
				'type' => 'select'
			]
		];

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id );

		$field = new CMB2_Field( $args );

		$this->assertEquals( $to_id, $field->get_data() );
	}

	/**
	 * @test
	 * it should return value from p2p connection if direction is read and write
	 */
	public function it_should_return_value_from_p_2_p_connection_if_direction_is_read_and_write() {
		$from_id = $this->factory->post->create();
		$to_id   = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id );

		$field = new CMB2_Field( $args );

		$this->assertEquals( $to_id, $field->get_data() );
	}

	/**
	 * @test
	 * it should return value from the meta if direction is write only
	 */
	public function it_should_return_value_from_the_meta_if_direction_is_write_only() {
		$from_id = $this->factory->post->create();
		$to_id   = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id );
		update_post_meta( $from_id, 'related', 23 );

		$field = new CMB2_Field( $args );

		$this->assertEquals( 23, $field->get_data() );
	}

	/**
	 * @test
	 * it should return array of values if field is repeatable and reading from p2p connection
	 */
	public function it_should_return_array_of_values_if_field_is_repeatable_and_reading_from_p_2_p_connection() {
		$from_id = $this->factory->post->create();
		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '<', 'post_to_post' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_1 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_2 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_3 );

		$field = new CMB2_Field( $args );

		$this->assertEqualSets( [ $to_id_1, $to_id_2, $to_id_3 ], $field->get_data() );
	}

	/**
	 * @test
	 * it should return array of values if field is repeatable and reading/writing from p2p connection
	 */
	public function it_should_return_array_of_values_if_field_is_repeatable_and_reading_writing_from_p_2_p_connection() {
		$from_id = $this->factory->post->create();
		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '<>', 'post_to_post' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_1 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_2 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_3 );

		$field = new CMB2_Field( $args );

		$this->assertEqualSets( [ $to_id_1, $to_id_2, $to_id_3 ], $field->get_data() );
	}

	/**
	 * @test
	 * it should write multiple p2p connections at once if direction is write and field is repeatable
	 */
	public function it_should_write_multiple_p_2_p_connections_at_once_if_direction_is_write_and_field_is_repeatable() {
		$from_id = $this->factory->post->create();
		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '>', 'post_to_post' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		$field = new CMB2_Field( $args );
		$field->save_field( [ $to_id_1, $to_id_2, $to_id_3 ] );

		/** @var \wpdb $wpdb */
		global $wpdb;

		$related_ids = $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_type = 'post_to_post' and p2p_from = $from_id" );
		$this->assertEqualSets( [ $to_id_1, $to_id_2, $to_id_3 ], $related_ids );
	}

	/**
	 * @test
	 * it should write multiple meta if direction is write only and field is repeatable
	 */
	public function it_should_write_multiple_meta_if_direction_is_write_only_and_field_is_repeatable() {
		$from_id = $this->factory->post->create();
		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '>', 'post_to_post' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( [ $to_id_1, $to_id_2, $to_id_3 ] );

		$related_ids = get_post_meta( $from_id, 'related', true );
		$this->assertEqualSets( [ $to_id_1, $to_id_2, $to_id_3 ], $related_ids );
	}

	/**
	 * @test
	 * it should write multiple p2p connections at once if direction is read and write and field is repeatable
	 */
	public function it_should_write_multiple_p_2_p_connections_at_once_if_direction_is_read_and_write_and_field_is_repeatable() {
		$from_id = $this->factory->post->create();
		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '<>', 'post_to_post' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		$field = new CMB2_Field( $args );
		$field->save_field( [ $to_id_1, $to_id_2, $to_id_3 ] );

		/** @var \wpdb $wpdb */
		global $wpdb;

		$related_ids = $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_type = 'post_to_post' and p2p_from = $from_id" );
		$this->assertEqualSets( [ $to_id_1, $to_id_2, $to_id_3 ], $related_ids );
	}

	/**
	 * @test
	 * it should delete any p2p connection if value is empty and direction is write
	 */
	public function it_should_delete_any_p_2_p_connection_if_value_is_empty_and_direction_is_write() {
		$from_id = $this->factory->post->create();

		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_1 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_2 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_3 );

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );
		$field->save_field( '' );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$related_ids = $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_type = 'post_to_post' and p2p_from = $from_id" );
		$this->assertCount( 0, $related_ids );
	}

	/**
	 * @test
	 * it should delete any p2p connection if direction is read and write
	 */
	public function it_should_delete_any_p_2_p_connection_if_direction_is_read_and_write() {
		$from_id = $this->factory->post->create();

		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_1 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_2 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_3 );

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<>', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );
		$field->save_field( '' );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$related_ids = $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_type = 'post_to_post' and p2p_from = $from_id" );
		$this->assertCount( 0, $related_ids );
	}

	/**
	 * @test
	 * it should not write any p2p connection if direction is read only
	 */
	public function it_should_not_write_any_p_2_p_connection_if_direction_is_read_only() {
		$from_id = $this->factory->post->create();

		$to_id_1 = $this->factory->post->create();
		$to_id_2 = $this->factory->post->create();
		$to_id_3 = $this->factory->post->create();

		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_1 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_2 );
		p2p_type( 'post_to_post' )->connect( $from_id, $to_id_3 );

		$args = [
			'object_id'   => $from_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );
		$field->save_field( '' );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$related_ids = $wpdb->get_col( "select p2p_to from $wpdb->p2p where p2p_type = 'post_to_post' and p2p_from = $from_id" );
		$this->assertEqualSets( [ $to_id_1, $to_id_2, $to_id_3 ], $related_ids );
	}

	/**
	 * @test
	 * it should allow specifying the p2p connection direction
	 */
	public function it_should_allow_specifying_the_p_2_p_connection_direction() {
		$to_id = $this->factory->post->create();

		$from_id_1 = $this->factory->post->create();
		$from_id_2 = $this->factory->post->create();
		$from_id_3 = $this->factory->post->create();

		$args = [
			'object_id'   => $to_id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'p2p relating field', 'cmb2' ),
				'id'   => cmb2_p2p_pipe( 'related', '<>', 'post_to_post', 'to' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );
		$field->save_field( [ $from_id_1, $from_id_2, $from_id_3 ] );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$related_ids = $wpdb->get_col( "select p2p_from from $wpdb->p2p where p2p_type = 'post_to_post' and p2p_to = $to_id" );
		$this->assertEqualSets( [ $from_id_1, $from_id_2, $from_id_3 ], $related_ids );
	}

	/**
	 * @test
	 * it should return connected posts when direction is reading and connection direction is to
	 */
	public function it_should_return_connected_posts_when_direction_is_reading_and_connection_direction_is_to() {
		$to_id = $this->factory->post->create();

		$id_1 = $this->factory->post->create();
		$id_2 = $this->factory->post->create();
		$id_3 = $this->factory->post->create();

		$id_4 = $this->factory->post->create();
		$id_5 = $this->factory->post->create();

		$args = [
			'object_id'   => $to_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '<', 'post_to_post', 'to' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		p2p_type( 'post_to_post' )->connect( $id_1, $to_id );
		p2p_type( 'post_to_post' )->connect( $id_2, $to_id );
		p2p_type( 'post_to_post' )->connect( $id_3, $to_id );

		p2p_type( 'post_to_post' )->connect( $to_id, $id_4 );
		p2p_type( 'post_to_post' )->connect( $to_id, $id_5 );

		$field = new CMB2_Field( $args );

		$value = $field->get_data();

		$this->assertEqualSets( [ $id_1, $id_2, $id_3 ], $value );
	}

	/**
	 * @test
	 * it should return connected posts when direction is read and write and connection direction is to
	 */
	public function it_should_return_connected_posts_when_direction_is_read_and_write_and_connection_direction_is_to() {
		$to_id = $this->factory->post->create();

		$id_1 = $this->factory->post->create();
		$id_2 = $this->factory->post->create();
		$id_3 = $this->factory->post->create();

		$id_4 = $this->factory->post->create();
		$id_5 = $this->factory->post->create();

		$args = [
			'object_id'   => $to_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '<>', 'post_to_post', 'to' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		p2p_type( 'post_to_post' )->connect( $id_1, $to_id );
		p2p_type( 'post_to_post' )->connect( $id_2, $to_id );
		p2p_type( 'post_to_post' )->connect( $id_3, $to_id );

		p2p_type( 'post_to_post' )->connect( $to_id, $id_4 );
		p2p_type( 'post_to_post' )->connect( $to_id, $id_5 );

		$field = new CMB2_Field( $args );

		$value = $field->get_data();

		$this->assertEqualSets( [ $id_1, $id_2, $id_3 ], $value );
	}

	/**
	 * @test
	 * it should write p2p connections when direction is write and connection direction is to
	 */
	public function it_should_write_p_2_p_connections_when_direction_is_write_and_connection_direction_is_to() {
		$post_id = $this->factory->post->create();

		$id_1 = $this->factory->post->create();
		$id_2 = $this->factory->post->create();
		$id_3 = $this->factory->post->create();

		$id_4 = $this->factory->post->create();
		$id_5 = $this->factory->post->create();

		$args = [
			'object_id'   => $post_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '>', 'post_to_post', 'to' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		p2p_type( 'post_to_post' )->connect( $post_id, $id_4 );
		p2p_type( 'post_to_post' )->connect( $post_id, $id_5 );

		$field = new CMB2_Field( $args );

		$value = $field->save_field( array( $id_1, $id_2, $id_3 ) );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$from_this = $wpdb->get_col( "SELECT p2p_to from $wpdb->p2p where p2p_from = $post_id and p2p_type = 'post_to_post'" );
		$to_this   = $wpdb->get_col( "SELECT p2p_from from $wpdb->p2p where p2p_to = $post_id and p2p_type = 'post_to_post'" );

		$this->assertEqualSets( [ $id_4, $id_5 ], $from_this );
		$this->assertEqualSets( [ $id_1, $id_2, $id_3 ], $to_this );
	}

	/**
	 * @test
	 * it should write right p2p connections when direction is read and write and connection direction is to
	 */
	public function it_should_write_right_p_2_p_connections_when_direction_is_read_and_write_and_connection_direction_is_to() {
	$post_id = $this->factory->post->create();

		$id_1 = $this->factory->post->create();
		$id_2 = $this->factory->post->create();
		$id_3 = $this->factory->post->create();

		$id_4 = $this->factory->post->create();
		$id_5 = $this->factory->post->create();

		$args = [
			'object_id'   => $post_id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'p2p relating field', 'cmb2' ),
				'id'         => cmb2_p2p_pipe( 'related', '<>', 'post_to_post', 'to' ),
				'type'       => 'select',
				'repeatable' => true
			]
		];

		p2p_type( 'post_to_post' )->connect( $post_id, $id_4 );
		p2p_type( 'post_to_post' )->connect( $post_id, $id_5 );

		$field = new CMB2_Field( $args );

		$value = $field->save_field( array( $id_1, $id_2, $id_3 ) );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$from_this = $wpdb->get_col( "SELECT p2p_to from $wpdb->p2p where p2p_from = $post_id and p2p_type = 'post_to_post'" );
		$to_this   = $wpdb->get_col( "SELECT p2p_from from $wpdb->p2p where p2p_to = $post_id and p2p_type = 'post_to_post'" );

		$this->assertEqualSets( [ $id_4, $id_5 ], $from_this );
		$this->assertEqualSets( [ $id_1, $id_2, $id_3 ], $to_this );	}
}