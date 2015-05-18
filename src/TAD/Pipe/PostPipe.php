<?php


class TAD_Pipe_PostPipe extends TAD_Pipe_AbstractPipe implements TAD_Pipe_PipeInterface {

	public static function instance() {
		return new self;
	}

	public function save( $override, array $args, $field_args, CMB2_Field $field ) {
		if ( in_array( $this->direction, array( '>', '<>' ) ) ) {
			remove_filter( "cmb2_override_{$this->field_id}_meta_save", array( $this, 'save' ) );
			wp_update_post( array( 'ID' => $args['id'], $this->target => $args['value'] ) );
		}

		// do override if sync, let run if write
		return $this->direction == '>' ? $override : true;
	}

	public function value( $override, $object_id, array $args, CMB2_Field $field ) {
		if ( ! in_array( $this->direction, array( '<', '<>' ) ) ) {
			return $override;
		}

		$post = get_post( $args['id'], null, 'display' );

		return $post->{$this->target};
	}

	public function remove( $override, array $args, array $field_args, CMB2_Field $field ) {
		if ( ! in_array( $this->direction, array( '>', '<>' ) ) ) {
			return $override;
		}

		global $wpdb;
		$wpdb->query( "UPDATE $wpdb->posts SET {$this->target} = DEFAULT WHERE ID = {$args['id']}" );

		// do override
		return true;
	}

}