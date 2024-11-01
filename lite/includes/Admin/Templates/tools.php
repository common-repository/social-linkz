<?php

use KaizenCoders\SocialLinkz\Helper;

$nav_menus = Helper::get_data( $template_data, 'links', [] );

$tab = ! empty( $_GET['tab'] ) ? Helper::clean( $_GET['tab'] ) : 'awesome-products';


?>

<div class="wrap">
    <h2>Tools</h2>
    <h2 class="nav-tab-wrapper">
		<?php foreach ( $nav_menus as $id => $menu ) { ?>
            <a href="<?php echo $menu['link']; ?>" class="nav-tab wpsf-tab-link <?php if ( $id === $tab ) {
				echo "nav-tab-active";
			} ?>">
				<?php echo $menu['title']; ?>
            </a>
		<?php } ?>
    </h2>

    <div class="bg-white shadow-md meta-box-sortables">
		<?php

		if ( 'awesome-products' === $tab ) {
			include_once KC_SL_ADMIN_TEMPLATES_DIR . '/other-products.php';
		} ?>
    </div>

</div>
