<?php
/*
Plugin Name: PX Catalog
Plugin URI: https://pixelaria.ru/
Description: Плагин-каталог элементов
Version: 1.0
Author: Pixelaria
Author URI: http://pixelaria.ru
*/

define( 'PX_CATALOG', __FILE__ );
define( 'PX_CATALOG_DIR', untrailingslashit( dirname( PX_CATALOG ) ) );

class px_catalog {
	function px_catalog() {
		if ( is_admin() ) {
		require_once PX_CATALOG_DIR . '/admin/admin.php';
		} else {
			require_once PX_CATALOG_DIR . '/includes/controller.php';
		}
	}
}

global $px_catalog;
$px_catalog = new px_catalog();
?>