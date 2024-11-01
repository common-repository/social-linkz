<?php

namespace KaizenCoders\SocialLinkz;

/**
 * Class Cache
 *
 * @since 1.0.0
 */
class Cache {

	/**
	 * Cache version
	 *
	 * @since 1.0.0
	 * @var string
	 *
	 */
	static $version = '1.0.0';

	static $prefix = 'kc_sl_';

	/** @var bool */
	static $enabled = true;

	/**
	 * Get prefix
	 *
	 * @return string
	 */
	static function get_prefix() {
		return self::$prefix . str_replace( '.', '', self::$version ) . '_';
	}

	/**
	 * @return mixed|void
	 */
	static function get_default_transient_expiration() {
		return apply_filters( 'cache_default_expiration', 10 );
	}

	/**
	 * @param $key
	 * @param $value
	 * @param  bool  $expiration
	 *
	 * @return bool
	 */
	static function set_transient( $key, $value, $expiration = false ) {
		if ( ! self::$enabled ) {
			return false;
		}
		if ( ! $expiration ) {
			$expiration = self::get_default_transient_expiration();
		}

		return set_transient( self::get_prefix() . 'cache_' . $key, $value, $expiration );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param  string  $key
	 *
	 * @return bool|mixed
	 *
	 */
	static function get_transient( $key ) {
		if ( ! self::$enabled ) {
			return false;
		}

		return get_transient( self::get_prefix() . 'cache_' . $key );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $key
	 *
	 */
	static function delete_transient( $key ) {
		delete_transient( self::get_prefix() . 'cache_' . $key );
	}

	/**
	 * Only sets if key is not falsy
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed  $value
	 * @param  string  $group
	 *
	 * @param  string  $key
	 */
	static function set( $key, $value, $group ) {
		if ( ! $key ) {
			return;
		}

		wp_cache_set( (string) $key, $value, self::get_prefix() . $group );
	}

	/**
	 * Only gets if key is not falsy
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $group
	 *
	 * @param  string  $key
	 *
	 * @return bool|mixed
	 *
	 */
	static function get( $key, $group ) {
		if ( ! $key ) {
			return false;
		}

		return wp_cache_get( (string) $key, self::get_prefix() . $group );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param  string  $group
	 *
	 * @param  string  $key
	 *
	 * @return bool
	 *
	 */
	static function exists( $key, $group ) {
		if ( ! $key ) {
			return false;
		}
		$found = false;
		wp_cache_get( (string) $key, self::get_prefix() . $group, false, $found );

		return $found;
	}


	/**
	 * Only deletes if key is not falsy
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $group
	 *
	 * @param  string  $key
	 */
	static function delete( $key, $group ) {
		if ( ! $key ) {
			return;
		}

		wp_cache_delete( (string) $key, self::get_prefix() . $group );
	}

}