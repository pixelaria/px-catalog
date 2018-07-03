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

		public function getProducts($cat=false,$status=false) {
			global $wpdb,$table_prefix;
			
			$query="SELECT * FROM ".$table_prefix."px_product WHERE 1";

			if ($cat) $query.=" AND category=".$cat;
			if ($status) $query.=" AND status=1";

			$products = $wpdb->get_results($query,ARRAY_A);
			return $products;
		}

		public function getProductInfo($product) {
			global $wpdb,$table_prefix;
			$query="SELECT p.* FROM ".$table_prefix."px_product p WHERE id=".$product;
			$product_info = $wpdb->get_results($query,ARRAY_A);
			$result=$product_info[0];
			return $result;
		}

		public function saveProduct($data) {
			global $wpdb,$table_prefix;
			
			if ($data['id']==-1)  //добавление новой записи
				$query="INSERT INTO ".$table_prefix."px_product (name, description,  price, photo, category, status, date)".
													" VALUES ('".$data['name']."','".$data['description']."','".$data['price']."','".$data['photo']."','".$data['category']."','".$data['status']."',NOW())";
			else //редактирование
				$query="UPDATE ".$table_prefix."px_product SET 
								    `name` = '".$data['name'].
								"', `description` = '".$data['photo'].
								"', `price` = '".$data['phone'].
								"', `photo` = '".$data['email'].
								"', `category` = '".$data['address'].
								"', `status` = '".$data['status'].
								"', `date` = '".$data['date'].
								"' WHERE id=".$data['id'];
					
			$wpdb->query($query); //выполняем запрос

			if ($data['id']==-1) $id=$wpdb->insert_id;
			else $id=$data['id'];

			return $id;
		}	


		public function setProductStatus($status,$id) {
			global $wpdb,$table_prefix;
			$query = "UPDATE ".$table_prefix."px_product SET status=".$status." WHERE id=".$id;
			$wpdb->query($query); //выполняем запрос
		}

		public function deteteProduct($id) {
			global $wpdb,$table_prefix;
			
			$query="DELETE FROM ".$table_prefix."px_product WHERE id=".$id; //затем само резюме
			$wpdb->query($query); //выполняем запрос
		}
	}
?>