<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    SocialLinkz
 * @subpackage SocialLinkz/admin
 */

namespace KaizenCoders\SocialLinkz;

use KaizenCoders\SocialLinkz\Admin\Controllers\ToolsController;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    SocialLinkz
 * @subpackage SocialLinkz/admin
 * @author     Your Name <email@example.com>
 */
class Admin {

	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param  Plugin  $plugin  This plugin's instance.
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Social Linkz_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Social Linkz_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( Helper::is_plugin_admin_screen() ) {

			\wp_enqueue_style(
				'social-linkz-main',
				\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/app.css',
				[],
				$this->plugin->get_version(),
				'all' );

			\wp_enqueue_style(
				$this->plugin->get_plugin_name() . '-admin',
				\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/social-linkz-admin.css',
				[],
				$this->plugin->get_version(),
				'all' );
		}

		\wp_enqueue_style(
			'social-linkz',
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/social-linkz.css',
			[],
			$this->plugin->get_version(),
			'all' );


	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Social Linkz_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Social Linkz_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( Helper::is_plugin_admin_screen() ) {

			// jQuery UI Sortable.
			wp_enqueue_script( 'jquery-ui-sortable' );

			// Color picker UI.
			wp_enqueue_style( 'wp-color-picker' );

			\wp_enqueue_script(
				$this->plugin->get_plugin_name() . '-app',
				\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/app.js',
				[ 'jquery' ],
				$this->plugin->get_version(),
				true );

			\wp_enqueue_script(
				$this->plugin->get_plugin_name(),
				\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/social-linkz.js',
				[ 'jquery' ],
				$this->plugin->get_version(),
				false );

			\wp_enqueue_script(
				$this->plugin->get_plugin_name() . '-admin',
				\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/social-linkz-admin.js',
				[ 'wp-color-picker', 'wp-i18n' ],
				$this->plugin->get_version(),
				false );
		}
	}

	/**
	 * Add admin menu
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {

		add_menu_page( __( 'Social Linkz', 'social-linkz' ), __( 'Social Linkz', 'social-linkz' ), 'manage_options',
			'social-linkz', [ $this, 'render_dashboard' ], 'dashicons-share', 30 );

		$hook = add_submenu_page( 'social-linkz', __( 'Tools', 'social-linkz' ),
			__( 'Tools', 'social-linkz' ), 'read', 'social-linkz-tools', [
				$this,
				'render_tools_page',
			] );

		new \KaizenCoders\SocialLinkz\Admin\Settings();
	}

	public function render_dashboard() {
		include_once KC_SL_ADMIN_TEMPLATES_DIR . '/dashboard.php';
	}

	public function render_tools_page() {
		$tools = new ToolsController();
		$tools->render();
	}

	/**
	 * Remove all unwanted admin notices from others
	 *
	 * @since 1.0.0
	 */
	public function remove_admin_notices() {
		global $wp_filter;

		if ( ! Helper::is_plugin_admin_screen() ) {
			return;
		}

		$get_page = Helper::get_request_data( 'page' );

		if ( ! empty( $get_page ) && 'social-linkz' == $get_page ) {
			remove_all_actions( 'admin_notices' );
		} else {

			$allow_display_notices = [
				'show_review_notice',
				'kc_sl_fail_php_version_notice',
				'kc_sl_show_admin_notice',
				'show_custom_notices',
				'_admin_notices_hook',
			];

			$filters = [
				'admin_notices',
				'user_admin_notices',
				'all_admin_notices',
			];

			foreach ( $filters as $filter ) {

				if ( ! empty( $wp_filter[ $filter ]->callbacks ) && is_array( $wp_filter[ $filter ]->callbacks ) ) {

					foreach ( $wp_filter[ $filter ]->callbacks as $priority => $callbacks ) {

						foreach ( $callbacks as $name => $details ) {

							if ( is_object( $details['function'] ) && $details['function'] instanceof \Closure ) {
								unset( $wp_filter[ $filter ]->callbacks[ $priority ][ $name ] );
								continue;
							}

							if ( ! empty( $details['function'][0] ) && is_object( $details['function'][0] ) && count( $details['function'] ) == 2 ) {
								$notice_callback_name = $details['function'][1];
								if ( ! in_array( $notice_callback_name, $allow_display_notices ) ) {
									unset( $wp_filter[ $filter ]->callbacks[ $priority ][ $name ] );
								}
							}

							if ( ! empty( $details['function'] ) && is_string( $details['function'] ) ) {
								if ( ! in_array( $details['function'], $allow_display_notices ) ) {
									unset( $wp_filter[ $filter ]->callbacks[ $priority ][ $name ] );
								}
							}
						}
					}
				}

			}
		}

	}

	/**
	 * Redirect after activation
	 *
	 * @since 1.0.0
	 *
	 */
	public function redirect_to_dashboard() {

		// Check if it is multisite and the current user is in the network administrative interface. e.g. `/wp-admin/network/`
		if ( is_multisite() && is_network_admin() ) {
			return;
		}

		if ( get_option( 'social_linkzdo_activation_redirect', false ) ) {
			delete_option( 'social_linkzdo_activation_redirect' );
			wp_redirect( 'admin.php?page=social-linkz' );
		}
	}

	/**
	 * Dismiss Admin Notices
	 *
	 * @since 1.2.11
	 */
	public function dismiss_admin_notice() {
		if ( isset( $_GET['kc_sl_dismiss_admin_notice'] ) && $_GET['kc_sl_dismiss_admin_notice'] == '1' && isset( $_GET['option_name'] ) ) {

			$option_name = sanitize_text_field( $_GET['option_name'] );

			update_option( 'kc_sl_' . $option_name . '_dismissed', 'yes', false );

			if ( $option_name === 'offer_halloween_2020' ) {
				exit();
			} else {
				$referer = wp_get_referer();
				wp_safe_redirect( $referer );
				exit();
			}
		}
	}

	public function kc_sl_show_admin_notice() {

		$notice = Cache::get_transient( 'notice' );

		if ( ! empty( $notice ) ) {

			$status = Helper::get_data( $notice, 'status', '' );

			if ( ! empty( $status ) ) {
				$message       = Helper::get_data( $notice, 'message', '' );
				$is_dismisible = Helper::get_data( $notice, 'is_dismisible', true );

				switch ( $status ) {
					case 'success':
						KC_SL()->notices->success( $message, $is_dismisible );
						break;
					case 'error':
						KC_SL()->notices->error( $message, $is_dismisible );
						break;
					case 'warning':
						KC_SL()->notices->warning( $message, $is_dismisible );
						break;
					case 'info':
					default;
						KC_SL()->notices->info( $message, $is_dismisible );
						break;

				}

				Cache::delete_transient( 'notice' );
			}
		}
	}

	/**
	 * Show Custom notice/ offers/ promotions
	 *
	 * @since 1.0.0
	 */
	public function show_custom_notices() {


	}

	/**
	 * Update admin footer text
	 *
	 * @param $footer_text
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 *
	 */
	public function update_admin_footer_text( $footer_text ) {

		// Update Footer admin only on ES pages
		if ( Helper::is_plugin_admin_screen() ) {

			$wordpress_url = 'https://www.wordpress.org';
			$website_url   = 'https://www.kaizencoders.com';

			/* Translators: 1. WordPress URL, 2. Social Linkz Plugin Version, 3. KaizenCoders Website URL. */
			$footer_text = sprintf( __( '<span id="">Thank you for creating with <a href="%1$s" target="_blank">WordPress</a> | Social Linkz <b>%2$s</b>. Developed by team <a href="%3$s" target="_blank">KaizenCoders</a></span>',
				'social-linkz' ), $wordpress_url, KC_SL_PLUGIN_VERSION, $website_url );
		}

		return $footer_text;
	}

	/**
	 * Update plugin notice
	 *
	 * @param $response
	 *
	 * @param $data
	 *
	 * @since 1.8.4.1
	 *
	 */
	public function in_plugin_update_message( $data, $response ) {

		if ( isset( $data['upgrade_notice'] ) ) {
			printf(
				'<div class="update-message">%s</div>',
				wpautop( $data['upgrade_notice'] )
			);
		}
	}

	/**
	 * Register blocks.
	 *
	 * @return void
	 *
	 * @since 1.8.6
	 */
	public function register_blocks() {

		register_block_type(
			'social-linkz/share',
			[
				'style'           => 'social-linkz',
				'render_callback' => [ '\KaizenCoders\SocialLinkz\Helper', 'share_block' ],
			]
		);

	}

	/**
	 * Enqueue Block Editor Assets.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		// Main CSS.
		\wp_enqueue_style(
			'social-linkz',
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/social-linkz.css',
			[],
			$this->plugin->get_version(),
			'all' );

		\wp_enqueue_style(
			'social-linkz-blocks',
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/blocks.css',
			[ 'wp-edit-blocks' ],
			$this->plugin->get_version(),
			'all' );

		// Share block.
		wp_enqueue_script( 'social-linkz-blocks-share-js',
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/blocks-share.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n' ],
			$this->plugin->get_version());

		\wp_enqueue_style(
			'social-linkz-share-style',
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/share-style.css',
			[],
			$this->plugin->get_version(),
			'all' );

		wp_localize_script( 'social-linkz-blocks-share-js', 'socialLinkz', Helper::tinymce_localized_settings() );

	}
}
