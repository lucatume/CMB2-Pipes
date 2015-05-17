<?php


class TAD_Pipe_PostPipe extends TAD_Pipe_AbstractPipe implements TAD_Pipe_PipeInterface {

	public static function instance() {
		return new self;
	}

	public function save( $override, array $args, $field_args, CMB2_Field $field ) {
		if ( ! in_array( $this->direction, array( '>', '<>' ) ) ) {
			return;
		}

		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET {$this->target} = %s WHERE ID = {$args['id']}", $args['value'] ) );

		// do override
		return true;
	}

	public function value( $override, $object_id, array $args, CMB2_Field $field ) {
		if ( ! in_array( $this->direction, array( '<', '<>' ) ) ) {
			return;
		}

		global $wpdb;
		$value = $wpdb->get_var( "SELECT p.{$this->target} FROM $wpdb->posts p WHERE ID = {$args['id']}" );

		return empty( $value ) ? '' : $value;
	}

	public function remove( $override, array $args, array $field_args, CMB2_Field $field ) {
		if ( ! in_array( $this->direction, array( '>', '<>' ) ) ) {
			return;
		}

		global $wpdb;
		$wpdb->query( "UPDATE $wpdb->posts SET {$this->target} = DEFAULT WHERE ID = {$args['id']}" );

		// do override
		return true;
	}

}