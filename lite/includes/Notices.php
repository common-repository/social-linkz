<?php

namespace KaizenCoders\SocialLinkz;

class Notices {
	/**
	 * Show Notice
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $status
	 * @param  bool  $is_dismissible
	 *
	 * @param  string  $message
	 */
	public function show_notice( $message = '', $status = '', $is_dismissible = true ) {

		$class = 'notice notice-success';
		if ( 'error' === $status ) {
			$class = 'notice notice-error';
		}

		if ( $is_dismissible ) {
			$class .= ' is-dismissible';
		}

		echo "<div class='{$class}'><p>{$message}</p></div>";
	}

	/**
	 * Success Message
	 *
	 * @since 1.0.0
	 *
	 * @param  bool  $is_dismisible
	 *
	 * @param  string  $message
	 */
	public function success( $message = '', $is_dismisible = true ) {
		$this->show_notice( $message, 'success', $is_dismisible );
	}

	/**
	 * Error Message
	 *
	 * @since 1.0.0
	 *
	 * @param  bool  $is_dismisible
	 *
	 * @param  string  $message
	 */
	public function error( $message = '', $is_dismisible = true ) {
		$this->show_notice( $message, 'error', $is_dismisible );
	}

	/**
	 * Warning Message
	 *
	 * @since 1.0.0
	 *
	 * @param  bool  $is_dismisible
	 *
	 * @param  string  $message
	 */
	public function warning( $message = '', $is_dismisible = true ) {
		$this->show_notice( $message, 'warning', $is_dismisible );
	}

	/**
	 * Info Message
	 *
	 * @since 1.0.0
	 *
	 * @param  bool  $is_dismisible
	 *
	 * @param  string  $message
	 */
	public function info( $message = '', $is_dismisible = true ) {
		$this->show_notice( $message, 'info', $is_dismisible );
	}

}
