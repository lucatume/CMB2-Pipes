<?php


class TAD_Pipe_AbstractPipe implements TAD_Pipe_SettablePropertiesInterface {

	/**
	 * @var string
	 */
	protected $field_id;

	/**
	 * @var array
	 */
	protected $direction;

	/**
	 * @var string
	 */
	protected $target;

	public function set_field_id( $field_id ) {
		Arg::_( $field_id, 'Field ID' )->is_string();
		$this->field_id = $field_id;
	}

	public function set_direction( $direction ) {
		if ( $direction == '<>' ) {
			$direction = array( '<' => '', '>' => '' );
		}
		$this->direction = is_array( $direction ) ? $direction : array( $direction => '' );
	}

	public function set_target( $target ) {
		$legit_targets = TAD_Pipe_Piper::get_legit_pipe_targets();
		Arg::_( $target, 'Pipe target' )->is_string();
		$this->target = $target;
	}

	public function set( $property, $value ) {
		$this->{$property} = $value;
	}

	protected function direction_is_read() {
		return in_array( '<', array_keys( $this->direction ) );
	}

	protected function direction_is_write() {
		return in_array( '>', array_keys( $this->direction ) );
	}

	protected function direction_is_read_and_write() {
		return in_array( '<', array_keys( $this->direction ) ) && in_array( '>', array_keys( $this->direction ) );
	}

	protected function has_write_filter() {
		return array_key_exists( '>', $this->direction ) && ! empty( $this->direction['>'] );
	}

	protected function has_read_filter() {
		return array_key_exists( '<', $this->direction ) && ! empty( $this->direction['<'] );
	}

	protected function get_write_filter() {
		return $this->direction['>'];
	}

	protected function get_read_filter() {
		return $this->direction['<'];
	}

	protected function maybe_apply_write_filter( $value ) {
		if ( $this->has_write_filter() ) {
			$filter = $this->get_write_filter();
			$value  = call_user_func( $filter, $value );

			return $value;
		}

		return $value;
	}

	protected function maybe_apply_read_filter( $value ) {
		if ( $this->has_read_filter() ) {
			$filter = $this->get_read_filter();
			$value  = call_user_func( $filter, $value );

			return $value;
		}

		return $value;
	}
}