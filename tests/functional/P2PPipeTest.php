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

		$this->assertEquals( 'foo', cmb2_p2p_pipe( 'foo', 'p2p_type' ) );
	}

	/**
	 * @test
	 * it should just return the field if the p2p type is not registered
	 */
	public function it_should_just_return_the_field_if_the_p_2_p_type_is_not_registered() {
		$this->assertEquals( 'foo', cmb2_p2p_pipe( 'foo', 'p2p_type' ) );
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
				'id'   => cmb2_p2p_pipe( 'related', 'post_to_post' ),
				'type' => 'select'
			]
		];

		$field = new CMB2_Field( $args );

		$field->save_field( $to_id );

		/** @var \wpdb $wpdb */
		global $wpdb;
		$this->assertEquals( 1, $wpdb->get_var( "select count(p2p_id) from $wpdb->p2p where p2p_from = $from_id and p2p_to = $to_id and p2p_type = 'post_to_post'" ) );
	}
}