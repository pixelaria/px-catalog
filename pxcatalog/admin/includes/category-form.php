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
</style>
<div class="wrap">
	<?php 
		if ($action==='add') {
			echo '<h2>Добавление новой категории</h2>';
		} else {
			echo '<h2>Редактирование категории</h2>';
		}
	?>
	<form id="category" method="post" action="<?php echo esc_url( add_query_arg( array( 'cat' => $cat ), menu_page_url( 'px-catalog-cats', false ) ) ); ?>" name="category">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" style="position: relative;">
					<div id="titlediv">
						<div id="titlewrap">
							<input type="text" id="title" value="<?php echo $catInfo['name'];?>" size="30" name="name" placeholder="Название категории">
						</div>
					</div>
					<?php the_editor($catInfo['desc'],'desc',null,true,2,true); ?>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox" >
						<h3>Статус</h3>
						<div class="inside" >
							<p>По умолчанию созданная категория не публикуется. Если вы хотите сразу опубликовать его - воспользуйтесь функцией "Опубликовать" </p>
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
			</div>
			<br class="clear">
		</div>
		<input type="hidden" name="action" value="save">
		<input type="hidden" name="status" id="status" value="<?php echo $data['status'];?>">
		<input type="hidden" name="cat" value="<?php echo $cat; ?>">	
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
			$('#category').submit();
		});


		$("#category").submit(function(e) {
			if (!checkFields()) { //если что-то не ввели, то не отправляем форму
	    		e.preventDefault();
	    		showError();
	    	}
		});

		$('#title').change(function(){$(this).removeClass('warning');});


		function checkFields() {
			var result=true;
			if( !$('#title').val() ) {
				$('#title').addClass('warning');
				result=false;
			}
			return result;
		}
		function showError() {
			var html='<div class="notice notice-error is-dismissible below-h2" id="message">'+
						'<p>Пожалуйста, заполните обязательные атрибуты</p>'+
						'<button class="notice-dismiss" type="button" onclick="jQuery(\'#message\').remove();">'+
							'<span class="screen-reader-text">Скрыть это уведомление.</span>'+
						'</button>'+
					'</div>';
			if (!$("#message" ).length) { //проверяем - нет ли уже сообщения
				$('#category').prepend(html);	 
			}
		}
	});
</script>

	    