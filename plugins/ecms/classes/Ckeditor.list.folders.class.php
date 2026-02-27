<?php
/**
 * CKEditor - Folder List Picker Class
 * @package ECMED
 */
class Ckeditor_List_Folders extends ECCAT_Category_List {

	/**
	 * Not used in the media picker process.
	 */
	public function remove() {
		return NULL;
	}

	/**
	 * Not used in the media picker process.
	 */
	private function _getContext() {
		return NULL;
	}

	private function _getLink($category){
		$link = parse_url($_SERVER['REQUEST_URI']);
		$queries = explode('&',$link['query']);
		foreach ($queries as $qk => $qv) {
			list($key,$val) = explode('=',$qv);
			$ql[$key] = $val;
		}
		// Reasign the CID
		$ql['category_id'] = $category['Category_ID'];
		foreach ($ql as $qk => $qv) {
			$ql[$qk] = $qk.'='.$qv;
		}
		$queries = implode('&',$ql);
		return $link['path'].'?'.$queries;
	}

	public function format_list($format,$environment='console',$medium='computer') {
		$list = NULL;

		switch($format):
		case('details'):
			// Create the list object and include the context menu.
			$list = new ECMS_View_Details(array('container_id'=>'folder_list_'.rand(100,999),'item_class'=>'folder_item'));

			// Create the list columns.
			$list->addColumn('icon', NULL, array('width'=>24,'align'=>'centre','type'=>'icon'));
			$list->addColumn('title','Title');
			$list->addColumn('description','Description', array('height'=>100));
			$list->addColumn('lastedit','Last Edited');

			// Add records.
			if ($this->get_total()) { foreach ($this->get_results() as $category) {
				$columns = array();

				if (is_object($category)) {

					// TODO: Expand to accommodate object lists as well.

				} else {
					// Category Icon
					if ((isset($category['active'])) && ($category['active'])) { $columns['icon'] = "ECCAT.category.png"; } else { $columns['icon'] = "ECCAT.category-off.png"; }

					// Title & Display
					$columns['title'] = $category['title'];
					if (ECMS_Registry::get('user')->get_pref('display_system_variables')) {
						$columns['title'] .= ' (<span class="ecms-body-sm ecms-grey">'.$category['Category_ID'].'</span>)';
					}

					// Description
					if ($category['description']) { $columns['description'] = $category['description']; } else { $columns['description'] = NULL; }

					// Last Edit
					$columns['lastedit'] = ECUTL_Datetime::format($category['lastedit']);

					// Record Link
					$link = $this->_getLink($category);
				}

				// Add this record to the list.
				$list->addRecord($columns,$link);
			} }
			break;
		case('tiles'):
			// Create the list object and include the context menu.
			$list = new ECMS_View_Tiles(array('container_id'=>'folder_list_'.rand(100,999),'item_class'=>'folder_item'));

			// Add records.
			if ($this->get_total()) {
				foreach ($this->get_results() as $category) {
					$rows = array();

					if (is_object($category)) {

						// TODO: Expand to accommodate object lists as well.

					} else {
						// Title
						$rows['title'] = $category['title'];

						// Icon
						if ((isset($category['locked'])) && ($category['locked'])) {
							if ((isset($category['active'])) && ($category['active'])) {
								$rows['icon'] = "ECCAT.category-locked.png";
							} else {
								$rows['icon'] = "ECCAT.category-locked-off.png";
							}
						} else {
							if ((isset($category['active'])) && ($category['active'])) {
								$rows['icon'] = "ECCAT.category.png";
							} else {
								$rows['icon'] = "ECCAT.category-off.png";
							}
						}
						$rows['sys_icon'] = NULL;

						// Subcats
						$rows['row1'] = NULL;

						// Last Edit
						$rows['row2'] = ECUTL_Datetime::format($category['lastedit']);

						// EMPTY
						$rows['row3'] = NULL;

						// Link
						$link = $this->_getLink($category);
					}

					// Add this record to the list.
					$list->addRecord($rows,$link);
				}
			}
			break;
		case('icons'):
		case('thumbnails'):
			// Create the list object and include the context menu.
			$list = new ECMS_View_Icons(array('container_id'=>'folder_list_'.rand(100,999),'item_class'=>'folder_item'));
			if ($format == 'icons') { $list->setSize('icon'); } else { $list->setSize('thumb'); }

			// Add records.
			if ($this->get_total()) { foreach ($this->get_results() as $category) {
				$parameters = array();

				if (is_object($category)) {

					// TODO: Expand to accommodate object lists as well.

				} else {
					// Title
					$parameters['title'] = $category['title'];

					// Hover
					$parameters['hover'] = 'Last Edit: '. ECUTL_Datetime::format($category['lastedit']);

					// Size specific parameters.
					if ($format == 'icons') {
						// Icon
						if ((isset($category['locked'])) && ($category['locked'])) {
							if ((isset($category['active'])) && ($category['active'])) {
								$parameters['icon'] = "ECCAT.category-locked.png";
							} else {
								$parameters['icon'] = "ECCAT.category-locked-off.png";
							}
						} else {
							if ((isset($category['active'])) && ($category['active'])) {
								$parameters['icon'] = "ECCAT.category.png";
							} else {
								$parameters['icon'] = "ECCAT.category-off.png";
							}
						}
					} elseif ($format == 'thumbnails') {
						// Media Contents
						$parameters['media'] = NULL;

						// Get up to 4 random media files from this folder.
						$media = new ECMED_Media_List(array('categories'=>$category['Category_ID'],'limit'=>4,'order'=>'random','status'=>1));

						// Add the media files to the parameter list for use in the overlay.
						if ($media->get_total()) {
							foreach ($media->get_results() as $file) {
								$icon = ECMED_Utility::getIconByMime($file['fileType']);
								$icon = str_replace('<ecms>MEDIA_ID</ecms>', $file['Media_ID'],(string) $icon);
								$parameters['media'][] = $icon;
							}
						}
					}

					// Link
					$link = $this->_getLink($category);
				}

				// Add this record to the list.
				$list->addRecord($parameters,$link);
			} }
			break;
		endswitch;

		return $list;
	}
}