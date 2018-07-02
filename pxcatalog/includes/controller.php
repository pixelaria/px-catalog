<?php
if ( ! class_exists( 'PX_Library' ) ) {
    require_once PX_CATALOG_DIR . '/admin/includes/class-px-library.php';
}
require_once PX_CATALOG_DIR . '/admin/includes/recaptchalib.php';

//бъявляем шорткоды
add_shortcode( 'pxvacancyform', 'px_vacancy_form' ); //щорткод формы добавления вакансии
add_shortcode( 'pxvcardform', 'px_vcard_form' ); //шорткод формы добавления резюме
add_shortcode( 'pxvacancylist', 'px_vacancy_list' ); //шорткод списка вакансий (все или по категории)
add_shortcode( 'pxvcardlist', 'px_vcard_list' ); //шорткод списка резюме (все или по категории)
add_shortcode( 'pxvacancy', 'px_vacancy' ); //шорткод вакансии по id
add_shortcode( 'pxvcard', 'px_vcard' ); //шорткод резюме по id
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



//Форма добавления вакансии 
function px_vacancy_form( $atts ) { 
	$px_library=new PX_Library();
	$ages=$px_library->get_ages();
  $deadlines=$px_library->get_deadlines();
  $categories=$px_library->getCategories();
	$type=true; //означает фронт
	$action="add"; //означает добавление
	$data=add_vacancy();
	$id=$data['id'];
	include PX_CATALOG_DIR . '/admin/includes/vacancy-form.php';
}

//Форма добавения резюме
function px_vcard_form( $atts ){
	$px_library=new PX_Library();
	$ages=$px_library->get_ages();
  $deadlines=$px_library->get_deadlines();
  $categories=$px_library->getCategories();
  $roadtime=$px_library->get_roadtime();
  $educations=$px_library->getEducations();

  $type=true; //означает фронт
	$action="add"; //означает добавление
	$data=add_vcard();
	$id=$data['id'];
	if (!$type) wp_enqueue_media();
	include PX_CATALOG_DIR . '/admin/includes/vcard-form.php';
}





function add_vcard() {
	$px_library=new PX_Library();

	//заполняем NULL вместо ''
    if ($_POST['marital'] === '-1') {$_POST['marital'] = 'NULL';} 
    if ($_POST['childs'] === '-1') {$_POST['childs'] = 'NULL';}   
    
	//фомрируем информацию о резюме
    $vcardInfo = array(
        'id' => -1,
        'name' => $_POST['uname'],
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
        'status' => 0,
        'edu' => $_POST['edu'],

        'languages' => $_POST['languages'],
        'skills' => $_POST['skills'],
        'drive' => $_POST['drive'],
        'salary' => $_POST['salary'],
        'sport' => $_POST['sport'],
        'attitude' => $_POST['attitude'],
        'hobby' => $_POST['hobby'],
    
    );
    
    if ($_POST['action']) {
    	$secret = "6Lcm8BQUAAAAADl2FI_CDmSXVqhWd9cWtM7IbcpW";
		$response = null;
		$reCaptcha = new ReCaptcha($secret);
	    // if submitted check response
		if ($_POST["g-recaptcha-response"]) {
		$response = $reCaptcha->verifyResponse(
		        $_SERVER["REMOTE_ADDR"],
		        $_POST["g-recaptcha-response"]
		    );
		}
		if ($response != null && $response->success) {
	    	$id=$px_library->saveVCard($vcardInfo); //сохраняем
	    	$_POST=array();
	    	$vcardInfo=array();
	    	$vcardInfo['id']=$id;
	    } else {
	    	$vcardInfo['error'] = 'Каптча';
	    }
    }
    return $vcardInfo;
}

function add_vacancy() {
	$px_library=new PX_Library();
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
        'id' => -1,
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
        'food' => $_POST['food'],
        'holiday' => $_POST['holiday'],
        'exp' => $_POST['exp'],
        'overtime' => $_POST['overtime'],
        'status' => 0
    );

    $secret = "6Lcm8BQUAAAAADl2FI_CDmSXVqhWd9cWtM7IbcpW";
	$response = null;
	$reCaptcha = new ReCaptcha($secret);
 
    // if submitted check response
	if ($_POST["g-recaptcha-response"]) {
	$response = $reCaptcha->verifyResponse(
	        $_SERVER["REMOTE_ADDR"],
	        $_POST["g-recaptcha-response"]
	    );
	}
	if ($response != null && $response->success) {
    	$id=$px_library->saveVacancy($saveVacancy); //сохраняем
    	$_POST=array();
    	$saveVacancy=array();
    } else {
    	$saveVacancy['error'] = 'Каптча';
    }
    $saveVacancy['id']=$id;
    return $saveVacancy;
}

//Список доступных вакансий (все или по категории)
function px_vacancy_list($atts) {
	$px_library=new PX_Library();
	extract( shortcode_atts( array(
		'category' => -1
	), $atts ) );
	$result.= '<div class="entry-content">';
	if ($category===-1) { // все категории
		$result.= '<h1 class="cards_name">Все категории</h1>';
		$vacancies=$px_library->getVacancies();
	} else { //конкретная категория
		//$catInfo=$px_library->getCategoryInfo($category);
		$vacancies=$px_library->getVacancies($category,1);
		//$result.= $catInfo['desc'];
	}
	if ($vacancies) {	
		$ages=$px_library->get_ages();	
		foreach ($vacancies as $vacancy ) {
			$result.='<div class="vacancy">';
			$result.= '<div class="vacancy__desc">';
			$result.= '<a href="/pxcatalog/vacancy/'.$vacancy['id'].'">'.$vacancy['title'].'</a>';
			if ($vacancy['salary']) $result.= '<p><span>Зарплата: </span>'.$vacancy['salary'].'</p>';
			if ($vacancy['age']) $result.= '<p><span>Возраст: </span>'.$ages[$vacancy['age']].'</p>';
			if ($vacancy['sex']) $result.= '<p><span>Пол: </span>'.$vacancy['sex'].'</p>';
			//if ($catInfo['name']) $result.= '<p><span>Специальность: </span>'.$catInfo['name'].'</p>';
			$result.= '</div>';
			$result.='</div>';
		}
	} else {
		$result.= '<p>Вакансии отсутствуют</p>';
	}
	$result.= '</div>';
	return $result;

}

//Список доступных 
function px_vcard_list($atts) {
	$px_library=new PX_Library();
	extract( shortcode_atts( array(
		'category' => -1
	), $atts ) );
	
	if ($category===-1) { // все категории
		$vcards=$px_library->getVCards();
		$result.= '<h3 class="cards_name">Свободные специалисты</h3>';
	} else { //конеретная категория
		$vcards=$px_library->getVCards($category,1);
		$catInfo=$px_library->getCategoryInfo($category);
		//$result.= '<h3 class="cards_name">Свободные '.mb_strtolower($catInfo['name']).'</h3>';
	}
	if ($vcards) {
		foreach ($vcards as $vcard ) {
			$result.='<div class="vcard">';
			$result.='<div class="vcard__thumb">'.wp_get_attachment_image( $vcard['photo'], 'thumbnail' ).'</div>';
			$result.= '<div class="vcard__desc">';
			$result.= '<a href="/pxcatalog/vcard/'.$vcard['id'].'">'.$vcard['name'].'</a>';
			if ($vcard['age']) $result.= '<p><span>Возраст: </span>'.$vcard['age'].'</p>';
			if ($vcard['experience']) $result.= '<p><span>Опыт:</span> '.$vcard['experience'].'</p>';
			if ($vcard['skills']) $result.= '<p><span>Навыки:</span> '.$vcard['skills'].'</p>';
			$result.= '</div>';
			$result.='</div>';
		}
	} else {
		$result.= '<p>Свободные специалисты отсутствуют</p>';
	}
	return $result;
}


function px_vcard($atts) {
	$px_library=new PX_Library();
	extract( shortcode_atts( array(
		'id' => -1
	), $atts ) );

	$vcardInfo=$px_library->getVCardInfo($id);
	$roadtime=$px_library->get_roadtime();
	$educations=$px_library->getEducations();
	
	echo '<div class="profile">';
	echo '<h1 class="cards_name">'.$vcardInfo['name'].'</h1>';
	if ($vcardInfo['photo']){
		echo '<div class="vcard-photo col-sm-5">';
		echo wp_get_attachment_image( $vcardInfo['photo'], 'full', "", array( "class" => "img-responsive" ));
		echo '</div>';
	}
	echo '<div class="col-sm-5">
			<table class="profile"><tbody>';
	echo '<tr><td><b>Контактный телефон: </b>'.$vcardInfo['phone'].'</td></tr>';
	if ($vcardInfo['address'])
		echo '<tr><td><b>Адрес: </b>'.$vcardInfo['address'].'</td></tr>';
	if ($vcardInfo['experience'])
		echo '<tr><td><b>Опыт работы: </b>'.$vcardInfo['experience'].'</td></tr>';
	//образования


	foreach ($educations as $education) {
		echo '<tr><td><b>'.$education['name'].': </b>';
			foreach ($education['eds'] as $edu) {
				
				if (in_array($edu['id'], $vcardInfo['eds'])) {
					echo '<span>'.$edu['name'].', </span>';
				}
            	
            }
		echo '</td></tr>';
	}	
	echo '<tr><td><b>Пол: </b>';
	echo ($vcardInfo['sex'])?'Мужской':'Женский';
	echo '</td></tr>';

	echo '<tr><td><b>Возраст: </b>'.$vcardInfo['age'].'</td></tr>';

	if (isset($vcardInfo['marital'])) { 
		echo '<tr><td><b>Семейное положение: </b>';
		echo ($vcardInfo['marital'])?'Замужем/Женат':'Не замужем/Не женат';
		echo '</td></tr>';
	}

	if (isset($vcardInfo['childs'])) { 
		echo '<tr><td><b>Наличие своих детей: </b>';
		echo ($vcardInfo['childs'])?'Да':'Нет';
		echo '</td></tr>';
	}

	echo '<tr><td><b>Время на дорогу: </b>'.$roadtime[$vcardInfo['roadtime']].'</td></tr>';
	if ($vcardInfo['worktime'])
		echo '<tr><td><b>Желаемый график работы: </b>'.$vcardInfo['worktime'].'</td></tr>';
	

	if ($vcardInfo['languages'])
		echo '<tr><td><b>Желаемый график работы: </b>'.$vcardInfo['languages'].'</td></tr>';
	if ($vcardInfo['salary'])
		echo '<tr><td><b>Желаемая заработная плата: </b>'.$vcardInfo['salary'].'</td></tr>';
	if (isset($vcardInfo['drive'])) { 
		echo '<tr><td><b>Наличие водительских прав: </b>';
		echo ($vcardInfo['drive'])?'Да':'Нет';
		echo '</td></tr>';
	}
	if ($vcardInfo['skills'])
		echo '<tr><td><b>Навыки: </b>'.$vcardInfo['skills'].'</td></tr>';
	
	if ($vcardInfo['sport'])
		echo '<tr><td><b>Спорт: </b>'.$vcardInfo['sport'].'</td></tr>';
	if ($vcardInfo['attitude'])
		echo '<tr><td><b>Отношение к работе: </b>'.$vcardInfo['attitude'].'</td></tr>';
	if ($vcardInfo['hobby'])
		echo '<tr><td><b>Хобби: </b>'.$vcardInfo['hobby'].'</td></tr>';
	echo '</tbody></table>
		</div>
	</div>';
}

function px_vacancy($atts) {
	$px_library=new PX_Library();
	extract( shortcode_atts( array(
		'id' => -1
	), $atts ) );

	$vacancyInfo=$px_library->getVacancyInfo($id);
	$ages=$px_library->get_ages();
	echo '<div class="profile"><h1 class="cards_name">'.$vacancyInfo['title'].'</h1>';
	echo '<table class="profile"><tbody>';
	echo '<tr><td><b>Контактное лицо: </b>'.$vacancyInfo['name'].'</td></tr>';
	echo '<tr><td><b>Контактный телефон: </b>'.$vacancyInfo['phone'].'</td></tr>';
	echo '<tr><td><b>Категория: </b>'.$vacancyInfo['category'].'</td></tr>';
	
	echo '<tr><td><b>Пол: </b>';
	if (isset($vacancyInfo['sex'])) { echo ($vacancyInfo['sex'])?'Мужской':'Женский';}
	else echo 'Не важен';
	echo '</td></tr>';

	echo '<tr><td><b>Возраст: </b>'.$ages[$vacancyInfo['age']].'</td></tr>';

	echo '<tr><td><b>Семейное положение: </b>';
	if (isset($vacancyInfo['sex'])) { echo ($vacancyInfo['sex'])?'Замужем/Женат':'Не замужем/Не женат';}
	else echo 'Не важно';
	echo '</td></tr>';

	echo '<tr><td><b>Наличие своих детей: </b>';
	if (isset($vacancyInfo['sex'])) { echo ($vacancyInfo['sex'])?'Да':'Нет';}
	else echo 'Не важно';
	echo '</td></tr>';

	if ($vacancyInfo['adds'])
		echo '<tr><td><b>Дополнительные пожелания: </b>'.$vacancyInfo['adds'].'</td></tr>';
	if ($vacancyInfo['salary'])
		echo '<tr><td><b>Заработная плата: </b>'.$vacancyInfo['salary'].'</td></tr>';
	
	if ($vacancyInfo['mode'])
		echo '<tr><td><b>Режим: </b>'.$vacancyInfo['mode'].'</td></tr>';
	 

	if (isset($vacancyInfo['roads'])) { 
		echo '<tr><td><b>Компенсация дорожных расходов: </b>';
		echo ($vacancyInfo['roads'])?'Да':'Нет';
		echo '</td></tr>';
	}

	if ($vacancyInfo['languages']) { 
		echo '<tr><td><b>Знание языков: </b>';
		echo $vacancyInfo['languages'];
		echo '</td></tr>';
	}

	if ($vacancyInfo['skills']) { 
		echo '<tr><td><b>Навыки: </b>';
		echo $vacancyInfo['skills'];
		echo '</td></tr>';
	}

	if (isset($vacancyInfo['drive'])) { 
		echo '<tr><td><b>Наличие водительских прав: </b>';
		echo ($vacancyInfo['drive'])?'Да':'Не обязательно';
		echo '</td></tr>';
	}

	if ($vacancyInfo['sport']) { 
		echo '<tr><td><b>Отношение к спорту: </b>';
		echo $vacancyInfo['sport'];
		echo '</td></tr>';
	}
	if (isset($vacancyInfo['food'])) { 
		echo '<tr><td><b>Питание работника: </b>';
		echo ($vacancyInfo['food'])?'Да':'Нет';
		echo '</td></tr>';
	}
	if (isset($vacancyInfo['overtime'])) { 
		echo '<tr><td><b>Сверхурочные: </b>';
		echo ($vacancyInfo['overtime'])?'Да':'Нет';
		echo '</td></tr>';
	}
	if (isset($vacancyInfo['holiday'])) { 
		echo '<tr><td><b>Отпуск: </b>';
		echo ($vacancyInfo['holiday'])?'Оплачиваемый':'Неоплачиваемый';
		echo '</td></tr>';
	}
	
	if ($vacancyInfo['exp'])
		echo '<tr><td><b>Опыт: </b>'.$vacancyInfo['exp'].'</td></tr>';

	echo '</tbody></table></div>';
}
//Список кактегорий
function px_categories($atts) {
	$px_library=new PX_Library();
	extract( shortcode_atts( array(
		'type' => 'vcards'
	), $atts ) );


	$categories=$px_library->getCategories();
	$result.='<style>
		.category {display:inline-block;width:50%;}
		.category span, .category p {display:inline-block;}
		.category a {text-decoration:none;}
	</style>';
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