<?php echo '
<style>
	.selectit {
		margin-right: 10px; 
		display:inline-block;
	}
	
	.edu td {
		padding-bottom: 5px;
	}
	.warning {
		border:1px solid #f24545 !important;
		-webkit-transition: 0.5s;
	    -moz-transition: 0.5s;
	    -o-transition: 0.5s;
	    transition: 0.5s;
	} 
	input, textarea, button, select {
		width:98%;
	}
	input, select {
		box-shadow:0 1px 2px rgba(0, 0, 0, 0.07) inset;
	}

	.notice-dismiss {
		width:auto;
	}
	';
if ($type) 
	echo '
	table,tr,td {
		border:none;
	}
	td {
		padding:3px;
	}
	td.first {
		width:40%;
	}
	.notice {
		background: #fff none repeat scroll 0 0;
	    border-left: 4px solid #dd3d36;
	    box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.15);
	    margin: 10px 0;
	    padding: 1px 12px;
	}
	
	.notice-dismiss {
	    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
	    border: medium none;
	    color: #b4b9be;
	    cursor: pointer;
	    margin: 0;
	    padding: 9px;
	    position: absolute;
	    right: 1px;
	    top: 0;
	}
	.success {
		border-left: 4px solid #7ad03a;
	}
	.notice p {
    	margin: 0.5em 0;
    	padding: 2px;
	}
	';
echo '</style>
<div class="wrap">';?>
	<?php 
		if ($action==='add') {
			echo '<h1>Добавление новой вакансии</h1>';
		} else {
			echo '<h1>Редактирование вакансии</h1>';
		}
	?>
	<?php
		if ($type)
			$link='/vacancyform/';
		else 
			$link = esc_url( add_query_arg( array( 'vacancy' => $vacancy ), menu_page_url( 'px-vacancy', false ) ) );
		echo '<form id="vacancy" method="post" action="'.$link.'" name="vacancy">';
	?>
	<?php if ($type && $id) { 
		echo '<div id="message" class="notice success">
						<p>Вакансия добавлена на рассмотрение модераторами сайта.</p>
						<button onclick="jQuery(\'#message\').remove();" type="button" class="notice-dismiss">
							<span class="screen-reader-text">Скрыть это уведомление.</span>
						</button>
			</div>';
		if ($data['error']) { 
			echo '<div id="message" class="notice">
						<p>Каптча</p>
						<button onclick="jQuery(\'#message\').remove();" type="button" class="notice-dismiss">
							<span class="screen-reader-text">Скрыть это уведомление.</span>
						</button>
					</div>';
		} 	
	} ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" style="position: relative;">
					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" id="title" value="<?php echo $data['title'];?>" size="30" name="utitle" placeholder="Заголовок">
						</div>
					</div>
					<div class="stuffbox" >
						<h3><label for="name">Данные Вакансии</label></h3>
						<div class="inside">
							<table>
								<tbody>
									<tr>
										<td class="first">ФИО:</td>
										<td><input type="text" id="name" name="uname" value="<?php echo $data['name'];?>"></td>
									</tr>
									<tr>
										<td class="first">Телефон:</td>
										<td><input type="text" id="phone" name="uphone" value="<?php echo $data['phone'];?>"></td>
									</tr>
									<tr>
										<td class="first">E-mail:</td>
										<td><input type="text" id="email" name="uemail" value="<?php echo $data['email'];?>"></td>
									</tr>
									<tr>
										<td class="first">Срок исполнения заявки:</td>
										<td>
											<select class="inputbox" size="1" id="deadline" name="deadline">
												<?php 
												foreach ($deadlines as $key => $value) {
													if ($key==$data['deadline'])
														echo '<option value="'.$key.'" selected="selected">'.$value.'</option>';
													else
														echo '<option value="'.$key.'">'.$value.'</option>'; 
												}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Категория (специальность):</td>
										<td>
											<select name="category" id="category">
												<?php
												if (!$data['category'])
													echo '<option value="0" disabled selected>Выберите категорию</option>';
												foreach ($categories as $category) {
													if ($category['id']==$data['category'])
														echo '<option value="'.$category['id'].'" selected="selected">'.$category['name'].'</option>';
													else
														echo '<option value="'.$category['id'].'">'.$category['name'].'</option>'; 
												}		
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Пол:</td>
										<td>
											<select class="inputbox" size="1" id="sex" name="sex">
											    <?php 
											    	if (!isset($data['sex'])) {
											    		echo '<option value="" selected>Не важно</option>';
											    		echo '<option value="1">мужской</option>';
											    		echo '<option value="0">женский</option>';
											    	} else {
											    		echo '<option value="" selected>Не важно</option>';
											    		if ($data['sex']) {
											    			echo '<option value="1" selected="selected" >мужской</option>';
											    			echo '<option value="0">женский</option>';
										    			} else {
											    			echo '<option value="1">мужской</option>';
											    			echo '<option value="0" selected="selected">женский</option>';
														}
											    	}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Возраст:</td>
										<td>
											<select class="inputbox" size="1" id="age" name="age">
											<?php
												foreach ($ages as $key => $value) {
													if ($data['age']==$key)
														echo '<option value="'.$key.'" selected>'.$value.'</option>';
													else 
														echo '<option value="'.$key.'">'.$value.'</option>';
												}		
											?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Район проживания:</td>
										<td><input type="text" id="district" name="district" value="<?php echo $data['district'];?>"></td>
									</tr>
									<tr>
										<td class="first">Семейное положение:</td>
										<td>
											<select class="inputbox" size="1" id="marital" name="marital">
												<?php 
											    	if (!isset($data['marital'])) {
														echo '<option value="" selected>Не важно</option>';
														echo '<option value="1">Замужем/женат</option>';
											    		echo '<option value="0">Не замужем/не женат</option>';
													} else {
														echo '<option value="">Не важно</option>';
														if ($data['marital']) { 
															echo '<option value="1" selected="selected">Замужем/женат</option>';
											    			echo '<option value="0">Не замужем/не женат</option>';
														} else {
															echo '<option value="1">Замужем/женат</option>';
															echo '<option value="0" selected="selected">Не замужем/не женат</option>';
										    			}
													}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Наличие своих детей:</td>
										<td>
											<select class="inputbox" size="1" id="childs" name="childs">
												<?php 
											    	if (!isset($data['childs'])) {
											    		echo '<option value="" selected>Не важно</option>';
											    		echo '<option value="1">Да</option>';
											    		echo '<option value="0">Нет</option>';
													} else {
											    		echo '<option value="">Не важно</option>';
											    		if ($data['childs']) {
											    			echo '<option value="1" selected="selected">Да</option>';
											    			echo '<option value="0">Нет</option>';
											    		} else {
											    			echo '<option value="1">Да</option>';
											    			echo '<option value="0" selected="selected">Нет</option>';
											    		}
											    	}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Дополнительные пожелания:</td>
										<td><textarea rows="3" id="adds" name="adds" style="width:98%;"><?php echo $data['adds'];?></textarea></td>
									</tr>
									<tr>
										<td class="first">Заработная плата:</td>
										<td><input type="text" id="salary" name="salary" value="<?php echo $data['salary'];?>"></td>
									</tr>
									<tr>
										<td class="first">Режим:</td>
										<td><input type="text" id="mode" name="mode" value="<?php echo $data['mode'];?>"></td>
									</tr>
									<tr>
										<td class="first">Компенсация дорожных расходов:</td>
										<td>
											<select class="inputbox" size="1" id="roads" name="roads">
												<?php 
													if (!isset($data['roads'])) {
														echo '<option value="" selected>Не выбрано</option>';
														echo '<option value="1">Да</option>';
											    		echo '<option value="0">Нет</option>';
													} else {
														echo '<option value="" selected>Не выбрано</option>';
														if ($data['roads']) { 
															echo '<option value="1" selected="selected">Да</option>';
											    			echo '<option value="0">Нет</option>';
														} else {
															echo '<option value="1">Да</option>';
															echo '<option value="0" selected="selected">Нет</option>';
										    			}
													}
												?>
											</select>
										</td>
									</tr>

									<tr>
										<td class="first">Языки:</td>
										<td><input type="text" id="languages" name="languages" value="<?php echo $data['languages'];?>"></td>
									</tr>
									<tr>
										<td class="first">Навыки:</td>
										<td><input type="text" id="skills" name="skills" value="<?php echo $data['skills'];?>"></td>
									</tr>
									<tr>
										<td class="first">Наличие водительского удостоверения:</td>
										<td>
											<select class="inputbox" size="1" id="drive" name="drive">
												<?php 
													if (!isset($data['drive'])) {
														echo '<option value="-1" selected disabled>не выбрано</option>';
														echo '<option value="1">есть</option>';
											    		echo '<option value="0">нет</option>';
													} else {
														if ($data['drive']) { 
															echo '<option value="1" selected="selected">есть</option>';
											    			echo '<option value="0">нет</option>';
														} else {
															echo '<option value="0" selected="selected">нет</option>';
										    				echo '<option value="1">есть</option>';
														}
													}
												?>
											</select>
										</td>
									</tr>
									
									<tr>
										<td class="first">Спорт:</td>
										<td><input type="text" id="sport" name="sport" value="<?php echo $data['sport'];?>"></td>
									</tr>
									
									<tr>
										<td class="first">Питание работника:</td>
										<td>
											<select class="inputbox" size="1" id="food" name="food">
												<?php 
													if (!isset($data['food'])) {
														echo '<option value="" selected>Не выбрано</option>';
														echo '<option value="1">Да</option>';
											    		echo '<option value="0">Нет</option>';
													} else {
														echo '<option value="">Не выбрано</option>';
														if ($data['food']) { 
															echo '<option value="1" selected>Да</option>';
											    			echo '<option value="0">Нет</option>';
														} else {
															echo '<option value="1">Да</option>';
															echo '<option value="0" selected>Нет</option>';
										    			}
													}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Сверхурочные:</td>
										<td>
											<select class="inputbox" size="1" id="overtime" name="overtime">
												<?php 
													if (!isset($data['overtime'])) {
														echo '<option value="" selected>Не выбрано</option>';
														echo '<option value="1">Да</option>';
											    		echo '<option value="0">Нет</option>';
													} else {
														echo '<option value="">Не выбрано</option>';
														if ($data['overtime']) { 
															echo '<option value="1" selected>Да</option>';
											    			echo '<option value="0">Нет</option>';
														} else {
															echo '<option value="1">Да</option>';
															echo '<option value="0" selected>Нет</option>';
										    			}
													}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Отпуск:</td>
										<td>
											<select class="inputbox" size="1" id="holiday" name="holiday">
												<?php 
													if (!isset($data['holiday'])) {
														echo '<option value="" selected>Не выбрано</option>';
														echo '<option value="1">Оплачиваемый</option>';
											    		echo '<option value="0">Не оплачиваемый</option>';
													} else {
														echo '<option value="">Не выбрано</option>';
														if ($data['holiday']) { 
															echo '<option value="1" selected>Оплачиваемый</option>';
											    			echo '<option value="0">Не оплачиваемый</option>';
														} else {
															echo '<option value="1">Оплачиваемый</option>';
															echo '<option value="0" selected>Не оплачиваемый</option>';
										    			}
													}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Мин. стаж работы (лет):</td>
										<td><input type="text" id="exp" name="exp" value="<?php echo $data['exp'];?>"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php if (!$type) { ?>
				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox" >
						<h3>Статус</h3>
						<div class="inside" >
							<p>По умолчанию созданная вакансия не публикуется. Если вы хотите сразу опубликовать ее - воспользуйтесь функцией "Опубликовать" </p>
							<ul>
								<li><input type="submit" value="Сохранить" name="px-category-save" class="button-primary"></li>
								<li>
									<?php if($data['status']) {?>
										<button id="approve" class="button-secondary">Снять с публикации</button>
									<?php } else { ?>
										<button id="approve" class="button-primary">Опубликовать</button>
									<?php } ?>
								</li>
								<li><input type="submit" value="Удалить" name="px-category-delete" class="button-secondary"></li>
							</ul>
						</div>
					</div>
				</div>
				<?php }  ?>
			</div>
		</div>
		<input type="hidden" name="action" value="save">
		<?php if (!$type) { ?>
			<input type="hidden" name="status" id="status" value="<?php echo $data['status'];?>">
			<input type="hidden" name="vacancy" value="<?php echo $vacancy; ?>">	
		<?php } else { ?>
			<div class="g-recaptcha" data-sitekey="6Lcm8BQUAAAAAOS4uxBkri9fitDUDKvfJJx2b_Pg" style="width: 50%;margin:0 auto;"></div>
			<input type="submit"  value="Отправить" style="display:block; width: 50%;margin:0 auto;">
		<?php } ?>
	</form>
</div>
<script type="text/javascript">
jQuery(function($) {
	$('#approve').click(function(){
		var status=$('#status').val();
		if (status==1) 
			$('#status').val(0);
		else 
			$('#status').val(1);
		$('#vacancy').submit();
	});

	$("#vacancy").submit(function(e) {

		if (!checkFields()) { //если что-то не ввели, то не отправляем форму
    		e.preventDefault();
    		showError();
    	}
	});

	$('#title').change(function(){$(this).removeClass('warning');});
	$('#name').change(function(){$(this).removeClass('warning');});
	$('#phone').change(function(){$(this).removeClass('warning');});
	$('#category').change(function(){$(this).removeClass('warning');});
	

	function checkFields() {
		var result=true;
		if( !$('#title').val() ) {
			$('#title').addClass('warning');
			result=false;
		}
		if( !$('#name').val() ) {
			$('#name').addClass('warning');
			result=false;
		}
		if( !$('#phone').val() ) {
			$('#phone').addClass('warning');
			result=false;
		}
		if( !$('#category').val() ) {
			$('#category').addClass('warning');
			result=false;
		}
		return result;
	}

	function showError() {
		$('#message').remove();
		var html='<div class="notice notice-error is-dismissible below-h2" id="message">'+
					'<p>Пожалуйста, заполните обязательные атрибуты</p>'+
					'<button class="notice-dismiss" type="button" onclick="jQuery(\'#message\').remove();">'+
						'<span class="screen-reader-text">Скрыть это уведомление.</span>'+
					'</button>'+
				'</div>';
		if (!$("#message" ).length) { //проверяем - нет ли уже сообщения
			$('#vacancy').prepend(html);	 
		}
	}
});
</script>
