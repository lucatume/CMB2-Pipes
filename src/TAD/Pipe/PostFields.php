<?php


class TAD_Pipe_PostFields {

	const INVALID = 'invalid';

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * @var bool Whether the class should throw exceptions or not.
	 */
	protected $should_throw;

	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function date( $value ) {
		$timestamp = is_timestamp( $value ) ? $value : strtotime( $value );

		return date( 'Y-m-d H:i:s', $timestamp );
	}

	public static function format_and_sanitize( $field, $value ) {

		Arg::_( $value, 'Value' )->not()->is_array();

		$types = self::instance()->get_field_types();

		Arg::_( $field, 'Post field' )->in( array_keys( $types ) );

		$value = empty( $types[ $field ] ) ? $value : call_user_func( array(
			self::instance(),
			$types[ $field ]
		), $value );

		return $value;
	}

	private function get_field_types() {
		return array(
			'post_author'           => false,
			'post_date'             => 'date',
			'post_date_gmt'         => 'date',
			'post_content'          => false,
			'post_title'            => false,
			'post_excerpt'          => false,
			'post_status'           => false,
			'comment_status'        => false,
			'ping_status'           => false,
			'post_password'         => false,
			'post_name'             => false,
			'to_ping'               => false,
			'pinged'                => false,
			'post_modified'         => 'date',
			'post_modified_gmt'     => 'date',
			'post_content_filtered' => false,
			'post_parent'           => false,
			'guid'                  => false,
			'menu_order'            => false,
			'post_type'             => false,
			'post_mime_type'        => false,
			'comment_count'         => false
		);
	}

	public function should_throw( $bool ) {
		$this->should_throw = (bool) $bool;
	}
}