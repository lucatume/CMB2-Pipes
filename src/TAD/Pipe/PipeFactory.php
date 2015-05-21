<?php


class TAD_Pipe_PipeFactory {

	/**
	 * @var static
	 */
	protected static $instance;

	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function make( $target ) {
		$legit_targets = TAD_Pipe_Piper::get_legit_pipe_targets();
		Arg::_( $target, 'Pipe target' )->is_string()->in( array_keys( $legit_targets ) );

		$type = $legit_targets[ $target ];
		// `some-type` to `TAD_Pipe_SomeTypePipe`
		$type       = str_replace( ' ', '_', ucwords( implode( ' ', preg_split( '/[-_\s]+/', $type ) ) ) );
		$class_name = 'TAD_Pipe_' . $type . 'Pipe';
		if ( class_exists( $class_name ) ) {
			return call_user_func( array( $class_name, 'instance' ) );
		}

		return false;
	}

}