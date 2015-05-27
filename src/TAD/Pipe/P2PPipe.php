<?php


class TAD_Pipe_P2PPipe extends TAD_Pipe_AbstractPipe implements TAD_Pipe_PipeInterface, TAD_Pipe_InstanceableInterface {

	public static function instance() {
		return new self;
	}

	public function save( $override, array $args, $field_args, CMB2_Field $field ) {
		Arg::_( $args['value'], 'Value' )->is_array()->_or()->is_numeric();

		if ( $this->direction_is_read() ) {
			return;
		}

		$value = is_array( $args['value'] ) ? $args['value'] : array( $args['value'] );

		$p2p_type = p2p_type( $this->target );

		if ( empty( $p2p_type ) ) {
			return;
		}

		$connection_direction = $this->get_connection_direction();

		if ( $connection_direction == 'from' ) {
			foreach ( $value as $id ) {
				$p2p_type->connect( $args['id'], $id );
			}
		} else {
			foreach ( $value as $id ) {
				$p2p_type->connect( $id, $args['id'] );
			}
		}

		return $this->direction_is_write() ? $override : true;
	}

	public function value( $override, $object_id, array $args, CMB2_Field $field ) {
		if ( $this->direction_is_write() ) {
			return $override;
		}

		$related = get_posts( array(
			'fields'              => 'ids',
			'nopaging'            => true,
			'suppress_filters'    => false,
			'connected_type'      => $this->target,
			'connected_items'     => $object_id,
			'connected_direction' => $this->get_connection_direction()
		) );

		return $args['repeat'] ? $related : reset( $related );
	}

	public function remove( $override, array $args, array $field_args, CMB2_Field $field ) {
		if ( $this->direction_is_read() ) {
			return;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		p2p_delete_connections( $this->target, array( 'connected_items' => $args['id'] ) );
	}

	/**
	 * @return string
	 */
	private function get_connection_direction() {
		$connection_direction = empty( $this->connection_direction ) ? 'from' : $this->connection_direction;

		return $connection_direction;
	}

}