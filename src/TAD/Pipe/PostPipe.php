<?php


class TAD_Pipe_PostPipe extends TAD_Pipe_AbstractPipe implements TAD_Pipe_PipeInterface {


	public static function instance() {
		return new self;
	}

	public function save( $override, array $args, $field_args, CMB2_Field $field ) {
		if ( in_array( $this->direction, array( '>', '<>' ) ) ) {
			remove_filter( "cmb2_override_{$this->field_id}_meta_save", array( $this, 'save' ) );

			$value = TAD_Pipe_PostFields::format_and_sanitize( $this->target, $args['value'] );
			if ( $value !== TAD_Pipe_PostFields::INVALID ) {
				wp_update_post( array( 'ID' => $args['id'], $this->target => $value ) );
			}
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

		remove_filter( "cmb2_override_{$this->field_id}_meta_remove", array( $this, 'remove' ) );
		wp_update_post( array( 'ID' => $args['id'], $this->target => null ) );

		// do override
		return true;
	}

}