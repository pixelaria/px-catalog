<?php
if ( ! class_exists( 'PX_Library' ) ) {
    require_once PX_CATALOG_DIR . '/admin/includes/class-px-library.php';
}


/* Добавляем функционал бэкенда */
add_action('admin_menu', 'mt_add_pages');
add_action('admin_enqueue_scripts', 'do_customs' );

function do_customs() {
    wp_enqueue_style( 'px_catalog_css',
        PX_CATALOG_DIR. '/admin/css/plugin.css',
        array(), "1.0.0", 'all' );
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

    $vacancy=add_submenu_page('px-catalog', 
        'Вакансии', 'Вакансии', 
        8, 'px-vacancy', 
        'px_vacancy_page');

    $vcard=add_submenu_page('px-catalog', 
        'Резюме', 'Резюме', 
        8, 'px-vcard', 
        'px_vcard_page');


    //добавляем экшены
    add_action( 'load-' . $catagory, 'px_catalog_cats_prepare');
    add_action( 'load-' . $vcard, 'px_vcard_prepare');
    add_action( 'load-' . $vacancy, 'px_vacancy_prepare');
    
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

function px_vcard_prepare() {
    $px_library=new PX_Library();
    
    $action=$_REQUEST['action'];
    if ($action == 'save') { //если у нас добавление новой или сохранение

        $id = $_POST['vcard'];

        if ($id==-1) {
            $message='created';
            $catInfo['status']=1;
        } else {
            $mesage='saved';
            $catInfo['status']=$_POST['status'];
        }
        //заполняем NULL вместо ''
        if ($_POST['marital'] === '-1') {$_POST['marital'] = 'NULL';} 
        if ($_POST['childs'] === '-1') {$_POST['childs'] = 'NULL';}  

        //фомрируем информацию о резюме
        $vcardInfo = array(
            'id' => $_POST['vcard'],
            'name' => $_POST['uname'],
            'photo' => $_POST['photo'],
            'phone' => $_POST['uphone'],
            'email' => $_POST['uemail'],
            'address' => $_POST['address'],
            'category' => $_POST['category'],
            'experience' => $_POST['experience'],
            'sex' => $_POST['sex'],
            'age' => $_POST['age'],
            'marital' => $_POST['marital'],
            'childs' => $_POST['childs'],
            'roadtime' => $_POST['roadtime'],
            'worktime' => $_POST['worktime'],
            'status' => $_POST['status'],
            'date' => $_POST['date'],
            'edu' => $_POST['edu'],

            'languages' => $_POST['languages'],
            'skills' => $_POST['skills'],
            'drive' => $_POST['drive'],
            'salary' => $_POST['salary'],
            'sport' => $_POST['sport'],
            'attitude' => $_POST['attitude'],
            'hobby' => $_POST['hobby']
        );
        
        $id=$px_library->saveVCard($vcardInfo); //сохраняем
        
        $query = array(
            'message' => $mesage
        );
       $redirect_to = add_query_arg( $query, menu_page_url( 'px-vcard', false ));
       wp_safe_redirect($redirect_to);
       exit();
    } else if ($action == 'delete') {
        $id = $_REQUEST['vcard'];    
        
        $px_library->deleteVcard($id);

        $query = array(
            'message' => 'deleted'
        );
        $redirect_to = add_query_arg( $query, menu_page_url( 'px-vcard', false ));
        wp_safe_redirect($redirect_to);
        exit();
    } else if ($action == 'publish') {
        $id = $_REQUEST['vcard'];    
        $px_library->setVcardStatus(1,$id); 
         $query = array(
            'message' => 'published'
        );
        $redirect_to = add_query_arg( $query, menu_page_url( 'px-vcard', false ));
        wp_safe_redirect($redirect_to);
    } else if ($action == 'unpublish') {
        $id = $_REQUEST['vcard'];    
        $px_library->setVcardStatus(0,$id);
         $query = array(
            'message' => 'unpublished'
        );
        $redirect_to = add_query_arg( $query, menu_page_url( 'px-vcard', false ));
        wp_safe_redirect($redirect_to); 
    }
}

function px_vcard_page() {
    wp_enqueue_media();

    $px_library=new PX_Library();
    $action=$_REQUEST['action'];
    
    $roadtime=$px_library->get_roadtime();
    $categories=$px_library->getCategories();
    $educations=$px_library->getEducations();
    
    if ($action == 'add') {
        $vcard=-1;
        include PX_CATALOG_DIR . '/admin/includes/vcard-form.php';
    } else if ($action == 'edit') {
        $vcard=$_REQUEST['vcard']; //получаем идентификатор категории
        $data=$px_library->getVCardInfo($vcard); //получаем информацию о категории
        
        include PX_CATALOG_DIR . '/admin/includes/vcard-form.php';
    } else {
        echo '<h2> Резюме';
        echo ' <a href="' . esc_url(menu_page_url( 'px-vcard', false )."&action=add") . '" class="add-new-h2">Добавить новое резюме</a>';
        echo '</h2>';
        echo '<form  method="get">';
        $px_library=new PX_Library();
        if (!class_exists( 'PX_Catalog_VCards_Table' ) ) {
            require_once PX_CATALOG_DIR . '/admin/includes/class-catalog-vcards-table.php';
        } 
        $table = new PX_Catalog_VCards_Table();
        $table->set_items($px_library->getVCards());
        $table->prepare_items();
        $table->display();
        echo '</form>';
    }
}

function add_new_link() {
   
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


function px_vacancy_prepare() {
    $px_library=new PX_Library();
    $action=$_REQUEST['action'];
    if ($action == 'save') { //если у нас добавление новой или сохранение
        $id = $_POST['vacancy'];    

        if ($id==-1) {
            $message='created';
            $catInfo['status']=1;
        } else {
            $mesage='saved';
            $catInfo['status']=$_POST['status'];
        }
     
        //заполняем NULL вместо ''
        if ($_POST['sex'] === '') { $_POST['sex'] = 'NULL'; }
        if ($_POST['marital'] === '') {$_POST['marital'] = 'NULL';} 
        if ($_POST['childs'] === '') {$_POST['childs'] = 'NULL';}   
        if ($_POST['roads'] === '') {$_POST['roads'] = 'NULL';} 
        if ($_POST['food'] === '') {$_POST['food'] = 'NULL'; }
        if ($_POST['overtime'] === '') {$_POST['overtime'] = 'NULL'; }
        if ($_POST['holiday'] === '') {$_POST['holiday'] = 'NULL'; }

        //фомрируем информацию о вакансии
        $vacancyInfo = array(
            'id' => $_POST['vacancy'],
            'title' => $_POST['utitle'],
            'name' => $_POST['uname'],
            'phone' => $_POST['uphone'],
            'email' => $_POST['uemail'],
            'deadline' => $_POST['deadline'],
            'category' => $_POST['category'],
            'sex' => $_POST['sex'],
            'age' => $_POST['age'],
            'district' => $_POST['district'],
            'marital' => $_POST['marital'],
            'childs' => $_POST['childs'],
            'adds' => $_POST['adds'],
            'salary' => $_POST['salary'],
            'mode' => $_POST['mode'],
            'roads' => $_POST['roads'],
            'languages' => $_POST['languages'],
            'sport' => $_POST['sport'],
            'skills' => $_POST['skills'],
            'drive' => $_POST['drive'],
            'food' => $_POST['food'],
            'holiday' => $_POST['holiday'],
            'exp' => $_POST['exp'],
            'overtime' => $_POST['overtime'],
            'status' => $_POST['status'],
            'date' => $_POST['date']
        );
        
        $id=$px_library->saveVacancy($vacancyInfo); //сохраняем
        
        $query = array(
            'message' => $mesage
        );
       $redirect_to = add_query_arg( $query, menu_page_url( 'px-vacancy', false ));
       wp_safe_redirect($redirect_to);
       exit();
    } else if ($action == 'delete') {
        $id = $_REQUEST['vacancy'];    
        
        $px_library->deleteVacancy($id);

        $query = array(
            'message' => 'deleted'
        );
        $redirect_to = add_query_arg( $query, menu_page_url( 'px-vacancy', false ));
        wp_safe_redirect($redirect_to);
        exit();
    } else if ($action == 'publish') {
        $id = $_REQUEST['vacancy'];    
        $px_library->setVacancyStatus(1,$id); 
         $query = array(
            'message' => 'published'
        );
        $redirect_to = add_query_arg( $query, menu_page_url( 'px-vacancy', false ));
        wp_safe_redirect($redirect_to);
        exit();
    } else if ($action == 'unpublish') {
        $id = $_REQUEST['vacancy'];    
        $px_library->setVacancyStatus(0,$id);
         $query = array(
            'message' => 'unpublished'
        );
        $redirect_to = add_query_arg( $query, menu_page_url( 'px-vacancy', false ));
        wp_safe_redirect($redirect_to);
        exit(); 
    }
}


function px_vacancy_page() {
    $px_library=new PX_Library();
    $action=$_REQUEST['action'];

    $ages=$px_library->get_ages();
    $deadlines=$px_library->get_deadlines();
    $categories=$px_library->getCategories();
    
    if ($action == 'add') {
        $vacancy=-1;
        include PX_CATALOG_DIR . '/admin/includes/vacancy-form.php';
    } else if ($action == 'edit') {
        $vacancy=$_REQUEST['vacancy']; //получаем идентификатор категории
        $data=$px_library->getVacancyInfo($vacancy); //получаем информацию о категории
        include PX_CATALOG_DIR . '/admin/includes/vacancy-form.php';
    } else {
        echo '<h2> Вакансии';
        echo ' <a href="' . esc_url(menu_page_url( 'px-vacancy', false )."&action=add") . '" class="add-new-h2">Добавить новую вакансию</a>';
        echo '</h2>';
        echo '<form  method="get">';
        if (!class_exists( 'PX_Catalog_Vacancy_Table' ) ) {
            require_once PX_CATALOG_DIR . '/admin/includes/class-catalog-vacancy-table.php';
        } 
        
        $table = new PX_Catalog_Vacancy_Table();
        $table->set_items($px_library->getVacancies());
        $table->prepare_items();
        $table->display();
        echo '</form>';
    }
}

?>