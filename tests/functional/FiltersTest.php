<?php


class FiltersTest extends \WP_UnitTestCase {

	protected $backupGlobals = false;

	public function setUp() {
		// before
		parent::setUp();

		// your set up methods here
	}

	public function tearDown() {
		// your tear down methods here

		// then
		parent::tearDown();
	}

	/**
	 * @test
	 * it should apply write filters when direction is write
	 */
	public function it_should_apply_write_filters_when_direction_is_write() {
		$id    = $this->factory->post->create();
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( 'a_field', array( '>' => 'ucwords' ), 'post_title' ),
				'type' => 'text'
			]
		];
		$field = new CMB2_Field( $args );

		$field->save_field( 'lorem ipsum dolor' );

		$post = get_post( $id );
		$this->assertEquals( 'Lorem Ipsum Dolor', $post->post_title );
	}

	/**
	 * @test
	 * it should apply write filters when direction is read and write
	 */
	public function it_should_apply_write_filters_when_direction_is_read_and_write() {
		$id    = $this->factory->post->create();
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( 'a_field', array( '<' => '', '>' => 'ucwords' ), 'post_title' ),
				'type' => 'text'
			]
		];
		$field = new CMB2_Field( $args );

		$field->save_field( 'lorem ipsum dolor' );

		$post = get_post( $id );
		$this->assertEquals( 'Lorem Ipsum Dolor', $post->post_title );
	}

	/**
	 * @test
	 * it should apply callback write filters
	 */
	public function it_should_apply_callback_write_filters() {
		$id      = $this->factory->post->create();
		$closure = function ( $value ) {
			return str_replace( ' ', '_', $value );
		};
		$args    = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( 'a_field', array( '>' => $closure ), 'post_title' ),
				'type' => 'text'
			]
		];
		$field   = new CMB2_Field( $args );

		$field->save_field( 'lorem ipsum dolor' );

		$post = get_post( $id );
		$this->assertEquals( 'lorem_ipsum_dolor', $post->post_title );
	}

	/**
	 * @test
	 * it should apply read filters when direction is read
	 */
	public function it_should_apply_read_filters_when_direction_is_read() {
		$id    = $this->factory->post->create( array( 'post_title' => 'lorem ipsum dolor' ) );
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( 'a_field', array( '<' => 'ucwords' ), 'post_title' ),
				'type' => 'text'
			]
		];
		$field = new CMB2_Field( $args );

		$this->assertEquals( 'Lorem Ipsum Dolor', $field->get_data() );
	}

	/**
	 * @test
	 * it should apply read filters when direction is read and write
	 */
	public function it_should_apply_read_filters_when_direction_is_read_and_write() {
		$id    = $this->factory->post->create( array( 'post_title' => 'lorem ipsum dolor' ) );
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( 'a_field', array( '<' => 'ucwords', '>' => '' ), 'post_title' ),
				'type' => 'text'
			]
		];
		$field = new CMB2_Field( $args );

		$this->assertEquals( 'Lorem Ipsum Dolor', $field->get_data() );
	}

	/**
	 * @test
	 * it should allow for callback read filters
	 */
	public function it_should_allow_for_callback_read_filters() {
		$id      = $this->factory->post->create( array( 'post_title' => 'lorem ipsum dolor' ) );
		$closure = function ( $value ) {
			return str_replace( ' ', '_', $value );
		};
		$args    = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( 'a_field', array( '<' => $closure, '>' => '' ), 'post_title' ),
				'type' => 'text'
			]
		];
		$field   = new CMB2_Field( $args );

		$this->assertEquals( 'lorem_ipsum_dolor', $field->get_data() );
	}

	/**
	 * @test
	 * it should allow non filtering direction to be specified as just key
	 */
	public function it_should_allow_non_filtering_direction_to_be_specified_as_just_key() {
		$id    = $this->factory->post->create( [ 'post_title' => 'Some' ] );
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( 'a_field', array( '<', '>' => 'ucwords' ), 'post_title' ),
				'type' => 'text'
			]
		];
		$field = new CMB2_Field( $args );

		$this->assertEquals( 'Some', $field->get_data() );
	}
}