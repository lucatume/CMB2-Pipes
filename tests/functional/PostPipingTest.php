<?php


class PostPipingTest extends \WP_UnitTestCase {

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
	 * it should override the meta value when direction reads from post field
	 */
	public function it_should_override_the_meta_value_when_direction_reads_from_post_field() {
		$id       = $this->factory->post->create( [ 'post_title' => 'From posts table' ] );
		$field_id = 'the_post_title';
		update_post_meta( $id, $field_id, 'From meta table' );
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'The post date', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '<', 'post_title' ),
				'type' => 'text',
			]
		];
		$field = new CMB2_Field( $args );

		$value = $field->get_data( $field_id );

		$this->assertEquals( 'From posts table', $value );
	}
}