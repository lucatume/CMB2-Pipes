<?php


class PostPipingTest extends \WP_UnitTestCase {

	protected $backupGlobals = false;

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
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '<', 'post_title' ),
				'type' => 'text',
			]
		];
		$field = new CMB2_Field( $args );

		$value = $field->get_data( $field_id );

		$this->assertEquals( 'From posts table', $value );
	}

	/**
	 * @test
	 * it should override the meta value when field read and writes to post field
	 */
	public function it_should_override_the_meta_value_when_field_read_and_writes_to_post_field() {
		$id       = $this->factory->post->create( [ 'post_title' => 'From posts table' ] );
		$field_id = 'the_post_title';
		update_post_meta( $id, $field_id, 'From meta table' );
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '<>', 'post_title' ),
				'type' => 'text',
			]
		];
		$field = new CMB2_Field( $args );

		$value = $field->get_data( $field_id );

		$this->assertEquals( 'From posts table', $value );
	}

	/**
	 * @test
	 * it should write to post field when writing to post field
	 */
	public function it_should_write_to_post_field_when_writing_to_post_field() {
		$id       = $this->factory->post->create();
		$field_id = 'the_post_title';
		$args     = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '>', 'post_title' ),
				'type' => 'text',
			]
		];
		$field    = new CMB2_Field( $args );

		$field->save_field( 'Lorem ipsum dolor' );

		$post = get_post( $id );
		$this->assertEquals( 'Lorem ipsum dolor', $post->post_title );
	}

	/**
	 * @test
	 * it should write the meta value when writing to post field
	 */
	public function it_should_write_the_meta_value_when_writing_to_post_field() {
		$id       = $this->factory->post->create();
		$field_id = 'the_post_title';
		$args     = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '>', 'post_title' ),
				'type' => 'text',
			]
		];
		$field    = new CMB2_Field( $args );

		$field->save_field( 'Lorem ipsum dolor' );

		$this->assertEquals( 'Lorem ipsum dolor', get_post_meta( $id, $field_id, true ) );
	}

	/**
	 * @test
	 * it should write the post field when reading and writing to/from the post field
	 */
	public function it_should_write_the_post_field_when_reading_and_writing_to_from_the_post_field() {
		$id       = $this->factory->post->create();
		$field_id = 'the_post_title';
		$args     = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '<>', 'post_title' ),
				'type' => 'text',
			]
		];
		$field    = new CMB2_Field( $args );

		$field->save_field( 'Lorem ipsum dolor' );

		$post = get_post( $id );
		$this->assertEquals( 'Lorem ipsum dolor', $post->post_title );
	}

	/**
	 * @test
	 * it should not write any meta when reading and writing to/from the post field
	 */
	public function it_should_not_write_any_meta_when_reading_and_writing_to_from_the_post_field() {
		$id       = $this->factory->post->create();
		$field_id = 'the_post_title';
		update_post_meta( $id, $field_id, 'Not overwritten' );
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '<>', 'post_title' ),
				'type' => 'text',
			]
		];
		$field = new CMB2_Field( $args );

		$field->save_field( 'Lorem ipsum dolor' );

		$this->assertEquals( 'Not overwritten', get_post_meta( $id, $field_id, true ) );
	}
}