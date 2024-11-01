<?php
/**
 * WordPress Settings Framework
 *
 * @author  Gilbert Pellegrom, James Kemp
 * @link    https://github.com/gilbitron/WordPress-Settings-Framework
 * @license MIT
 */

use KaizenCoders\SocialLinkz\Helper;
/**
 * Define your settings
 *
 * The first parameter of this filter should be wpsf_register_settings_[options_group],
 * in this case "my_example_settings".
 *
 * Your "options_group" is the second param you use when running new WordPressSettingsFramework()
 * from your init function. It's important as it differentiates your options from others.
 *
 * To use the tabbed example, simply change the second param in the filter below to 'wpsf_tabbed_settings'
 * and check out the tabbed settings function on line 156.
 */

add_filter( 'wpsf_register_settings_kc_sl', 'kc_sl_wpsf_tabbed_settings' );


/**
 * Tabbed example
 */
function kc_sl_wpsf_tabbed_settings( $wpsf_settings ) {

	$settings = KC_SL()->get_settings();

	// Tabs
	$tabs = [
		[
			'id'    => 'inline',
			'title' => __( 'Inline Content', 'social-linkz' ),
		],

		[
			'id'    => 'configuration',
			'title' => __( 'Configuration', 'social-linkz' ),
		],
	];

	$wpsf_settings['tabs'] = apply_filters( 'kc_sl_filter_settings_tab', $tabs );

	$inline_options = [
		[
			'id'      => 'enable_content',
			'title'   => __( 'Enable Inline Content', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'switch',
			'default' => 0,
		],

		[
			'id'      => 'social_networks',
			'title'   => __( 'Social Networks', 'social-linkz' ),
			'desc'    => '',
			'default' => 0,
			'type'    => 'social_networks',
			'choices' => Helper::get_social_networks(),
		],

	];

	$inline_options = apply_filters( 'kc_sl_filter_inline_options', $inline_options );

	$display_options = [
		[
			'id'      => 'post_types',
			'title'   => __( 'Post Types', 'social-linkz' ),
			'desc'    => '',
			'default' => [],
			'type'    => 'checkboxes',
			'choices' => Helper::get_all_cpts(),
		],
	];

	$display_options = apply_filters( 'kc_sl_filter_inline_content_display_options', $display_options );

	$design_options = [
		[
			'id'      => 'button_style',
			'title'   => __( 'Button Style', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'select',
			'default' => '',
			'choices' => Helper::get_inline_button_styles()
		],

		[
			'id'      => 'button_layout',
			'title'   => __( 'Button Layout', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'select',
			'default' => '',
			'choices' => Helper::get_inline_button_layouts()
		],

		[
			'id'      => 'button_alignment',
			'title'   => __( 'Button Alignment', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'select',
			'default' => '',
			'choices' => Helper::get_inline_button_alignments()
		],

		[
			'id'      => 'button_size',
			'title'   => __( 'Button Size', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'select',
			'default' => '',
			'choices' => Helper::get_inline_button_size()
		],

		[
			'id'      => 'button_shape',
			'title'   => __( 'Button Shape', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'select',
			'default' => '',
			'choices' => Helper::get_inline_button_shapes()
		],

		[
			'id'      => 'button_color',
			'title'   => __( 'Button Color', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'color',
			'default' => '',
		],

		[
			'id'      => 'button_hover_color',
			'title'   => __( 'Button Hover Color', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'color',
			'default' => '',
		],

		[
			'id'      => 'icon_color',
			'title'   => __( 'Icon Color', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'color',
			'default' => '',
		],

		[
			'id'      => 'icon_hover_color',
			'title'   => __( 'Icon Hover Color', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'color',
			'default' => '',
		],

		[
			'id'      => 'inverse_on_hover',
			'title'   => __( 'Inverse On Hover', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'switch',
			'default' => 0,
		],

		[
			'id'      => 'button_margin',
			'title'   => __( 'Button Margin', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'text',
			'default' => '10px',
		],
	];

	$design_options = apply_filters( 'kc_sl_filter_inline_content_design_options', $design_options );

	// Sections.
	$inline_sections = [
		[
			'tab_id'        => 'inline',
			'section_id'    => 'general',
			'section_title' => __( 'General Options', 'social-linkz' ),
			'section_order' => 10,
			'fields'        => $inline_options,
		],

		[
			'tab_id'        => 'inline',
			'section_id'    => 'display',
			'section_title' => __( 'Display', 'social-linkz' ),
			'section_order' => 20,
			'fields'        => $display_options,
		],

		[
			'tab_id'        => 'inline',
			'section_id'    => 'design',
			'section_title' => __( 'Design', 'social-linkz' ),
			'section_order' => 30,
			'fields'        => $design_options,
		],

	];

	$inline_sections = apply_filters( 'kc_sl_filter_inline_settings_sections', $inline_sections );

	$configuration_options = [
		[
			'id'      => 'url_shortify',
			'title'   => __( 'Enable URL Shortify', 'social-linkz' ),
			'desc'    => '',
			'type'    => 'switch',
			'default' => 0,
		],
	];
	$configuration_sections = [
		[
			'tab_id'        => 'configuration',
			'section_id'    => 'link_shortening',
			'section_title' => __( 'Link Shortening', 'social-linkz' ),
			'section_order' => 30,
			'fields'        => $configuration_options,
		],
	];

	$sections = array_merge( [], $inline_sections, $configuration_sections );

	$sections = apply_filters( 'kc_sl_filter_settings_sections', $sections );

	$wpsf_settings['sections'] = $sections;

	return $wpsf_settings;
}