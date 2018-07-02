<?php

if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class PX_Catalog_Vacancy_Table extends WP_List_Table {
	var $items = array();
	var $found_data = array();
	
	function set_items($items) {
		$this->items=$items;
	}

	function no_items() {
		_e( 'Вакансии отсутствуют.' );
	}
	
	function column_default( $item, $column_name ) {
		switch( $column_name ) { 
			case 'title':
			case 'name':
			case 'date':
			case 'status':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array('title',false),
			'name'  => array('name',false),
			'date'  => array('date',false),
			'status'   => array('status',false)
		);
		return $sortable_columns;
	}

	function get_columns(){
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'title' => 'Заголовок',
			'name' => 'ФИО',
			'date'    => 'Дата подачи',
			'status'      => 'Статус'
		);
		return $columns;
	}
	function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date'; // If no sort, default to name
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc'; // If no order, default to asc
		$result = strcmp( $a[$orderby], $b[$orderby] ); // Determine sort order
		return ( $order === 'asc' ) ? $result : -$result; // Send final sort direction to usort
	}

	function column_title($item) {
		if ($item['status'])
			$actions = array(
				'edit'      => sprintf('<a href="?page=%s&action=%s&vacancy=%s">Редактировать</a>',$_REQUEST['page'],'edit',$item['id']),
				'delete'    => sprintf('<a href="?page=%s&action=%s&vacancy=%s">Удалить</a>',$_REQUEST['page'],'delete',$item['id']),
				'unpublish'    => sprintf('<a href="?page=%s&action=%s&vacancy=%s">Снять с публикации</a>',$_REQUEST['page'],'unpublish',$item['id']),
			);
		else 
			$actions = array(
				'edit'      => sprintf('<a href="?page=%s&action=%s&vacancy=%s">Редактировать</a>',$_REQUEST['page'],'edit',$item['id']),
				'delete'    => sprintf('<a href="?page=%s&action=%s&vacancy=%s">Удалить</a>',$_REQUEST['page'],'delete',$item['id']),
				'publish'    => sprintf('<a href="?page=%s&action=%s&vacancy=%s">Опубликовать</a>',$_REQUEST['page'],'publish',$item['id']),
			);
		return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
	}



	function get_bulk_actions() {
		$actions = array(
			'publish'	=> 'Опубликовать',
			'unpublish'	=> 'Снять с публикации',
			'delete'    => 'Удалить'
		);
		return $actions;
	}
	function column_cb($item) {
		return sprintf('<input type="checkbox" name="vacancy[]" value="%s" />', $item['id']);    
	}
	function column_status($item) {
		if ($item['status']) { $class="dashicons-no"; $func="publish"; $text="Опубликована";}
		else { $class="dashicons-yes"; $func="unpublish"; $text="Снята с публикации";}
		return '<span>'.$text.'</span>';
	}

	function prepare_items() {
		$columns  = $this->get_columns();
		
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->items, array( &$this, 'usort_reorder' ) );

		$current_page = $this->get_pagenum();
		$total_items = count( $this->items );
		$this->found_data = array_slice( $this->items,( ( $current_page-1 )* $per_page ), $per_page );
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );
		$this->items = $this->found_data;
	}
} //class