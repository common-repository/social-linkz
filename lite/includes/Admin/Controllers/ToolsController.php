<?php

namespace KaizenCoders\SocialLinkz\Admin\Controllers;

use KaizenCoders\SocialLinkz\Helper;

class ToolsController extends BaseController {
	/**
	 * ToolsController constructor.
	 *
	 * @since 1.1.9
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Render Tools
	 *
	 * @since 1.8.5
	 */
	public function render() {
		$template_data['links'] = $this->get_tabs();

		include_once KC_SL_ADMIN_TEMPLATES_DIR . '/tools.php';
	}

	/**
	 * Get links
	 *
	 * @since 1.8.5
	 */
	public function get_tabs() {
		$tabs['awesome-products'] = [
			'title' => __( 'Other Awesome Products', 'social-linkz' ),
			'link'  => add_query_arg( [ 'tab' => 'awesome-products' ], admin_url( 'admin.php?page=social-linkz-tools' ) ),
		];

		return $tabs;
	}

}
