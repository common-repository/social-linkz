<?php

namespace KaizenCoders\SocialLinkz\Admin;

class Settings {

	/**
	 * @var string
	 */
	private $plugin_path;

	/**
	 * @var \KaizenCoders\SocialLinkz\Settings
	 */
	private $wpsf;

	/**
	 * WPSFTest constructor.
	 */
	public function __construct() {

		$this->plugin_path = plugin_dir_path( __FILE__ );

		$this->wpsf = new \KaizenCoders\SocialLinkz\Settings(  $this->plugin_path . '/admin-settings.php', 'kc_sl' );

		// Add admin menu
		add_action( 'admin_menu', array( $this, 'add_settings_page' ), 20 );

		// Add an optional settings validation filter (recommended)
		add_filter( $this->wpsf->get_option_group() . '_settings_validate', array( &$this, 'validate_settings' ) );
	}

	/**
	 * Add settings page.
	 */
	public function add_settings_page() {

		$this->wpsf->add_settings_page( [
			'parent_slug' => 'social-linkz',
			'page_title'  => __( 'Settings', 'social-linkz' ),
			'menu_title'  => __( 'Settings', 'social-linkz' ),
			'capability'  => 'manage_options',
		] );
	}

	/**
	 * Validate settings.
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function validate_settings( $input ) {
		// Do your settings validation here
		// Same as $sanitize_callback from http://codex.wordpress.org/Function_Reference/register_setting
		return $input;
	}
}
