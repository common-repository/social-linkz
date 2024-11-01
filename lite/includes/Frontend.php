<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/Frontend
 */

namespace KaizenCoders\SocialLinkz;

use KaizenCoders\SocialLinkz\Models\Link;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/Frontend
 * @author     Your Name <email@example.com>
 */
class Frontend {

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined in that particular
		 * class.
		 *
		 * The Loader will then create the relationship between the defined
		 * hooks and the functions defined in this class.
		 */

		\wp_enqueue_style(
			$this->plugin->get_plugin_name(),
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/social-linkz.css',
			[],
			$this->plugin->get_version(),
			'all' );


		\wp_enqueue_style(
			'social-linkz-share-style',
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/styles/share-style.css',
			[],
			$this->plugin->get_version(),
			'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined in that particular
		 * class.
		 *
		 * The Loader will then create the relationship between the defined
		 * hooks and the functions defined in this class.
		 */

		\wp_enqueue_script(
			$this->plugin->get_plugin_name(),
			\plugin_dir_url( dirname( __FILE__ ) ) . 'dist/scripts/social-linkz.js',
			[ 'jquery' ],
			$this->plugin->get_version(),
			false );

	}

	/**
	 * Render Inline social share content.
	 *
	 * @since 1.8.0
	 *
	 * @param $direct
	 *
	 * @param $content
	 *
	 * @return mixed|string
	 *
	 */
	function render_inline_content( $content, $direct = false ) {
		global $wp_current_filter;

		$settings = KC_SL()->get_settings();

		$inline_settings = Helper::get_data( $settings, 'inline', [] );

		$show_in_feeds = Helper::get_data( $inline_settings, 'display|show_in_feeds', 0 );

		// Bail if the_content is requested by someone else.
		if ( ! empty( $wp_current_filter ) && is_array( $wp_current_filter ) ) {
			if ( count( array_intersect( $wp_current_filter,
					[ 'wp_head', 'get_the_excerpt', 'widget_text_content', 'p3_content_end' ] ) ) > 0 ) {
				return $content;
			}

			// Nested the_content hook.
			if ( empty( $show_in_feeds ) || ! in_the_loop() ) {
				$filter_counts = array_count_values( $wp_current_filter );
				if ( ! empty( $filter_counts['the_content'] ) && $filter_counts['the_content'] > 1 ) {
					return $content;
				}
			}
		}

		if ( ! is_singular() && ( empty( $show_in_feeds ) || ! in_the_loop() ) ) {
			return $content;
		}

		$button_position = Helper::get_data( $inline_settings, 'display|button_position', '' );
		if ( ! empty( $button_position ) && $button_position == 'do_not_add_to_content' && ! $direct ) {
			return $content;
		}

		$inline_share_enabled = Helper::get_data( $inline_settings, 'general|enable_content', false );

		if ( ! empty( $inline_share_enabled ) ) {
			global $post;

			$post_types = Helper::get_data( $inline_settings, 'display|post_types', [] );

			if ( ( ! empty( $post_types ) && in_array( $post->post_type, $post_types ) ) || $direct ) {
				// TODO: Do the post level settings nad inherit post settings.

				if ( ! $direct && is_singular() && ! in_the_loop() ) {
					return $content;
				}

				$output = "";

				// Inline Styles.
				$output .= Helper::print_inline_styles( 'inline', $inline_settings );

				$output .= Helper::print_buttons( 'inline' );

				// Add output to the content.
				if ( ! empty( $output ) ) {

					$inline_position = apply_filters( 'kc_sl_inline_position', $button_position );
					if ( ! empty( $inline_position ) ) {
						// Add below content class.
						$below_output = str_replace( 'social-linkz-buttons social-linkz-inline',
							'social-linkz-buttons social-linkz-inline social-linkz-inline-below',
							$output );

						if ( $inline_position == 'above_content' ) {
							$content = $output . $content;
						} elseif ( $inline_position == 'below_content' ) {
							$content = $content . $below_output;
						} elseif ( $inline_position == 'above_and_below_content' ) {
							$content = $output . $content . $below_output;
						}
					} else {
						$content = $output . $content;
					}
				}
			}
		}

		return $content;
	}
}
