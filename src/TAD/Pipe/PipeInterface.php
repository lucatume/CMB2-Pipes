<?php


interface TAD_Pipe_PipeInterface {

	public function set_field_id( $field_id );

	public function set_direction( $direction );

	public function set_target( $target );

	public function save( $override, array $args, $field_args, CMB2_Field $field );

	public function value( $override, $object_id, array $args, CMB2_Field $field );

	public function remove( $override, array $args, array $field_args, CMB2_Field $field );
}