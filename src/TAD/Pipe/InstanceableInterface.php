<?php


interface TAD_Pipe_InstanceableInterface {

	/**
	 * @return TAD_Pipe_InstanceableInterface A ready to run instance of the class.
	 */
	public static function instance();
}