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

	public function datePostFieldsInput() {
		$timestamp      = time();
		$formatted_time = ( new DateTime() )->setTimestamp( $timestamp )->format( 'Y-m-d H:i:s' );
		$format1Date    = ( new DateTime() )->setTimestamp( $timestamp )->format( 'm/d/y' );
		$format2Date    = ( new DateTime() )->setTimestamp( $timestamp )->format( 'm-d-y' );

		return [
			[ 'post_date', $timestamp, $formatted_time ],
			[ 'post_date_gmt', $timestamp, $formatted_time ],
			[ 'post_modified', $timestamp, $formatted_time ],
			[ 'post_modified_gmt', $timestamp, $formatted_time ],
			[ 'post_date', $format1Date, $formatted_time ],
			[ 'post_date_gmt', $format1Date, $formatted_time ],
			[ 'post_modified', $format1Date, $formatted_time ],
			[ 'post_modified_gmt', $format1Date, $formatted_time ],
		];
	}

	/**
	 * @test
	 * it should format dates modifying a date post field
	 * @dataProvider datePostFieldsInput
	 */
	public function it_should_format_dates_modifying_a_date_post_field( $target_field, $in, $out ) {
		$id       = $this->factory->post->create();
		$field_id = 'a_field';
		$args     = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '<>', $target_field ),
				'type' => 'text'
			]
		];
		$field    = new CMB2_Field( $args );

		$field->save_field( $in );

		$post     = get_post( $id );
		$expected = DateTime::createFromFormat( 'Y-m-d H:i:s', $out )->getTimestamp();
		$stored   = DateTime::createFromFormat( 'Y-m-d H:i:s', $post->{$target_field} )->getTimestamp();
		// might take some time, let's give it a 24h delta to cope with timezones
		// I'm really testing the format here
		$this->assertEquals( $expected, $stored, '', 86400 );
	}

	/**
	 * @test
	 * it should not allow repeatable fields to write to post fields
	 */
	public function it_should_not_allow_repeatable_fields_to_write_to_post_fields() {
		$id       = $this->factory->post->create( [ 'post_title' => 'Original title' ] );
		$field_id = 'a_field';
		$args     = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'A post field', 'cmb2' ),
				'id'         => cmb2_pipe( $field_id, '<>', 'post_title' ),
				'type'       => 'text',
				'repeatable' => true
			]
		];
		$value    = [ 'First title', 'Second title' ];
		$field    = new CMB2_Field( $args );

		$this->setExpectedException( 'InvalidArgumentException' );

		$field->save_field( $value );
	}

	/**
	 * @test
	 * it should throw when trying to write to non existing post field
	 */
	public function it_should_throw_when_trying_to_write_to_non_existing_post_field() {
		$id       = $this->factory->post->create( [ 'post_title' => 'Original title' ] );
		$field_id = 'a_field';

		$this->setExpectedException( 'InvalidArgumentException' );

		$args = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name' => __( 'A post field', 'cmb2' ),
				'id'   => cmb2_pipe( $field_id, '<>', 'non_existing_field' ),
				'type' => 'text'
			]
		];
	}

	public function fieldDefaults() {
		$defaults = array(
			'post_status'           => 'draft',
			'post_type'             => 'post',
			'post_author'           => get_current_user_id(),
			'ping_status'           => get_option( 'default_ping_status' ),
			'post_parent'           => 0,
			'menu_order'            => 0,
			'to_ping'               => '',
			'pinged'                => '',
			'post_password'         => '',
			'post_content_filtered' => '',
			'post_excerpt'          => '',
			'post_content'          => '',
			'post_title'            => ''
		);

		return array_map( function ( $key, $value ) {
			return [ $key, $value ];
		}, array_keys( $defaults ), $defaults );
	}

	/**
	 * @test
	 * it should set the post field to default value when removing field
	 * @dataProvider fieldDefaults
	 */
	public function it_should_set_the_post_field_to_default_value_when_removing_field( $post_field, $default ) {
		$id    = $this->factory->post->create();
		$args  = [
			'object_id'   => $id,
			'object_type' => 'post',
			'field_args'  => [
				'name'       => __( 'A post field', 'cmb2' ),
				'id'         => cmb2_pipe( 'a_field', '>', $post_field ),
				'type'       => 'text',
				'repeatable' => true
			]
		];
		$field = new CMB2_Field( $args );

		$field->save_field( '' );

		$post = get_post( $id );
		$this->assertEquals( $default, $post->{$post_field} );
	}
}