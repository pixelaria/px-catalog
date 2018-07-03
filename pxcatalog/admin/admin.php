<?php
if ( ! class_exists( 'PX_Library' ) ) {
  require_once PX_CATALOG_DIR . '/admin/includes/class-px-library.php';
}


/* Добавляем функционал бэкенда */
add_action('admin_menu', 'mt_add_pages');
add_action('admin_enqueue_scripts', 'do_customs' );

function do_customs() {
  wp_enqueue_style( 'px_catalog_css',PX_CATALOG_DIR. '/admin/css/plugin.css',array(), "1.0.0", 'all' );
  wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js', false, '1.4.4');
  wp_enqueue_script('jquery');
}

function mt_add_pages() {
  // Добавляем пункт пеню слева
  add_object_page( 'px-catalog',
    'Каталог',
    8, 'px-catalog',
    'px_catalog_main', 'dashicons-email' );

  $catagory=add_submenu_page('px-catalog', 
    'Категории','Категории', 
    8,'px-catalog-cats',
    'px_category_page');

  
  $product=add_submenu_page('px-catalog', 
    'Товары', 'Товары', 
    8, 'px-product', 
    'px_product_page');


  //добавляем экшены
  add_action( 'load-' . $catagory, 'px_catalog_cats_prepare');
  add_action( 'load-' . $product, 'px_product_prepare');
}


function px_catalog_main() {
  echo '<h2 clas="test">Каталог вакансий и резюме</h2>';
}

//вызывается перед формированием страницы, при создании\редактировании редиректит нас на страницу категорий
function px_catalog_cats_prepare() {
  $px_library=new PX_Library();
  
  $action=$_REQUEST['action'];
  if ($action == 'save') { //если у нас добавление новой или сохранение
    $id = $_POST['cat'];
    
    if ($id==-1) {
      $message='created';
      $catInfo['status']=1;
    } else {
      $mesage='saved';
      $catInfo['status']=$_POST['status'];
    }

    //фомрируем информацию о категории
    $catInfo['id']=$_POST['cat'];
    $catInfo['name']=$_POST['name'];
    $catInfo['desc']=$_POST['desc'];
    
    $id=$px_library->saveCategory($catInfo); //сохраняем
    
    $query = array(
      'message' => $mesage
    );
    $redirect_to = add_query_arg( $query, menu_page_url( 'px-catalog-cats', false ));
    wp_safe_redirect($redirect_to);
    exit();
  } else if ($action == 'delete') {
    $cat=$_REQUEST['cat']; //получаем идентификатор категории
    $px_library->deleteCategory($cat);

    $query = array(
      'message' => $mesage
    );
    $redirect_to = add_query_arg( $query, menu_page_url( 'px-catalog-cats', false ));
    wp_safe_redirect($redirect_to);
  } else if ($action == 'publish') {
    $id = $_REQUEST['cat'];  
    $px_library->setCatStatus(1,$id); 
     $query = array(
      'message' => 'published'
    );
    $redirect_to = add_query_arg( $query, menu_page_url( 'px-catalog-cats', false ));
    wp_safe_redirect($redirect_to);
  } else if ($action == 'unpublish') {
    $id = $_REQUEST['cat'];  
    $px_library->setCatStatus(0,$id);
     $query = array(
      'message' => 'unpublished'
    );
    $redirect_to = add_query_arg( $query, menu_page_url( 'px-catalog-cats', false ));
    wp_safe_redirect($redirect_to); 
  }
}

function px_category_page() {
  $px_library=new PX_Library();
  $action=$_REQUEST['action'];
  if ($action == 'add') {
    $cat=-1;
    include PX_CATALOG_DIR . '/admin/includes/category-form.php';
  } else if ($action == 'edit') {
    $cat=$_REQUEST['cat']; //получаем идентификатор категории
    $catInfo=$px_library->getCategoryInfo($cat); //получаем информацию о категории
    include PX_CATALOG_DIR . '/admin/includes/category-form.php';
  } else {
    echo '<h2> Категории';
    echo ' <a href="' . esc_url(menu_page_url( 'px-catalog-cats', false )."&action=add") . '" class="add-new-h2">' . esc_html( __( 'Add New', 'contact-form-7' ) ) . '</a>';
    echo '</h2>';
    
    if ($message) add_message(); 
    if ( ! class_exists( 'PX_Catalog_Categories_Table' ) ) {
      require_once PX_CATALOG_DIR . '/admin/includes/class-catalog-categories-table.php';
    }
    global $myListTable;
    $option = 'per_page';
    $args = array(
      'label' => 'Категории',
      'default' => 10,
      'option' => 'books_per_page'
    );
    add_screen_option( $option, $args );
    $table = new PX_Catalog_Categories_Table();
    $table->set_items($px_library->getCategories());
    $table->prepare_items();
    $table->display();
  }
}

function px_product_prepare() {
  $px_library=new PX_Library();
  
  $action=$_REQUEST['action'];
  if ($action == 'save') { //если у нас добавление новой или сохранение

    $id = $_POST['product'];

    if ($id==-1) {
      $message='created';
      $catInfo['status']=1;
    } else {
      $mesage='saved';
      $catInfo['status']=$_POST['status'];
    }
    
    //фомрируем информацию о резюме
    $product_info = array(
      'id' => $_POST['product'],
      'name' => $_POST['uname'],
      'desc' => $_POST['desc'],
      'category' => $_POST['category'],
      'category' => $_POST['category'],
      'status' => $_POST['status'],
      'date' => $_POST['date']
    );
    
    $id=$px_library->saveProduct($product_info); //сохраняем
    
    $query = array(
      'message' => $mesage
    );

     $redirect_to = add_query_arg( $query, menu_page_url( 'px-product', false ));
     wp_safe_redirect($redirect_to);
     exit();

  } else if ($action == 'delete') {
    $id = $_REQUEST['product'];  
    
    $px_library->deleteProduct($id);

    $query = array(
      'message' => 'deleted'
    );
    $redirect_to = add_query_arg( $query, menu_page_url( 'px-product', false ));
    wp_safe_redirect($redirect_to);
    exit();

  } else if ($action == 'publish') {
    $id = $_REQUEST['product'];  
    $px_library->setProductStatus(1,$id); 
     $query = array(
      'message' => 'published'
    );
    $redirect_to = add_query_arg( $query, menu_page_url( 'px-product', false ));
    wp_safe_redirect($redirect_to);

  } else if ($action == 'unpublish') {
    $id = $_REQUEST['product'];  
    $px_library->setProductStatus(0,$id);
     $query = array(
      'message' => 'unpublished'
    );
    $redirect_to = add_query_arg( $query, menu_page_url( 'px-product', false ));
    wp_safe_redirect($redirect_to); 
  }
}

function px_product_page() {
  wp_enqueue_media();

  $px_library=new PX_Library();
  $action=$_REQUEST['action'];
  
  $categories=$px_library->getCategories();
  
  if ($action == 'add') {
    $product=-1;
    include PX_CATALOG_DIR . '/admin/includes/product-form.php';
  } else if ($action == 'edit') {
    $product=$_REQUEST['product']; //получаем идентификатор категории
    $data=$px_library->getProductInfo($product); //получаем информацию о категории
    include PX_CATALOG_DIR . '/admin/includes/product-form.php';
  } else {
    echo '<h2> Продукт';
    echo ' <a href="' . esc_url(menu_page_url( 'px-product', false )."&action=add") . '" class="add-new-h2">Добавить новый продукт</a>';
    echo '</h2>';
    echo '<form  method="get">';
    $px_library=new PX_Library();
    if (!class_exists( 'PX_Catalog_Product_Table' ) ) {
      require_once PX_CATALOG_DIR . '/admin/includes/class-catalog-products-table.php';
    } 
    $table = new PX_Catalog_Products_Table();
    $table->set_items($px_library->getProducts());
    $table->prepare_items();
    $table->display();
    echo '</form>';
  }
}

function add_message() {
  if ($message=='saved') $msg='Категория сохранена';
  else if ($message=='created') $msg='Категория создана';
  echo '<div class="updated notice notice-success is-dismissible below-h2" id="message">
    <p>'.$msg.'</p>
    <button class="notice-dismiss" type="button">
      <span class="screen-reader-text">Скрыть это уведомление.</span>
    </button>
  </div>';
}


?>