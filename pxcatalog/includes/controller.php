<?php
if ( ! class_exists( 'PX_Library' ) ) {
		require_once PX_CATALOG_DIR . '/admin/includes/class-px-library.php';
}
require_once PX_CATALOG_DIR . '/admin/includes/recaptchalib.php';

//бъявляем шорткоды
add_shortcode( 'pxproductform', 'px_product_form' ); //шорткод формы добавления резюме
add_shortcode( 'pxproductlist', 'px_product_list' ); //шорткод списка резюме (все или по категории)
add_shortcode( 'pxproduct', 'px_product' ); //шорткод резюме по id
add_shortcode( 'pxcategories', 'px_categories' );


function px_plugin_rules() {
	// Правило перезаписи
	add_rewrite_rule( '^pxcatalog/([^/]*)/([^/]*)/?', 'index.php?page_id=111&type=$matches[1]&id=$matches[2]', 'top' );
	add_rewrite_rule( '^pxcatalog/([^/]*)/?', 'index.php?page_id=111&type=$matches[1]', 'top' );
	// скажем WP, что есть новые параметры запроса
	add_filter( 'query_vars', function( $vars ){
		$vars[] = 'type';
		$vars[] = 'id';
		return $vars;
	} );
	flush_rewrite_rules();
}

add_action('init', 'px_plugin_rules');

//Форма добавения резюме
function px_product_form( $atts ){
	$px_library=new PX_Library();
	$categories=$px_library->getCategories();
	$type=true; //означает фронт
	$action="add"; //означает добавление
	$data=add_product();
	$id=$data['id'];
	if (!$type) wp_enqueue_media();
	include PX_CATALOG_DIR . '/admin/includes/product-form.php';
}

function add_product() {
	$px_library=new PX_Library();

	//фомрируем информацию о резюме
	$productInfo = array(
			'id' => -1,
			'name' => $_POST['uname'],
			'description' => $_POST['description'],
			'photo' => $_POST['photo'],
			'category' => $_POST['category'],
			'status' => 0
	);
	
	if ($_POST['action']) {
		$secret = "6Lcm8BQUAAAAADl2FI_CDmSXVqhWd9cWtM7IbcpW";
  	$response = null;
  	$reCaptcha = new ReCaptcha($secret);
  	
    // if submitted check response
  	if ($_POST["g-recaptcha-response"]) {
  	  $response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],$_POST["g-recaptcha-response"]);
    }

  	if ($response != null && $response->success) {
  			$id=$px_library->saveProduct($productInfo); //сохраняем
  			$_POST=array();
  			$productInfo=array();
  			$productInfo['id']=$id;
  		} else {
  			$productInfo['error'] = 'Каптча';
  		}
  	}
	return $productInfo;
}




//Список доступных 
function px_product_list($atts) {
	$px_library=new PX_Library();
	
  extract( shortcode_atts( array(
		'category' => -1
	), $atts ) );
	
	if ($category===-1) { // все категории
		$products=$px_library->getProducts();
		$result.= '<h3 class="cards_name">Все товары</h3>';
	} else { //конеретная категория
		$products=$px_library->getProducts($category,1);
		$catInfo=$px_library->getCategoryInfo($category);
	}
	if ($products) {
		foreach ($products as $product ) {
			$result.='<div class="product">';
			$result.='<div class="product__thumb">'.wp_get_attachment_image( $product['photo'], 'thumbnail' ).'</div>';
			$result.= '<div class="product__desc">';
			$result.= '<a href="/pxcatalog/product/'.$product['id'].'">'.$product['name'].'</a>';
			$result.= '</div>';
			$result.='</div>';
		}
	} else {
		$result.= '<p>Нет товаров</p>';
	}
	return $result;
}

function px_product($atts) {
	$px_library=new PX_Library();
	extract( shortcode_atts( array(
		'id' => -1
	), $atts ) );

	$productInfo=$px_library->getProductInfo($id);
	
  echo "Product info";
}

//Список кактегорий
function px_categories($atts) {
	$px_library=new PX_Library();
	extract( shortcode_atts( array(
		'type' => 'products'
	), $atts ) );


	$categories=$px_library->getCategories();
	
  $result.='<div class="categories">';
	foreach ($categories as $cat) {
		$result.= '<div class="category">
			<a href="/pxcatalog/'.$type.'/'.$cat['id'].'/">
				<span><img src="http://www.prisluga.spb.ru/images/stories/folder_orange.png" alt=""></span>
				<span>'.$cat['name'].'</span>
			</a>
		</div>';
	}

	$result.='</div>';
	return $result;
}
?>