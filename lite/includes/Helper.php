<?php

namespace KaizenCoders\SocialLinkz;

/**
 * Plugin_Name
 *
 * @link      https://kaizencoders.com
 * @author    KaizenCoders <hello@kaizencoders.com>
 * @package   Social Linkz
 */

/**
 * Helper Class
 */
class Helper {

	/**
	 * Whether given user is an administrator.
	 *
	 * @param  \WP_User  $user  The given user.
	 *
	 * @return bool
	 */
	public static function is_user_admin( \WP_User $user = null ) {
		if ( is_null( $user ) ) {
			$user = wp_get_current_user();
		}

		if ( ! $user instanceof WP_User ) {
			_doing_it_wrong( __METHOD__, 'To check if the user is admin is required a WP_User object.', '1.0.0' );
		}

		return is_multisite() ? user_can( $user, 'manage_network' ) : user_can( $user, 'manage_options' );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string  $type  admin, ajax, cron, cli or frontend.
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public function request( $type ) {
		switch ( $type ) {
			case 'admin_backend':
				return $this->is_admin_backend();
			case 'ajax':
				return $this->is_ajax();
			case 'installing_wp':
				return $this->is_installing_wp();
			case 'rest':
				return $this->is_rest();
			case 'cron':
				return $this->is_cron();
			case 'frontend':
				return $this->is_frontend();
			case 'cli':
				return $this->is_cli();
			default:
				_doing_it_wrong( __METHOD__, esc_html( sprintf( 'Unknown request type: %s', $type ) ), '1.0.0' );

				return false;
		}
	}

	/**
	 * Is installing WP
	 *
	 * @return boolean
	 */
	public function is_installing_wp() {
		return defined( 'WP_INSTALLING' );
	}

	/**
	 * Is admin
	 *
	 * @return boolean
	 * @since 1.0.0
	 *
	 */
	public function is_admin_backend() {
		return is_user_logged_in() && is_admin();
	}

	/**
	 * Is ajax
	 *
	 * @return boolean
	 * @since 1.0.0
	 *
	 */
	public function is_ajax() {
		return ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || defined( 'DOING_AJAX' );
	}

	/**
	 * Is rest
	 *
	 * @return boolean
	 * @since 1.0.0
	 *
	 */
	public function is_rest() {
		return defined( 'REST_REQUEST' );
	}

	/**
	 * Is cron
	 *
	 * @return boolean
	 * @since 1.0.0
	 *
	 */
	public function is_cron() {
		return ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) || defined( 'DOING_CRON' );
	}

	/**
	 * Is frontend
	 *
	 * @return boolean
	 * @since 1.0.0
	 *
	 */
	public function is_frontend() {
		return ( ! $this->is_admin_backend() || ! $this->is_ajax() ) && ! $this->is_cron() && ! $this->is_rest();
	}

	/**
	 * Is cli
	 *
	 * @return boolean
	 * @since 1.0.0
	 *
	 */
	public function is_cli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Define constant
	 *
	 * @param $value
	 *
	 * @param $name
	 *
	 * @since 1.0.0
	 *
	 */
	public static function maybe_define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Get current date time
	 *
	 * @return false|string
	 */
	public static function get_current_date_time() {
		return gmdate( 'Y-m-d H:i:s' );
	}


	/**
	 * Get current date time
	 *
	 * @return false|string
	 *
	 */
	public static function get_current_gmt_timestamp() {
		return strtotime( gmdate( 'Y-m-d H:i:s' ) );
	}

	/**
	 * Get current date
	 *
	 * @return false|string
	 *
	 */
	public static function get_current_date() {
		return gmdate( 'Y-m-d' );
	}

	/**
	 * Format date time
	 *
	 * @param $date
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function format_date_time( $date ) {
		$convert_date_format = get_option( 'date_format' );
		$convert_time_format = get_option( 'time_format' );

		$local_timestamp = ( $date !== '0000-00-00 00:00:00' ) ? date_i18n( "$convert_date_format $convert_time_format",
			strtotime( get_date_from_gmt( $date ) ) ) : '<i class="dashicons dashicons-es dashicons-minus"></i>';

		return $local_timestamp;
	}

	/**
	 * Clean String or array using sanitize_text_field
	 *
	 * @param $variable Data to sanitize
	 *
	 * @return array|string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'clean', $var );
		} else {
			return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		}
	}

	/**
	 * Get IP
	 *
	 * @return mixed|string|void
	 *
	 * @since 1.0.0
	 */
	public static function get_ip() {

		// Get real visitor IP behind CloudFlare network
		if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ip = $_SERVER['HTTP_FORWARDED'];
		} else {
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'UNKNOWN';
		}

		return $ip;
	}

	/**
	 * Get GMT Offset
	 *
	 * @param  null  $timestamp
	 *
	 * @param  bool  $in_seconds
	 *
	 * @return float|int
	 *
	 * @since 1.0.0
	 *
	 */
	public static function get_gmt_offset( $in_seconds = false, $timestamp = null ) {

		$offset = get_option( 'gmt_offset' );

		if ( $offset == '' ) {
			$tzstring = get_option( 'timezone_string' );
			$current  = date_default_timezone_get();
			date_default_timezone_set( $tzstring );
			$offset = date( 'Z' ) / 3600;
			date_default_timezone_set( $current );
		}

		// check if timestamp has DST
		if ( ! is_null( $timestamp ) ) {
			$l = localtime( $timestamp, true );
			if ( $l['tm_isdst'] ) {
				$offset ++;
			}
		}

		return $in_seconds ? $offset * 3600 : (int) $offset;
	}

	/**
	 * Insert $new in $array after $key
	 *
	 * @param $key
	 * @param $new
	 *
	 * @param $array
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 */
	public static function array_insert_after( $array, $key, $new ) {
		$keys  = array_keys( $array );
		$index = array_search( $key, $keys );
		$pos   = false === $index ? count( $array ) : $index + 1;

		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}

	/**
	 * Insert a value or key/value pair before a specific key in an array.  If key doesn't exist, value is prepended
	 * to the beginning of the array.
	 *
	 * @param  string  $key
	 * @param  array  $new
	 *
	 * @param  array  $array
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 */
	public static function array_insert_before( array $array, $key, array $new ) {
		$keys = array_keys( $array );
		$pos  = (int) array_search( $key, $keys );

		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}

	/**
	 * Insert $new in $array after $key
	 *
	 * @param $array
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 *
	 */
	public static function is_forechable( $array = [] ) {

		if ( ! is_array( $array ) ) {
			return false;
		}

		if ( empty( $array ) ) {
			return false;
		}

		if ( count( $array ) <= 0 ) {
			return false;
		}

		return true;

	}

	/**
	 * Get current db version
	 *
	 * @since 1.0.0
	 */
	public static function get_db_version() {
		return Option::get( 'db_version', null );
	}

	/**
	 * Get data from array
	 *
	 * @param  string  $var
	 * @param  string  $default
	 * @param  bool  $clean
	 *
	 * @param  array  $array
	 *
	 * @return array|string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function get_data( $array = [], $var = '', $default = '', $clean = false ) {

		if ( empty( $array ) ) {
			return $default;
		}

		if ( ! empty( $var ) || ( 0 === $var ) ) {
			if ( strpos( $var, '|' ) > 0 ) {
				$vars = array_map( 'trim', explode( '|', $var ) );
				foreach ( $vars as $var ) {
					if ( isset( $array[ $var ] ) ) {
						$array = $array[ $var ];
					} else {
						return $default;
					}
				}

				return wp_unslash( $array );
			} else {
				$value = isset( $array[ $var ] ) ? wp_unslash( $array[ $var ] ) : $default;
			}
		} else {
			$value = wp_unslash( $array );
		}

		if ( $clean ) {
			$value = self::clean( $value );
		}

		return $value;
	}

	/**
	 * Get POST | GET data from $_REQUEST
	 *
	 * @param  string  $default
	 * @param  bool  $clean
	 *
	 * @param  string  $var
	 *
	 * @return array|string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function get_request_data( $var = '', $default = '', $clean = true ) {
		return self::get_data( $_REQUEST, $var, $default, $clean );
	}

	/**
	 * Get POST data from $_POST
	 *
	 * @param  string  $default
	 * @param  bool  $clean
	 *
	 * @param  string  $var
	 *
	 * @return array|string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function get_post_data( $var = '', $default = '', $clean = true ) {
		return self::get_data( $_POST, $var, $default, $clean );
	}

	/**
	 * Get Current Screen Id
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public static function get_current_screen_id() {

		$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		if ( ! $current_screen instanceof \WP_Screen ) {
			return '';
		}

		$current_screen = get_current_screen();

		return ( $current_screen ? $current_screen->id : '' );
	}


	/**
	 * Get all Plugin admin screens
	 *
	 * @return array|mixed|void
	 *
	 * @since 1.0.0
	 */
	public static function get_plugin_admin_screens() {

		// TODO: Can be updated with a version check when https://core.trac.wordpress.org/ticket/18857 is fixed
		$prefix = sanitize_title( __( 'Social Linkz', 'social-linkz' ) );

		$screens = [
			'toplevel_page_social-linkz',
			"{$prefix}_page_kc-sl-settings",
			"{$prefix}_page_social-linkz-tools",
			"{$prefix}_page_social-linkz-account",
		];

		$screens = apply_filters( 'kc_sl_admin_screens', $screens );

		return $screens;
	}

	/**
	 * Is es admin screen?
	 *
	 * @param  string  $screen_id  Admin screen id
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 *
	 */
	public static function is_plugin_admin_screen( $screen_id = '' ) {

		$current_screen_id = self::get_current_screen_id();
		// Check for specific admin screen id if passed.
		if ( ! empty( $screen_id ) ) {
			if ( $current_screen_id === $screen_id ) {
				return true;
			} else {
				return false;
			}
		}

		$plugin_admin_screens = self::get_plugin_admin_screens();

		if ( in_array( $current_screen_id, $plugin_admin_screens ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Current URL
	 *
	 * @return string
	 *
	 * @since 1.0.1
	 */
	public static function get_current_url() {
		$port = Helper::get_data( $_SERVER, 'SERVER_PORT' );
		$name = Helper::get_data( $_SERVER, 'SERVER_NAME' );

		if ( array_key_exists( 'HTTPS', $_SERVER ) && $_SERVER['HTTPS'] != 'off' && $_SERVER['HTTPS'] != '' ) {
			$name = "https://" . $name;
		} else {
			$name = "http://" . $name;
		}

		if ( $port != 80 && $port != 443 ) {
			$name = $name . ":" . $port;
		}

		return $name . $_SERVER["REQUEST_URI"];
	}

	/**
	 * Get Social Networks.
	 *
	 * @param $type
	 *
	 * @return []
	 *
	 * @since 1.8.0
	 *
	 */
	public static function get_social_networks( $type = 'share' ) {
		$networks = [
			'facebook' => [
				'name'   => 'Facebook',
				'icon'   => '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>',
				'share'  => true,
				'follow' => true,
			],

			'twitter' => [
				'name'   => 'X',
				'icon'   => '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>',
				'share'  => true,
				'follow' => true,
			],
		];

		//filter entire networks array
		$networks = apply_filters( 'kc_sl_social_networks', $networks );

		//filter out networks not matching requested type
		return array_filter( $networks, function ( $a ) use ( $type ) {
			return ! empty( $a[ $type ] );
		} );
	}

	/**
	 * Print inline styles.
	 *
	 * @param $args
	 *
	 * @param $location
	 *
	 * @return string|void
	 *
	 * @since 1.8.9
	 *
	 */
	public static function print_inline_styles( $location, $args ) {
		$button_margin         = intval( Helper::get_data( $args, 'design|button_margin', 10 ) );
		$hide_above_breakpoint = Helper::get_data( $args, 'display|hide_above_breakpoint', 0 );
		$hide_below_breakpoint = Helper::get_data( $args, 'display|hide_below_breakpoint', 0 );
		$mobile_breakpoint     = Helper::get_data( $args, 'display|mobile_breakpoint', 1200 );

		$show_labels           = Helper::get_data( $args, 'design|show_labels', 0 );
		$hide_labels_on_mobile = Helper::get_data( $args, 'design|hide_labels_on_mobile', 0 );
		$button_alignment      = Helper::get_data( $args, 'design|button_alignment', '' );
		$button_layout         = Helper::get_data( $args, 'design|button_layout', '' );

		$styles = "";
		if ( ! empty( $hide_above_breakpoint ) ) {
			$styles .= "@media (min-width: " . ( (int) ( ! empty( $mobile_breakpoint ) ? $mobile_breakpoint : "1200" ) + 1 ) . "px) {
			.social-linkz-" . $location . " {
				display: none;
			}
		}";
		}
		$styles .= "@media (max-width: " . ( ! empty( $mobile_breakpoint ) ? $mobile_breakpoint : "1200" ) . "px" . ") {
		" . ( ! empty( $hide_below_breakpoint ) ? ".social-linkz-" . $location . " { display: none; }" : "" ) . "
		" . ( ! empty( $show_labels ) && ! empty( $hide_labels_on_mobile ) ? ".social-linkz-buttons.social-linkz-" . $location . " .social-linkz-button-icon { width: 100%; } .social-linkz-buttons.social-linkz-" . $location . " .social-linkz-button-label { display: none; }" : "" ) . "
	}";
		if ( ! empty( $button_alignment ) && empty( $button_layout ) ) {
			$styles .= ".social-linkz-" . $location . ":not(.social-linkz-columns) .social-linkz-buttons-wrapper {
			justify-content: " . ( $button_alignment == 'right' ? "flex-end" : "center" ) . ";
		}";
			if ( $button_alignment == 'right' ) {
				$styles .= "body .social-linkz-" . $location . ":not(.social-linkz-columns) a.social-linkz-button, body .social-linkz-" . $location . " .social-linkz-total-share-count {
				margin: 0px 0px " . $button_margin . "px " . $button_margin . "px;
			}";
			} elseif ( $button_alignment == 'center' ) {
				$styles .= "body .social-linkz-" . $location . ":not(.social-linkz-columns) a.social-linkz-button, body .social-linkz-" . $location . " .social-linkz-total-share-count {
				margin: 0px " . ( $button_margin / 2 ) . "px " . $button_margin . "px " . ( $button_margin / 2 ) . "px;
			}";
			}
		} else {
			//if($button_margin != 10) {
			$styles .= "body .social-linkz-" . $location . " a.social-linkz-button, body .social-linkz-" . $location . " .social-linkz-total-share-count {
				margin: 0px " . $button_margin . "px " . $button_margin . "px 0px;
			}";
			//}
		}

		if ( $button_layout !== 'auto_width' && $button_margin != 10 ) {
			$columns = preg_replace( '/[^0-9]/', '', $button_layout );
			$styles  .= "body .social-linkz-" . $location . " a.social-linkz-button {
			flex-basis: calc(" . round( 100 / $columns,
					6 ) . "% - " . ( ( $columns - 1 ) * $button_margin ) / $columns . "px);
		}";

		}
		if ( ! empty( $styles ) ) {
			return "<style>" . $styles . "</style>";
		}
	}

	/**
	 * Print Buttons.
	 *
	 * @param $params
	 *
	 * @param $location
	 *
	 * @return string
	 *
	 * @since 1.8.0
	 *
	 */
	public static function print_buttons( $location, $params = [] ) {
		global $post;

		$settings = KC_SL()->get_settings();

		if ( $location == 'share' ) {
			$settings['share']['labels'] = 1;
			$settings['share']['layout'] = '3-col';
			if ( empty( $settings['share']['social_networks'] ) ) {
				$settings['share']['social_networks'] = array_keys( Helper::get_social_networks() );
				if ( ( $key = array_search( 'share', $settings['share']['social_networks'] ) ) !== false ) {
					unset( $settings['share']['social_networks'][ $key ] );
				}
			}
			unset( $settings['share']['cta_text'] );
		}

		if ( ! empty( $params ) ) {
			$settings[ $location ] = array_merge( $settings[ $location ] ?? [], $params );
		}

		$share_counts = [ 0 ];

		// Get post share counts.
		if ( ! empty( $settings[ $location ]['total_share_count'] ) || ! empty( $settings[ $location ]['network_share_counts'] ) ) {

			$post_id = is_singular() || ( $post && in_the_loop() ) ? $post->ID : ( is_home() ? ( is_front_page() ? 0 : get_option( 'page_for_posts' ) ) : null );

			$share_counts = [ 0 ];
			$share_count  = 0;

			//check for minimum share count
			if ( empty( $share_counts ) || ( isset( $settings['minimum_share_count'] ) && ( array_sum( $share_counts ) < $settings['minimum_share_count'] ) ) ) {
				unset( $share_counts );
			}
		}

		$location_settings = Helper::get_data( $settings, $location, [] );

		$social_networks = Helper::get_data( $location_settings, 'general|social_networks', [] );


		$output = "";

		if ( ! empty( $social_networks ) ) {
			// Remove subscribe button if link is not set.
			if ( ( $key = array_search( 'subscribe',
					$social_networks ) ) !== false && empty( $settings['subscribe_link'] ) ) {
				unset( $settings[ $location ]['social_networks'][ $key ] );
			}

			$networks = Helper::get_social_networks();

			// Remove mobile networks on desktop.
			$mobile_networks = apply_filters( 'kc_sl_mobile_networks', [] );
			if ( ! empty( $mobile_networks ) && is_array( $mobile_networks ) && ! wp_is_mobile() ) {
				$settings[ $location ]['social_networks'] = array_diff( $settings[ $location ]['social_networks'],
					$mobile_networks );
			}

			$button_shape = Helper::get_data( $location_settings, 'design|button_shape', 'squared' );
			$button_style = Helper::get_data( $location_settings, 'design|button_style', 'solid' );

			$shape_class = ! empty( $button_shape ) ? " social-linkz-" . $button_shape : '';

			//build classes
			$container_class = "";
			$button_class    = "";
			$icon_class      = "";
			$label_class     = "";

			// Button styles.
			if ( ! empty( $button_style ) ) {
				if ( $button_style == 'solid' ) {
					$button_class = $shape_class;
				} elseif ( $button_style == 'inverse' ) {
					$button_class = " social-linkz-inverse";
					$icon_class   .= " social-linkz-inverse social-linkz-border" . $shape_class;
					$label_class  .= " social-linkz-inverse";
				} elseif ( $button_style == 'bordered_label' ) {
					$button_class = " social-linkz-inverse" . $shape_class;
					$label_class  .= " social-linkz-border social-linkz-inverse";
				} elseif ( $button_style == 'bordered_button' ) {
					$button_class = " social-linkz-inverse social-linkz-border" . $shape_class;
					$icon_class   .= " social-linkz-inverse";
					$label_class  .= " social-linkz-inverse";
				} elseif ( $button_style == 'minimal_label' ) {
					$button_class = " social-linkz-inverse";
					$icon_class   .= $shape_class;
					$label_class  .= " social-linkz-inverse";
				} elseif ( $button_style == 'minimal' ) {
					$button_class = " social-linkz-inverse" . $shape_class;
					$icon_class   .= " social-linkz-inverse";
					$label_class  .= " social-linkz-inverse";
				}
			}

			$button_layout = Helper::get_data( $location_settings, 'design|button_layout', '' );

			// Layout container classes.
			if ( ! empty( $button_layout ) ) {
				$container_class .= " social-linkz-columns social-linkz-" . $button_layout;
			}

			$button_size = Helper::get_data( $location_settings, 'design|button_size', '' );

			// Size container classes.
			if ( ! empty( $button_size ) ) {
				$container_class .= " " . $button_size;
			}

			// Share count container classes
			if ( ! empty( $share_counts ) ) {
				$container_class .= " social-linkz-share-count";
				if ( ! empty( $settings[ $location ]['total_share_count'] ) ) {
					$container_class .= " social-linkz-has-total-share-count-";
					$container_class .= ( ! empty( $settings[ $location ]['total_share_count_position'] ) ? $settings[ $location ]['total_share_count_position'] : 'after' );
				}
			}

			$inverse_on_hover = Helper::get_data( $location_settings, 'design|inverse_on_hover', 0 );

			// Inverse on hover.
			if ( ! empty( $inverse_on_hover ) ) {
				$container_class .= " social-linkz-inverse-hover";
			}

			// Add override styles.
			global $settings_inline_css;

			// Create array if it doesn't exist yet
			if ( ! is_array( $settings_inline_css ) ) {
				$settings_inline_css = [];
			}

			// <ake sure we haven't printed css for this location yet
			if ( ! in_array( $location, $settings_inline_css ) ) {

				// Add used location to global flag.
				$settings_inline_css[] = $location;
				$output                .= Helper::print_button_inline_styles( $location, $settings[ $location ] );
			}

			// Buttons container.
			$output .= "<div class='social-linkz-buttons social-linkz-" . $location . $container_class . " social-linkz-no-print'>";

			$cta_text         = Helper::get_data( $location_settings, 'cta|text', '' );
			$cta_font_size    = intval( Helper::get_data( $location_settings, 'cta|font_size', '' ) );
			$cta_font_color   = Helper::get_data( $location_settings, 'cta|font_color', '' );
			$button_alignment = Helper::get_data( $location_settings, 'design|button_alignment', '' );

			// Call to action
			if ( ! empty( $cta_text ) ) {
				$cta_style = "";
				$cta_style .= ! empty( $cta_font_size ) ? "font-size:" . intval( $cta_font_size ) . "px;" : "";
				$cta_style .= ! empty( $cta_font_color ) ? "color:" . $cta_font_color . ";" : "";
				$cta_style .= ! empty( $button_alignment ) ? "text-align:" . $button_alignment . ";" : "";
				$output    .= "<div class='social-linkz-inline-cta'" . ( ! empty( $cta_style ) ? " style='" . $cta_style . "'" : "" ) . ">" . apply_filters( 'kc_sl_inline_cta',
						$cta_text ) . "</div>";
			}

			$output .= "<div class='social-linkz-buttons-wrapper'>";

			$output_buttons = "";

			$social_networks = apply_filters( 'kc_sl_button_networks',
				Helper::get_data( $location_settings, 'general|social_networks', [] ), $location );

			// Remove share button if were in a feed.
			if ( ! is_singular() && in_the_loop() ) {
				if ( ( $key = array_search( 'share', $social_networks ) ) !== false ) {
					unset( $social_networks[ $key ] );
				}
			}

			// Setup copy button url.
			$copy_url = ( $post && ( is_singular() || in_the_loop() ) ) ? '"' . get_the_permalink( $post->ID ) . '"' : 'window.location.href';

			$network_share_counts       = Helper::get_data( $location_settings, 'share_counts|network_share_counts',
				0 );
			$total_share_count          = Helper::get_data( $location_settings, 'share_counts|total_share_count', 0 );
			$total_share_count_position = Helper::get_data( $location_settings,
				'share_counts|total_share_count_position',
				0 );

			$show_labels = Helper::get_data( $location_settings, 'design|show_labels', 0 );

			// Print buttons.
			foreach ( $social_networks as $key => $network ) {

				$class = $network;
				if ( ! empty( $share_counts[ $network ] ) && ! empty( $network_share_counts ) ) {
					$class .= " social-linkz-share-count";
				}
				$target = '_blank';
				if ( in_array( $network, [ 'email', 'sms' ] ) ) {
					$target = '_self';
				}

				// Button link.
				$output_buttons .= "<a href='" . Helper::get_network_link( $network ) . "' aria-label='" . $networks[ $network ]['name'] . "' target='" . $target . "' class='social-linkz-button " . $class . "' rel='nofollow noopener noreferrer'";
				if ( $network == 'pinterest' ) {
					$output_buttons .= " data-pin-do='none'";
					if ( ! empty( $settings['pinterest']['share_button_behavior'] ) ) {
						$output_buttons .= " data-pinterest-gallery='1'";
					}
				} elseif ( $network == 'copy' ) {
					$output_buttons .= " onClick='event.preventDefault();navigator.clipboard.writeText(" . $copy_url . ").then(() =&gt; alert(\"" . __( 'URL Copied',
							'social-linkz' ) . "\"));'";
				} elseif ( $network == 'mastodon' ) {
					$output_buttons .= " onClick=\"event.preventDefault();nsMastodonWindow(this);\"";
				} elseif ( $network == 'print' ) {
					$output_buttons .= " onClick='event.preventDefault();window.print();'";
				} elseif ( $network == 'share' ) {
					$output_buttons .= " onClick='event.preventDefault();'";
				}
				$button_attributes = apply_filters( 'kc_sl_button_attributes', [], $network, $location );
				if ( ! empty( $button_attributes ) && is_array( $button_attributes ) ) {
					foreach ( $button_attributes as $att => $val ) {
						$output_buttons .= " " . $att . "='" . $val . "'";
					}
				}
				$output_buttons .= ">";

				// Button wrapper.
				$output_buttons .= "<span class='social-linkz-button-wrapper social-linkz-button-block" . $button_class . "'>";

				// Button icon.
				$output_buttons .= "<span class='social-linkz-button-icon social-linkz-button-block" . $icon_class . "'>";
				$output_buttons .= $networks[ $network ]['icon'];
				if ( ! empty( $share_counts[ $network ] ) ) {
					$output_buttons .= "<span class='social-linkz-button-share-count'>" . round( $share_counts[ $network ] ) . "</span>";
					$share_count    = $share_count + $share_counts[ $network ];
				}
				$output_buttons .= "</span>";

				// Button label.
				if ( $location != 'floating' ) {
					$output_buttons .= "<span class='social-linkz-button-label social-linkz-button-block" . $label_class . ( empty( $show_labels ) ? " social-linkz-hide" : "" ) . "'>";
					$output_buttons .= "<span class='social-linkz-button-label-wrapper'>";
					$output_buttons .= $networks[ $network ]['name'];
					$output_buttons .= "</span>";
					$output_buttons .= "</span>";
				}

				$output_buttons .= "</span>";

				$output_buttons .= "</a>";
			}

			// Show total share count.
			if ( ! empty( $total_share_count ) && ! empty( $share_count ) ) {

				$output_total_share_count = 0;

				if ( ! empty( $total_share_count_position ) ) {
					$output .= $output_total_share_count . $output_buttons;
				} else {
					$output .= $output_buttons . $output_total_share_count;
				}
			} else {
				$output .= $output_buttons;
			}

			$output .= "</div>";
			$output .= "</div>";
		}


		return $output;
	}

	/**
	 * Print button inline style.
	 *
	 * @param $location
	 * @param $args
	 *
	 * @return string|void
	 *
	 * @since 1.8.2
	 */
	public static function print_button_inline_styles( $location, $args ) {
		$show_labels             = Helper::get_data( $args, 'design|show_labels', 0 );
		$button_color            = Helper::get_data( $args, 'design|button_color', '' );
		$button_hover_color      = Helper::get_data( $args, 'design|button_hover_color', '' );
		$inverse_on_hover        = Helper::get_data( $args, 'design|inverse_on_hover', 0 );
		$icon_color              = Helper::get_data( $args, 'design|icon_color', 0 );
		$icon_hover_color        = Helper::get_data( $args, 'design|icon_hover_color', 0 );
		$total_share_count       = Helper::get_data( $args, 'share_counts|total_share_count', 0 );
		$total_share_count_color = Helper::get_data( $args, 'share_counts|total_share_count_color', 0 );

		$styles = "";
		if ( empty( $show_labels ) ) {
			$styles .= "body .social-linkz-buttons.social-linkz-" . $location . " .social-linkz-button-icon { width: 100%; }";
		}
		if ( ! empty( $button_color ) ) {
			$styles .= ".social-linkz-" . $location . " .social-linkz-button { --social-linkz-button-color: " . $button_color . "; }";
		}
		if ( ! empty( $button_hover_color ) ) {
			$styles .= ".social-linkz-" . $location . " .social-linkz-button:hover { --social-linkz-button-color: " . $button_hover_color . "; }";
			$styles .= "body .social-linkz-" . $location . " a.social-linkz-button:hover .social-linkz-button-wrapper>span { box-shadow: none !important; filter: brightness(1) !important; }";
		}
		if ( empty( $inverse_on_hover ) ) {
			if ( ! empty( $icon_color ) ) {
				$styles .= ".social-linkz-" . $location . " .social-linkz-button-icon *, .social-linkz-" . $location . " .social-linkz-button-label span { color: " . $icon_color . "; }";
			}
			if ( ! empty( $icon_hover_color ) ) {
				$styles .= ".social-linkz-" . $location . " .social-linkz-button:hover .social-linkz-button-icon *, .social-linkz-" . $location . " .social-linkz-button:hover .social-linkz-button-label span { color: " . $icon_hover_color . "; }";
			}
		}
		if ( ! empty( $total_share_count ) ) {
			if ( ! empty( $total_share_count_color ) ) {
				$styles .= "body .social-linkz-" . $location . " .social-linkz-total-share-count { color: " . $total_share_count_color . "; }";
			}
		}
		if ( ! empty( $styles ) ) {
			return "<style>" . $styles . "</style>";
		}
	}

	/**
	 * Get the network link.
	 *
	 * @param $network
	 *
	 * @return mixed|string|null
	 *
	 * @since 1.8.2
	 */
	public static function get_network_link( $network ) {

		if ( empty( $network ) ) {
			return '';
		}

		global $post;

		$settings = KC_SL()->get_settings();

		$shorten = true;
		//make sure we have valid post
		if ( $post && ( is_singular() || in_the_loop() ) && in_array( $post->post_status,
				[ 'publish', 'private', 'inherit' ] ) ) {

			//post permalink
			$permalink = apply_filters( 'kc_sl_post_permalink', get_the_permalink( $post->ID ) );

			//title
			$title = $post->post_title;

			//image
			$social_image = '';

		} else {

			if ( is_home() ) {
				$title     = get_bloginfo( 'name' );
				$permalink = get_post_type_archive_link( 'post' );
			} elseif ( is_archive() ) {
				$title     = strip_tags( get_the_archive_title() );
				$permalink = get_pagenum_link();
				$shorten   = false;
			} else {
				return '';
			}

			$social_image = '';
		}

		// Generate Short URL using URL Shortify if it's enable.
		if ( Helper::get_data( $settings, 'configuration|link_shortening|url_shortify', 0 ) ) {
			if ( function_exists( 'get_the_shorturl' ) ) {
				$permalink = get_the_shorturl( true );
			}
		}

		// Encode final permalink.
		$permalink = rawurlencode( apply_filters( 'kc_sl_permalink', $permalink, $network ) );

		// Apply filters.
		$title = rawurlencode( apply_filters( 'kc_sl_meta_title', $title, $network ) );

		//build link for network
		switch ( $network ) {
			case 'twitter':
				$link = "https://twitter.com/intent/tweet?text=" . $title . "&url=" . $permalink . ( ! empty( $settings['twitter_username'] ) ? "&via=" . $settings['twitter_username'] : "" );
				break;

			case 'facebook':
				$link = "https://www.facebook.com/sharer/sharer.php?u=" . $permalink;
				break;

			case 'linkedin':
				$link = "https://www.linkedin.com/shareArticle?title=" . $title . "&url=" . $permalink . "&mini=true";
				break;

			case 'pinterest':
				$pinterest_image = '';
				if ( ! empty( $details['pinterest_image'] ) ) {
					$pinterest_image = wp_get_attachment_url( $details['pinterest_image'] );
				}
				if ( empty( $pinterest_image ) ) {
					$pinterest_image = $social_image;
				}
				$pinterest_title = ! empty( $details['pinterest_title'] ) ? rawurlencode( apply_filters( 'kc_sl_meta_title',
					$details['pinterest_title'], 'pinterest' ) ) : $title;
				if ( $pinterest_image ) {
					$link = "https://pinterest.com/pin/create/button/?url=" . $permalink . "&media=" . $pinterest_image . "&description=" . $pinterest_title;
				} else {
					$link = '#';
				}
				break;

			case 'buffer':
				$link = "https://buffer.com/add?url=" . $permalink . "&text=" . $title;
				break;

			case 'reddit':
				$link = "https://www.reddit.com/submit?url=" . $permalink . "&title=" . $title;
				break;

			case 'hackernews':
				$link = "https://news.ycombinator.com/submitlink?u=" . $permalink . "&t=" . $title;
				break;

			case 'pocket':
				$link = "https://getpocket.com/edit?url=" . $permalink;
				break;

			case 'whatsapp':
				$link = "https://api.whatsapp.com/send?text=" . $title . "+" . $permalink;
				break;

			case 'tumblr':
				$link = "https://www.tumblr.com/widgets/share/tool?canonicalUrl=" . $permalink;
				break;

			case 'vkontakte':
				$link = "https://vk.com/share.php?url=" . $permalink;
				break;

			case 'xing':
				$link = "https://www.xing.com/spi/shares/new?url=" . $permalink;
				break;

			case 'flipboard':
				$link = "https://share.flipboard.com/bookmarklet/popout?v=2&url=" . $permalink . "&title=" . $title;
				break;

			case 'telegram':
				$link = "https://telegram.me/share/url?url=" . $permalink . "&text=" . $title;
				break;

			case 'mix':
				$link = "https://mix.com/add?url=" . $permalink;
				break;

			case 'threads':
				$link = 'https://www.threads.net/intent/post?text=' . $permalink;
				break;

			case 'yummly':
				$link = "https://www.yummly.com/urb/verify?url=" . $permalink . "&title=" . $title . "&image=" . $social_image . "&yumtype=button";
				break;

			case 'sms':
				$link = "sms:?&body=" . $title . "%20" . $permalink;
				break;

			case 'mastodon':
				$link = 'https://mastodon.social/share?text=' . $permalink;
				if ( ! empty( $settings['mastodon_username'] ) ) {
					$link .= rawurlencode( ' via @' . $settings['mastodon_username'] );
				}
				$link .= '&title=' . $title;
				break;

			case 'messenger':
				if ( ! wp_is_mobile() ) {
					if ( ! empty( $settings['facebook_app_id'] ) ) {
						$link = "https://www.facebook.com/dialog/send?app_id=" . $settings['facebook_app_id'] . "&display=popup&link=" . $permalink . "&redirect_uri=" . $permalink;
					} else {
						$link = "https://www.facebook.com/sharer/sharer.php?u=" . $permalink;
					}
				} else {
					$link = "fb-messenger://share/?link=" . $permalink;
				}
				break;

			case 'line':
				$link = "https://lineit.line.me/share/ui?url=" . $permalink . "&text=" . $title;
				break;

			case 'email':
				$link = "mailto:?subject=" . esc_attr( $title ) . "&amp;body=" . $permalink;
				break;

			case 'subscribe':
				$link = $settings['subscribe_link'] ?? '';
				break;

			default:
				$link = '#';
				break;
		}

		//return final link
		return apply_filters( 'kc_sl_network_link', $link, $permalink, $network );
	}

	/**
	 * Get all CPT data.
	 *
	 * @return string[]|\WP_Post_Type[]
	 *
	 * @since 1.8.0
	 */
	public static function get_all_cpt_data() {
		return get_post_types( [ '_builtin' => false, 'public' => true ], 'objects', 'and' );
	}

	/**
	 * Get all Custom Post Types.
	 *
	 * @return array
	 *
	 * @since 1.8.1
	 */
	public static function get_all_cpts() {
		$custom_post_types = [
			'post' => __( 'Posts', 'social-linkz' ),
			'page' => __( 'Pages', 'social-linkz' ),
		];

		$custom_post_types = apply_filters( 'kc_sl_get_custom_post_types', $custom_post_types );

		asort( $custom_post_types );

		return $custom_post_types;
	}

	/**
	 * Get Button Positions.
	 *
	 * @return array
	 *
	 * @since 1.8.1
	 */
	public static function get_button_positions() {
		return [
			'above_content'           => __( 'Above Content', 'social-linkz' ),
			'below_content'           => __( 'Below Content', 'social-linkz' ),
			'above_and_below_content' => __( 'Above and Below Content', 'social-linkz' ),
			'do_not_add_to_content'   => __( 'Don\'t Add To Content (Shortcode)', 'social-linkz' ),
		];
	}

	/**
	 * Get Button Styles.
	 *
	 * @return mixed|null
	 *
	 * @since 1.8.1
	 */
	public static function get_inline_button_styles() {
		$styles = [
			'solid' => __( 'Solid', 'social-linkz' ),
		];

		return apply_filters( 'kc_sl_inline_button_styles', $styles );
	}

	/**
	 * Get Button Layouts.
	 *
	 * @return mixed|null
	 *
	 * @since 1.8.1
	 */
	public static function get_inline_button_layouts() {
		$layouts = [
			'auto_width' => __( 'Auto Width', 'social-linkz' ),
		];

		return apply_filters( 'kc_sl_inline_button_layouts', $layouts );
	}

	/**
	 * Get inline button alignments.
	 *
	 * @return array
	 *
	 * @since 1.8.1
	 */
	public static function get_inline_button_alignments() {
		return [
			'left'   => __( 'Left', 'social-linkz' ),
			'right'  => __( 'Right', 'social-linkz' ),
			'center' => __( 'Center', 'social-linkz' ),
		];
	}

	/**
	 * Get inline button size.
	 *
	 * @return array
	 *
	 * @since 1.8.1
	 */
	public static function get_inline_button_size() {
		return [
			'small'  => __( 'Small', 'social-linkz' ),
			'medium' => __( 'Medium', 'social-linkz' ),
			'large'  => __( 'Large', 'social-linkz' ),
		];
	}

	/**
	 * Get inline button shapes.
	 *
	 * @return array
	 *
	 * @since 1.8.1
	 */
	public static function get_inline_button_shapes() {
		$shapes = [
			'squared' => __( 'Squared', 'social-linkz' ),
		];

		return apply_filters( 'kc_sl_inline_button_shapes', $shapes );
	}

	public static function share_block( $attributes = [], $content = [], $block = [] ) {

		//check for inner blocks
		$block_count = ! empty( $block->inner_blocks ) ? count( $block->inner_blocks ) : 0;
		if ( $block_count < 1 ) {
			return;
		}

		// Setup Instance.
		$instance = [
			'design' => [
				'button_style'       => $attributes['buttonStyle'] ?? 'solid',
				'button_layout'      => $attributes['buttonLayout'] ?? 'auto_width',
				'button_alignment'   => $attributes['alignment'] ?? 'left',
				'button_size'        => $attributes['buttonSize'] ?? 'medium',
				'button_shape'       => $attributes['buttonShape'] ?? 'squared',
				'button_color'       => $attributes['buttonColor'] ?? '',
				'button_hover_color' => $attributes['buttonHoverColor'] ?? '',
				'icon_color'         => $attributes['iconColor'] ?? '',
				'icon_hover_color'   => $attributes['iconHoverColor'] ?? '',
				'inverse_on_hover'   => $attributes['inverseHover'] ?? 0,
				'button_margin'      => $attributes['buttonMargin'] ?? 10,
				'show_labels'        => $attributes['showLabels'] ?? 0,
			],

			'display' => [
				'mobile_breakpoint'     => $attributes['mobileBreakpoint'] ?? 0,
				'hide_above_breakpoint' => $attributes['hideAboveBreakpoint'] ?? '',
				'hide_below_breakpoint' => $attributes['hideBelowBreakpoint'] ?? '',
			],

			'share_counts' => [
				'total_share_count'          => $attributes['totalShareCount'] ?? '',
				'total_share_count_position' => $attributes['totalShareCountPosition'] ?? '',
				'total_share_count_color'    => $attributes['totalShareCountColor'] ?? '',
				'network_share_counts'       => $attributes['networkShareCounts'] ?? '',
			],

			'cta' => [
				'text'       => $attributes['ctaText'] ?? '',
				'font_size'  => $attributes['ctaSize'] ?? '',
				'font_color' => $attributes['ctaColor'] ?? '',
			],


		];

		$instance['id'] = $attributes['id'];

		for ( $i = 1; $i <= $block_count; $i ++ ) {
			$inner_block = $block->inner_blocks->current();
			if ( ! empty( $inner_block->attributes['network'] ) ) {
				$instance['general']['social_networks'][] = $inner_block->attributes['network'];
			}

			$block->inner_blocks->next();
		}

		return Helper::print_inline_styles( 'block-' . $instance['id'],
				$instance ) . Helper::print_buttons( 'block-' . $instance['id'], $instance );
	}


	/**
	 * Return settings.
	 * @return array|mixed
	 * @since 1.8.6
	 */
	public static function tinymce_localized_settings() {

		$settings = KC_SL()->get_settings();

		// Translations.
		$settings['translations'] = [
			'ctt' => [
				'tooltip' => __( 'Click to Post', 'social-linkz' ),
				'title'   => __( 'Click to Post Shortcode', 'social-linkz' ),
				'submit'  => __( 'Insert Shortcode', 'social-linkz' ),
				'body'    => [
					'tweet'           => __( 'Post', 'social-linkz' ),
					'theme'           => [
						'title'  => __( 'Theme', 'social-linkz' ),
						'values' => [
							'default'   => __( 'Default (Black Background)', 'social-linkz' ),
							'simple'    => __( 'Simple (Transparent Background)', 'social-linkz' ),
							'simplealt' => __( 'Simple Alternate (Gray Background)', 'social-linkz' ),
						],
					],
					'ctatext'         => __( 'Call to Action Text', 'social-linkz' ),
					'ctaposition'     => [
						'title'  => __( 'Call to Action Position', 'social-linkz' ),
						'values' => [
							'default' => __( 'Right (Default)', 'social-linkz' ),
							'left'    => __( 'Left', 'social-linkz' ),
						],
					],
					'removeurl'       => [
						'title' => __( 'Remove Post URL', 'social-linkz' ),
						'text'  => __( 'The current URL will not be added to the post.', 'social-linkz' ),
					],
					'removeuser'      => [
						'title' => __( 'Remove Username', 'social-linkz' ),
						'text'  => __( 'The X username saved in social-linkz will not be added to the post.',
							'social-linkz' ),
					],
					'hidehash'        => [
						'title' => __( 'Hide Hashtags', 'social-linkz' ),
						'text'  => __( 'Trailing hashtags will be hidden from the display box.', 'social-linkz' ),
					],
					'accentcolortext' => __( 'Accent Color', 'social-linkz' ),
					'charcount'       => __( 'Characters Remaining', 'social-linkz' ),
				],
			],
		];

		// Share networks.
		$settings['networks']['share'] = Helper::get_social_networks( 'share' );

		$button_styles_data = Helper::get_inline_button_styles();

		$button_styles = [];
		foreach ( $button_styles_data as $value => $label ) {
			$button_styles[] = [
				'value' => $value,
				'label' => $label,
			];
		}

		$button_layouts_data = Helper::get_inline_button_layouts();
		$button_layouts = [];
		foreach ( $button_layouts_data as $value => $label ) {
			$button_layouts[] = [
				'value' => $value,
				'label' => $label,
			];
		}

		$button_shapes_data = Helper::get_inline_button_shapes();
		$button_shapes = [];
		foreach ( $button_shapes_data as $value => $label ) {
			$button_shapes[] = [
				'value' => $value,
				'label' => $label,
			];
		}

		$settings['isPRO'] = KC_SL()->is_pro();

		$settings['block'] = [
			'design' => [
				'buttonStyles'  => $button_styles,
				'buttonLayouts' => $button_layouts,
				'buttonShapes'  => $button_shapes,
			],
		];

		return $settings;
	}
}