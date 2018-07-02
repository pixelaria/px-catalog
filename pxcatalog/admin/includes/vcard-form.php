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
	.inputbox {
		width:auto;
		margin: -4px 4px -1px 0;
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
echo '</style>';?>
<div class="wrap">
	<?php 
		if ($action==='add') {
			echo '<h1>Добавление нового резюме</h1>';
		} else {
			echo '<h1>Редактирование резюме</h1>';
		}
	?>
	<?php
		if ($type)
			$link='/prisluga/vcardform/';
		else 
			$link = esc_url( add_query_arg( array( 'vcard' => $vcard ), menu_page_url( 'px-vcard', false ) ) );
		echo '<form id="vcard" method="post" action="'.$link.'" name="vacancy">';
	?>
	<?php if ($type && ($id>0)) { 
			echo '<div id="message" class="notice success">
						<p>Резюме добавлено на рассмотрение модераторами сайта.</p>
						<button onclick="jQuery(\'#message\').remove();" type="button" class="notice-dismiss">
							<span class="screen-reader-text">Скрыть это уведомление.</span>
						</button>
					</div>';
		} 
		if ($data['error']) { 
			echo '<div id="message" class="notice">
						<p>Каптча</p>
						<button onclick="jQuery(\'#message\').remove();" type="button" class="notice-dismiss">
							<span class="screen-reader-text">Скрыть это уведомление.</span>
						</button>
					</div>';
		} 
	?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" style="position: relative;">
					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" id="title" size="30" name="uname" value="<?php echo $data['name'];?>" placeholder="ФИО">
						</div>
					</div>
					<div class="stuffbox" >
						<h3><label for="name">Данные резюме</label></h3>
						<div class="inside">
							<table>
								<tbody>
									<?php if(!$type) { 

									?>
									<tr>
										<td class="first">Фотография:</td>
										<td>
										<?php if ($data['photo']) echo wp_get_attachment_image( $data['photo'], 'thumbnail' );
											else echo '<img src="https://placehold.it/150x150" alt="Фото" id="thumb">';
										?>
											<button id="upload_photo">Загрузить фото</button>
											<input type="hidden" name="photo" id="photo" value="<?php echo $data['photo'];?>">
										</td>
									</tr>
									<?php } ?>
									<tr>
										<td class="first">Телефон:</td>
										<td><input type="text" id="phone" name="uphone" value="<?php echo $data['phone'];?>"></td>
									</tr>
									<tr>
										<td class="first">E-mail:</td>
										<td><input type="text" id="email" name="uemail" value="<?php echo $data['email'];?>"></td>
									</tr>
									<tr>
										<td class="first">Адрес:</td>
										<td><textarea rows="3" id="address" name="address" style="width:98%;"><?php echo $data['address'];?></textarea></td>
									</tr>
									<tr>
										<td class="first">Категория:</td>
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
										<td class="first">Опыт работы:</td>
										<td><textarea rows="3" id="experience" name="experience" style="width:98%;"><?php echo $data['experience'];?></textarea></td>
									</tr>
									<?php 
										foreach ($educations as $education) {
											echo '<tr class="edu"><td class="first">'.$education['name'].'</td><td>';
												foreach ($education['eds'] as $edu) {
													echo '<label class="selectit" for="edu_'.$edu['id'].'">';
													if (in_array($edu['id'], $data['eds'])) 
														echo '<input id="edu_'.$edu['id'].'" name="edu['.$education['id'].'][]" value="'.$edu['id'].'" class="inputbox" type="checkbox" checked="checked">';
													else 
														echo '<input id="edu_'.$edu['id'].'" name="edu['.$education['id'].'][]" value="'.$edu['id'].'" class="inputbox" type="checkbox">';
								                	echo $edu['name'].'</label>';
								                }
											echo '</td></tr>';
										}		
									?>
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
										<td class="first">Отношение к работе:</td>
										<td>
											<textarea rows="2" id="attitude" name="attitude" style="width:98%;"><?php echo $data['attitude'];?></textarea>
										</td>
									</tr>
									<tr>
										<td class="first">Хобби:</td>
										<td>
											<textarea rows="2" id="hobby" name="hobby" style="width:98%;"><?php echo $data['hobby'];?></textarea>
										</td>
									</tr>
									<tr>
										<td class="first">Пол:</td>
										<td>
											<select class="inputbox" size="1" id="sex" name="sex">
												<?php 
													if (!isset($data['sex'])) {
														echo '<option value="-1" selected disabled>не выбран</option>';
														echo '<option value="1">мужской</option>';
											    		echo '<option value="0">женский</option>';
													} else {
														if ($data['sex']) { 
															echo '<option value="1" selected="selected">мужской</option>';
											    			echo '<option value="0">женский</option>';
														} else {
															echo '<option value="0" selected="selected">женский</option>';
										    				echo '<option value="1">мужской</option>';
														}
													}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Возраст:</td>
										<td><input type="text" id="age" name="age" value="<?php echo $data['age'];?>"></td>
									</tr>
									<tr>
										<td class="first">Семейное положение:</td>
										<td>
											<select class="inputbox" size="1" id="marital" name="marital">
											    <?php 
													if (!isset($data['marital'])) {
														echo '<option value="-1" selected>не указано</option>';
														echo '<option value="1">Замужем/женат</option>';
											    		echo '<option value="0">Не замужем/не женат</option>';
													} else {
														echo '<option value="-1">не выбрано</option>';
														if ($data['marital']) { 
															echo '<option value="1" selected="selected">Замужем/женат</option>';
											    			echo '<option value="0">Не замужем/не женат</option>';
														} else {
															echo '<option value="0" selected="selected">Не замужем/не женат</option>';
										    				echo '<option value="1">Замужем/женат</option>';
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
														echo '<option value="-1" selected>не указано</option>';
														echo '<option value="1">Да</option>';
											    		echo '<option value="0">Нет</option>';
													} else {
														echo '<option value="-1">не указано</option>';
														if ($data['childs']) { 
															echo '<option value="1" selected="selected">Да</option>';
											    			echo '<option value="0">Нет</option>';
														} else {
															echo '<option value="0" selected="selected">Нет</option>';
										    				echo '<option value="1">Да</option>';
														}
													}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Время на дорогу:</td>
										<td>
											<select class="inputbox" size="1" name="roadtime" id="roadtime">
												<?php foreach ($roadtime as $key => $value) {
													if ($key==$data['roadtime']) 
														echo "<option value='".$key."' selected='selected'>".$value."</option>";
													else 
														echo "<option value='".$key."'>".$value."</option>";
												} ?>
											</select>
										</td>
									</tr>
									<tr>
										<td class="first">Желаемый график работы:</td>
										<td><textarea rows="3" id="worktime" name="worktime" style="width:98%;"><?php echo $data['worktime'];?></textarea></td>
									</tr>
									<tr>
										<td class="first">Желаемая заработная плата:</td>
										<td><input type="text" id="salary" name="salary" value="<?php echo $data['salary'];?>"></td>
									</tr>
								</tbody>
							</table>
							<br>
						</div>
					</div>
				</div>
				<?php if (!$type) { ?>
				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox" >
						<h3>Статус</h3>
						<div class="inside" >
							<p>По умолчанию созданное резюме не публикуется. Если вы хотите сразу опубликовать его - воспользуйтесь функцией "Опубликовать" </p>
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
				<?php } ?>
			</div>
			<br class="clear">
		</div>
		<input type="hidden" name="action" value="save">
		<?php if (!$type) { ?>
			<input type="hidden" name="status" id="status" value="<?php echo $data['status'];?>">
			<input type="hidden" name="vcard" value="<?php echo $vcard; ?>">
		<?php } else { ?>
			<div class="col-xs-6"></div>
			<div class="col-xs-6">
				<div class="g-recaptcha " data-sitekey="6Lcm8BQUAAAAAOS4uxBkri9fitDUDKvfJJx2b_Pg" data-callback="recaptchaCallback"></div>
				<input id="submit" type="submit" disabled="disabled" value="Отправить">
			</div>
			
		<?php } ?>	
	</form>
</div>

<?php if (!$type) { ?>
<script>
	var file_frame;
	jQuery('#upload_photo').live('click', function( event ){
		event.preventDefault();
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
		    text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: false  // Set to true to allow multiple files to be selected
		});

		
		file_frame.on( 'select', function() {
		  attachment = file_frame.state().get('selection').first().toJSON();
		  jQuery( '#thumb' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
		  jQuery( '#photo' ).val( attachment.id );
		
		});
		file_frame.open();
	});
</script>

<?php } ?>

<script type="text/javascript">
jQuery(function($) {
	$('#approve').click(function(){
		var status=$('#status').val();
		if (status==1) 
			$('#status').val(0);
		else 
			$('#status').val(1);
		$('#vcard').submit();
	});


	$("#vcard").submit(function(e) {
		if (!checkFields()) { //если что-то не ввели, то не отправляем форму
    		e.preventDefault();
    		showError();
    	}
	});

	$('#title').change(function(){$(this).removeClass('warning');});
	//$('#photo').change(function(){$(this).removeClass('warning');});
	$('#phone').change(function(){$(this).removeClass('warning');});
	$('#category').change(function(){$(this).removeClass('warning');});
	$('#sex').change(function(){$(this).removeClass('warning');});
	$('#age').change(function(){$(this).removeClass('warning');});

	function checkFields() {
		var result=true;
		if( !$('#title').val() ) {
			$('#title').addClass('warning');
			result=false;
		}
		/*if( !$('#photo').val() ) {
			$('#photo').addClass('warning');
			result=false;
		}*/
		if( !$('#phone').val() ) {
			$('#phone').addClass('warning');
			result=false;
		}
		if( !$('#category').val() ) {
			$('#category').addClass('warning');
			result=false;
		}
		if( !$('#title').val() ) {
			$('#title').addClass('warning');
			result=false;
		}
		if( !$('#sex').val() ) {
			$('#sex').addClass('warning');
			result=false;
		}
		if( !$('#age').val() ) {
			$('#age').addClass('warning');
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
			$('#vcard').prepend(html);	 
		}
	}
	function recaptchaCallback() {
    	$('#submit').removeAttr('disabled');
	};
});
</script>


	    