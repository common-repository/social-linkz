<?php

/**
 *
 * Social Linkz
 *
 * Lightweight and fast social media sharing plugin
 *
 * @link      https://wordpress.org/plugins/social-linkz
 * @author    KaizenCoders <hello@kaizencoders.com>
 * @license   GPL-3.0+
 * @package   Social Linkz
 * @copyright 2024 KaizenCoders
 *
 * @wordpress-plugin
 * Plugin Name:       Social Linkz
 * Plugin URI:        https://kaizencoders.com/social-linkz
 * Description:       Lightweight and fast social media sharing plugin
 * Version:           1.8.6
 * Author:            KaizenCoders
 * Author URI:        https://kaizencoders.com
 * Text Domain:       social-linkz
 * Tested up to:      6.6.2
 * Requires PHP:      5.6.4
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 *
 * @fs_premium_only /pro/
 * @fs_ignore /vendor/, /lite/dist/styles/app.css, /lite/scripts/app.js
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( function_exists( 'kc_sl_fs' ) ) {
	kc_sl_fs()->set_basename( true, __FILE__ );
} else {
	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 */
	if ( ! defined( 'KC_SL_PLUGIN_VERSION' ) ) {
		define( 'KC_SL_PLUGIN_VERSION', '1.8.6' );
	}

	/**
	 * Minimum PHP version required for URL Shortify
	 *
	 * @since 1.0.0
	 *
	 */
	if ( ! defined( 'KC_SL_MIN_PHP_VER' ) ) {
		define( 'KC_SL_MIN_PHP_VER', '5.6.4' );
	}

	// Create a helper function for easy SDK access.
	function kc_sl_fs() {
		global $kc_sl_fs;

		if ( ! isset( $kc_sl_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/libs/fs/start.php';

			$kc_sl_fs = fs_dynamic_init( [
				'id'                  => '16515',
				'slug'                => 'social-linkz',
				'type'                => 'plugin',
				'public_key'          => 'pk_9a4efaa6c592d1728ec0374c27254',
				'is_premium'          => false,
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'has_affiliation'     => 'selected',
				'menu'                => [
					'slug'       => 'social-linkz',
					'first-path' => 'admin.php?page=social-linkz',
				],
			] );
		}

		return $kc_sl_fs;
	}

	// Init Freemius.
	kc_sl_fs();

	// Use custom icon for onboarding.
	kc_sl_fs()->add_filter( 'plugin_icon', function () {
		return dirname( __FILE__ ) . '/assets/images/plugin-icon.png';
	} );

	// Signal that SDK was initiated.
	do_action( 'kc_sl_fs_loaded' );

	if ( ! function_exists( 'kc_sl_fail_php_version_notice' ) ) {
		/**
		 * Admin notice for minimum PHP version.
		 *
		 * Warning when the site doesn't have the minimum required PHP version.
		 *
		 * @return void
		 * @since 1.0.0
		 *
		 */
		function kc_sl_fail_php_version_notice() {
			/* translators: %s: PHP version */
			$message      = sprintf( esc_html__( 'Social Linkz requires PHP version %s+, plugin is currently NOT RUNNING.',
				'social-linkz' ), KC_SL_MIN_PHP_VER );
			$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
			echo wp_kses_post( $html_message );
		}
	}


	if ( ! version_compare( PHP_VERSION, KC_SL_MIN_PHP_VER, '>=' ) ) {
		add_action( 'admin_notices', 'kc_sl_fail_php_version_notice' );

		return;
	}

	if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
		require_once dirname( __FILE__ ) . '/vendor/autoload.php';
	}

// Plugin Folder Path.
	if ( ! defined( 'KC_SL_PLUGIN_DIR' ) ) {
		define( 'KC_SL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}

	if ( ! defined( 'KC_SL_PLUGIN_BASE_NAME' ) ) {
		define( 'KC_SL_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
	}

	if ( ! defined( 'KC_SL_PLUGIN_FILE' ) ) {
		define( 'KC_SL_PLUGIN_FILE', __FILE__ );
	}

	if ( ! defined( 'KC_SL_PLUGIN_URL' ) ) {
		define( 'KC_SL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	if ( ! defined( 'KC_SL_PLUGIN_ASSETS_DIR_URL' ) ) {
		define( 'KC_SL_PLUGIN_ASSETS_DIR_URL', KC_SL_PLUGIN_URL . 'lite/dist/assets' );
	}

	if ( ! defined( 'KC_SL_PLUGIN_STYLES_DIR_URL' ) ) {
		define( 'KC_SL_PLUGIN_STYLES_DIR_URL', KC_SL_PLUGIN_URL . 'lite/dist/styles' );
	}

	/**
	 * The code that runs during plugin activation.
	 */
	\register_activation_hook( __FILE__, '\KaizenCoders\SocialLinkz\Activator::activate' );

	/**
	 * The code that runs during plugin deactivation.
	 */
	\register_deactivation_hook( __FILE__, '\KaizenCoders\SocialLinkz\Deactivator::deactivate' );

	if ( ! function_exists( 'KC_SL' ) ) {
		/**
		 * Get plugin instance
		 *
		 * @since 1.0.0
		 */
		function KC_SL() {
			return \KaizenCoders\SocialLinkz\Plugin::instance();
		}

		add_action( 'plugins_loaded', function () {
			KC_SL()->run();
		} );
	}

}
