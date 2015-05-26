<?php


class TAD_Pipe_PostFields {

	const INVALID = 'invalid';

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

	protected function date( $value ) {
		$timestamp = is_timestamp( $value ) ? $value : strtotime( $value );

		return date( 'Y-m-d H:i:s', $timestamp );
	}

	public static function format_and_sanitize( $field, $value ) {
		if ( is_array( $value ) ) {
			return self::INVALID;
		}

		$types = self::instance()->get_field_types();

		if ( ! array_key_exists( $field, $types ) ) {
			return self::INVALID;
		}

		try {
			$value = empty( $types[ $field ] ) ? $value : call_user_func( array(
				self::instance(),
				$types[ $field ]
			), $value );
		} catch ( Exception $e ) {
			return self::INVALID;
		}

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
}