<?php
/**
 * CMB2 Pipes support functions
 */

if ( ! function_exists( 'cmb2_pipe' ) ) {
	function cmb2_pipe( $field_id, $direction, $target ) {
		return TAD_Pipe_Piper::pipe( $field_id, $direction, $target );
	}

}
