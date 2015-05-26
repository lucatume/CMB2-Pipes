<?php


class TAD_Pipe_P2PPipe extends TAD_Pipe_AbstractPipe implements TAD_Pipe_PipeInterface, TAD_Pipe_InstanceableInterface {

	public static function instance() {
		return new self;
	}

	public function save( $override, array $args, $field_args, CMB2_Field $field ) {
		Arg::_( $args['value'], 'Value' )->is_array()->_or()->is_numeric();

		if ( $this->direction == '<' ) {
			return;
		}

		$value = is_array( $args['value'] ) ? $args['value'] : array( $args['value'] );

		$p2p_type = p2p_type( $this->target );

		if ( empty( $p2p_type ) ) {
			return;
		}

		foreach ( $value as $id ) {
			$p2p_type->connect( $args['id'], $id );
		}

		// do override
		return true;
	}

	public function value( $override, $object_id, array $args, CMB2_Field $field ) {
		// TODO: Implement value() method.
	}

	public function remove( $override, array $args, array $field_args, CMB2_Field $field ) {
		// TODO: Implement remove() method.
	}
}