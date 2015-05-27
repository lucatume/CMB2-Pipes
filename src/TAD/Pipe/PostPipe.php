<?php


class TAD_Pipe_PostPipe extends TAD_Pipe_AbstractPipe implements TAD_Pipe_PipeInterface, TAD_Pipe_InstanceableInterface {

	public static function instance() {
		return new self;
	}

	public function save( $override, array $args, $field_args, CMB2_Field $field ) {
		if ( $this->direction_is_read() ) {
			return $override;
		}
		remove_filter( "cmb2_override_{$this->field_id}_meta_save", array( $this, 'save' ) );

		$value = TAD_Pipe_PostFields::format_and_sanitize( $this->target, $args['value'] );

		$value = $this->maybe_apply_write_filter( $value );

		if ( $value !== TAD_Pipe_PostFields::INVALID ) {
			wp_update_post( array( 'ID' => $args['id'], $this->target => $value ) );
		}

		// do override if sync, let run if write
		return $this->direction_is_write() ? $override : true;
	}

	public function value( $override, $object_id, array $args, CMB2_Field $field ) {
		if ( $this->direction_is_write() ) {
			return $override;
		}

		$post = get_post( $args['id'], null, 'display' );

		$value = $post->{$this->target};

		return $this->maybe_apply_read_filter( $value );
	}

	public function remove( $override, array $args, array $field_args, CMB2_Field $field ) {
		if ( $this->direction_is_read() ) {
			return $override;
		}

		remove_filter( "cmb2_override_{$this->field_id}_meta_remove", array( $this, 'remove' ) );
		wp_update_post( array( 'ID' => $args['id'], $this->target => null ) );

		// do override
		return true;
	}

}