<?php


class TAD_Pipe_AbstractPipe {

	/**
	 * @var string
	 */
	protected $field_id;

	/**
	 * @var string
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
		Arg::_( $direction, 'Pipe direction' )->is_string()->in( '>', '<', '<>' );
		$this->direction = $direction;
	}

	public function set_target( $target ) {
		$legit_targets = TAD_Pipe_Piper::get_legit_pipe_targets();
		Arg::_( $target, 'Pipe target' )->is_string()->in( array_keys( $legit_targets ) );
		$this->target = $target;
	}
}