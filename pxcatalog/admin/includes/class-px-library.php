<?php
	
	class SM_Library {
		public function getCategories() {
			global $wpdb,$table_prefix;
			$query="SELECT * FROM ".$table_prefix."px_category";
			$categories = $wpdb->get_results($query,ARRAY_A);
			return $categories;
		}


		public function getCategoryInfo($cat) {
			global $wpdb,$table_prefix;
			$query="SELECT * FROM ".$table_prefix."px_category WHERE id=".$cat;
			$catInfo = $wpdb->get_results($query,ARRAY_A);
			return $catInfo[0];
		}
		

		public function saveCategory($data) {
			global $wpdb,$table_prefix;
			if ($data['id']==-1)  //создание
				$query="INSERT INTO ".$table_prefix."px_category(`name`, `desc`, `status`) VALUES ('".$data['name']."','".$data['desc']."',1)";
			else  //редактирование
				$query="UPDATE pri_px_category SET `name` = '".$data['name']."', `desc` = '".$data['desc']."' WHERE id=".$data['id'];
			$wpdb->query($query); //выполняем запрос
			if ($data['id']==-1) return $wpdb->insert_id;
			else return $data['id'];
		}

		public function deleteCategory($cat) {
			global $wpdb,$table_prefix;
			//удаление категории
			//сначала удаляем все данные, связанные с указанной категорией
			
			//затем удаляем саму категорию
			$query="DELETE FROM ".$table_prefix."px_category WHERE id=".$cat;
			$wpdb->query($query); //выполняем запрос
		}

		public function setCatStatus($status,$id) {
			global $wpdb,$table_prefix;
			$query = "UPDATE ".$table_prefix."px_category SET status=".$status." WHERE id=".$id;
			$wpdb->query($query); //выполняем запрос
		}

		public function getVCards($cat=false,$status=false) {
			global $wpdb,$table_prefix;
			
			$query="SELECT * FROM ".$table_prefix."px_vcard WHERE 1";

			if ($cat) $query.=" AND category=".$cat;
			if ($status) $query.=" AND status=1";

			$vcards = $wpdb->get_results($query,ARRAY_A);
			return $vcards;
		}

		public function getVCardInfo($vcard) {
			global $wpdb,$table_prefix;
			$query="SELECT vcard.* FROM ".$table_prefix."px_vcard vcard WHERE id=".$vcard;
			$cardInfo = $wpdb->get_results($query,ARRAY_A);
			$result=$cardInfo[0];
			
			$query="SELECT id_education AS id FROM ".$table_prefix."px_vcard_to_education WHERE id_vcard=".$vcard;
			$eds = $wpdb->get_results($query,ARRAY_A);
			if ($eds) {
				foreach ($eds as $ed) {
					$result['eds'][]=$ed['id'];
				}
			}
			return $result;
		}

		public function saveVCard($data) {
			global $wpdb,$table_prefix;
			
			if ($data['id']==-1)  //добавление новой записи
				$query="INSERT INTO ".$table_prefix."px_vcard (name, photo, phone, email, address, category,".
															  "experience, sex, age, marital, childs,".
															  "roadtime, worktime,".
															  "languages, skills, drive, salary, sport, attitude, hobby, ".
															  "status, date)".
													" VALUES ('".$data['name']."','".$data['photo']."','".$data['phone']."','".$data['email']."','".$data['address']."','".$data['category']."','"
																.$data['experience']."','".$data['sex']."','".$data['age']."',".$data['marital'].",".$data['childs'].",'"
																.$data['roadtime']."','".$data['worktime']."','"
																.$data['languages']."','".$data['skills']."','".$data['drive']."','".$data['salary']."','".$data['sport']."','".$data['attitude']."','".$data['hobby']."','"
																.$data['status']."',NOW())";
			else //редактирование
				$query="UPDATE ".$table_prefix."px_vcard SET 
								    `name` = '".$data['name'].
								"', `photo` = '".$data['photo'].
								"', `phone` = '".$data['phone'].
								"', `email` = '".$data['email'].
								"', `address` = '".$data['address'].
								"', `category` = '".$data['category'].
								"', `experience` = '".$data['experience'].
								"', `sex` = '".$data['sex'].
								"', `age` = '".$data['age'].
								"', `marital` = '".$data['marital'].
								"', `childs` = '".$data['childs'].
								"', `roadtime` = '".$data['roadtime'].
								"', `languages` = '".$data['languages'].
								"', `skills` = '".$data['skills'].
								"', `drive` = '".$data['drive'].
								"', `salary` = '".$data['salary'].
								"', `sport` = '".$data['sport'].
								"', `attitude` = '".$data['attitude'].
								"', `hobby` = '".$data['hobby'].
								"', `worktime` = '".$data['worktime'].
								"', `status` = '".$data['status'].
								"', `date` = '".$data['date'].
								"' WHERE id=".$data['id'];
					
			$wpdb->query($query); //выполняем запрос

			if ($data['id']==-1) $id=$wpdb->insert_id;
			else $id=$data['id'];

			if (isset($data['edu'])) { //если есть образования
				$query="DELETE FROM ".$table_prefix."px_vcard_to_education WHERE id_vcard=".$id;
				$wpdb->query($query); //сначала удаляем все что есть

				foreach ($data['edu'] as $key => $edu) { //цикл по типам образования
					$query="INSERT INTO ".$table_prefix."px_vcard_to_education (`id_vcard`, `id_education`) VALUES "; //запрос прячем внутрь для подстраховки
					foreach ($edu as $eds) { //цикл по типам образования
						$query.=" (".$id.",".$eds."),";
					}	
					$query=substr($query, 0, -1); //удалили последнюю запятую
					//echo $query;					
					$wpdb->query($query); //выполняем запрос


				}
			}
			return $id;
		}	


		public function setVcardStatus($status,$id) {
			global $wpdb,$table_prefix;
			$query = "UPDATE ".$table_prefix."px_vcard SET status=".$status." WHERE id=".$id;
			$wpdb->query($query); //выполняем запрос
		}

		public function deleteVcard($id) {
			global $wpdb,$table_prefix;
			$query="DELETE FROM ".$table_prefix."px_vcard_to_education WHERE id_vcard=".$id; //сначала удалили образования
			
			$wpdb->query($query); //выполняем запрос

			$query="DELETE FROM ".$table_prefix."px_vcard WHERE id=".$id; //затем само резюме
			$wpdb->query($query); //выполняем запрос
		}


		public function getVacancies($cat=false,$status=false) {
			global $wpdb,$table_prefix;
			$query="SELECT * FROM ".$table_prefix."px_vacancy WHERE 1";

			if ($cat) $query.=" AND category=".$cat;
			if ($status) $query.=" AND status=1";
			$vacancies = $wpdb->get_results($query,ARRAY_A);
			return $vacancies;
		}

		
		public function getVacancyInfo($vacancy) {
			global $wpdb,$table_prefix;
			$query="SELECT vacancy.* FROM ".$table_prefix."px_vacancy vacancy WHERE id=".$vacancy;
			$cardInfo = $wpdb->get_results($query,ARRAY_A);
			$result=$cardInfo[0];
			return $result;	
		}
		

		public function saveVacancy($data) {
			global $wpdb,$table_prefix;
			
			if ($data['id']==-1)  //добавление новой записи
				$query="INSERT INTO ".$table_prefix."px_vacancy (title, name, phone, email, deadline, category,".
															  "sex, age, district, marital, childs, adds, ".
															  "salary, mode, roads, languages, skills, drive, sport, food, overtime, holiday, exp, status, date)".
													" VALUES ('".$data['title']."','".$data['name']."','".$data['phone']."','".$data['email']."','".$data['deadline']."','".$data['category']."',"
																.$data['sex'].",'".$data['age']."','".$data['district']."',".$data['marital'].",".$data['childs'].",'".$data['adds']."','"
																.$data['salary']."','".$data['mode']."',".$data['roads'].",".$data['languages'].",".$data['skills'].",".$data['drive'].",".$data['sport']
																.",".$data['food'].",".$data['overtime'].",".$data['holiday'].",'".$data['exp']."',0,NOW())";
			else //редактирование
				$query="UPDATE ".$table_prefix."px_vacancy SET 
								    `title` = '".$data['title'].
								"', `name` = '".$data['name'].
								"', `phone` = '".$data['phone'].
								"', `email` = '".$data['email'].
								"', `deadline` = '".$data['deadline'].
								"', `category` = '".$data['category'].
								"', `sex` = ".$data['sex'].
								", `age` = '".$data['age'].
								"', `district` = '".$data['district'].
								"', `marital` = ".$data['marital'].
								", `childs` = ".$data['childs'].
								", `adds` = '".$data['adds'].
								"', `salary` = '".$data['salary'].
								"', `mode` = '".$data['mode'].
								"', `roads` = ".$data['roads'].
								", `languages` = '".$data['languages'].
								"', `skills` = '".$data['skills'].
								"', `drive` = ".$data['drive'].
								", `sport` = '".$data['roads'].
								"', `food` = ".$data['food'].
								", `overtime` = ".$data['overtime'].
								", `holiday` = ".$data['holiday'].
								", `exp` = '".$data['exp'].
								"', `overtime` = '".$data['overtime'].
								"', `status` = '".$data['status'].
								"', `date` = '".$data['date'].
								"' WHERE id=".$data['id'];

			$wpdb->query($query); //выполняем запрос
			if ($data['id']==-1) $id=$wpdb->insert_id;
			else $id=$data['id'];

			return $id;
		}

		public function setVacancyStatus($status,$id) {
			global $wpdb,$table_prefix;
			$query = "UPDATE ".$table_prefix."px_vacancy SET status=".$status." WHERE id=".$id;
			$wpdb->query($query); //выполняем запрос
		}

		public function deleteVacancy($id) {
			global $wpdb,$table_prefix;
			$query="DELETE FROM ".$table_prefix."px_vacancy WHERE id=".$id; //затем само резюме
			$wpdb->query($query); //выполняем запрос
		}

		
		public function getEducations() {
			global $wpdb,$table_prefix;
			$educations=array();
			$query="SELECT * FROM ".$table_prefix."px_education_type";
			$types = $wpdb->get_results($query,ARRAY_A);
			foreach ($types as $type) {
				$query="SELECT * FROM ".$table_prefix."px_education where type=".$type['id'];
				$education = $wpdb->get_results($query,ARRAY_A);
				$educations[] = array(
					'id'=>$type['id'],
					'name'=>$type['name'],
					'eds'=>$education
				); 
			}
			return $educations;
		}

		public function get_ages() {
		    $ages=array(
		        '0'=>"Не важно",
		        '1'=>"до 25",
		        '2'=>"25-35",
		        '3'=>"25-45",
		        '4'=>"35-45",
		        '5'=>"45-55",
		        '5'=>"от 50"
		    );
		    return $ages;
		}

		public function get_deadlines() {
		    $deadlines=array(
		        '1'=>"1 день",
		        '3'=>"3 дня",
		        '7'=>"неделя",
		        '31'=>"месяц"
		    );
		    return $deadlines;
		}

		public function get_roadtime() {
		    $roadtime=array(
		        '0'=>"Все равно",
		        '30'=>"0,5 часа",
		        '60'=>"1 час",
		        '90'=>"1,5 часа"
		    );
		    return $roadtime; 
		}
	}
?>