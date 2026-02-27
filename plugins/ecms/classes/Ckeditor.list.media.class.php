<?php
/**
 * CKEditor - Media List Picker Class
 *
 *
 *
 *
 *
 *
 */

class Ckeditor_List_Media extends ECMED_Media_List {

	/**
	 * Not used in the media picker process.
	 */
	public function remove() {
		return NULL;
	}

	private function _getLink($media){
		switch(ECMED_Utility::get_type_by_mime($media['fileType'])):
		case(ECMED_Utility::ECMED_DOCUMENT):
			// URL,EXT,TITLE,INFO
			return "javascript:selectDoc(".
			"'".'/media.php?mid='.$media['Media_ID'].'&amp;title='.urlencode(htmlspecialchars(strip_tags(str_replace('"','', (string)$media['title'])),ENT_QUOTES,ECMS_CHARSET))."',".
						"'".ECMS_ICON16.'MIME.'.ECMED_Utility::getExtensionByMime($media['fileType']).".png',".
						"'".addslashes(htmlspecialchars(str_replace('"','', (string)$media['title']),ENT_COMPAT,ECMS_CHARSET))."',".
						"'".strtoupper(ECMED_Utility::getExtensionByMime($media['fileType'])).': '.ECUTL_Format::size($media['fileSize'])."');";
		case(ECMED_Utility::ECMED_IMAGE):
		default:
			// URL,ALT,TITLE
			$size = ECMED_Utility::resize_image($media['fileWidth'],$media['fileHeight'],500,500);
			return "javascript:selectImage(
						'".'/media.php?mid='.$media['Media_ID'].'&amp;sid='.$size['fileWidth'].'x'.$size['fileHeight']."',
						'".addslashes(htmlspecialchars((string)$media['title'],ENT_COMPAT,ECMS_CHARSET))."',
						'".addslashes(htmlspecialchars((string)$media['title'],ENT_COMPAT,ECMS_CHARSET))."');";
		endswitch;
	}

	public function format_list($format,$environment='console',$medium='computer') {
		$list = NULL;

		switch($format):
		case('details'):
			// Create the list object and include the context menu.
			$list = new ECMS_View_Details(array('container_id'=>'med_files','item_class'=>'med_file'));
			$list->addContext( new Ckeditor_Context_Media );
			$list->setLinkType('js');

			// Create the list columns.
			$list->addColumn('icon', NULL, array('width'=>24,'align'=>'centre','type'=>'icon'));
			$list->addColumn('title','Title');
			$list->addColumn('description','Description',array('height'=>100));
			$list->addColumn('type','Mime Type',array('align'=>'centre'));
			$list->addColumn('size','Size',array('align'=>'centre'));
			$list->addColumn('dimensions','Dimensions (W x H)',array('align'=>'centre'));
			$list->addColumn('lastedit','Last Edited');

			// Add records.
			if ($this->get_total()) { foreach ($this->get_results() as $media) {
				$columns = array();
				$context = array();

				if (is_object($media)) {

					// TODO: Expand to accommodate object lists as well.

				} else {
					// Media Icon
					$columns['icon'] = ECMED_Utility::getIconByMime($media['fileType'],$media['active']);
					$columns['icon'] = str_replace('<ecms>MEDIA_ID</ecms>', (string)$media['Media_ID'], $columns['icon']);
					$columns['icon'] = str_replace('<ecms>SIZE_ID</ecms>', (string)'16x16', $columns['icon']);

					// Title
					$columns['title'] = $media['title'];

					// Description
					if (isset($media['description'])) { $columns['description'] = $media['description']; } else { $columns['description'] = NULL; }

					// Type
					$columns['type'] = $media['fileType'];

					// Size
					if ($media['fileSize'] < 1024) {
						$columns['size'] = number_format($media['fileSize'],1).'B';
					} elseif ($media['fileSize'] >= 1024 && $media['fileSize'] < 1048576) {
						$columns['size'] = number_format(($media['fileSize']/1024),1).'KB';
					} else {
						$columns['size'] = number_format(($media['fileSize']/1048576),1).'MB';
					}

					// Dimensions
					if (isset($media['fileWidth']) && isset($media['fileHeight'])) { $columns['dimensions'] = $media['fileWidth'].' x '.$media['fileHeight']; } else { $columns['dimensions'] = 'N/A'; }

					// Last Edit
					$columns['lastedit'] = ECUTL_Datetime::format($media['lastedit']);

					// Record Link
					$link = $this->_getLink($media);

					// Context menu variables.
					$context['id'] = $media['Media_ID'];

				}

				// Add this record to the list.
				$list->addRecord($columns,$link,$context);
			} }
			break;
		case('tiles'):
			// Create the list object and include the context menu.
			$list = new ECMS_View_Tiles(array('container_id'=>'med_files','item_class'=>'med_file'));
			$list->addContext( new Ckeditor_Context_Media );

			// Add records.
			if ($this->get_total()) { foreach ($this->get_results() as $media) {
				$rows = array();
				$context = array();

				if (is_object($media)) {

					// TODO: Expand to accommodate object lists as well.

				} else {
					// Media Icon
					$rows['icon'] = ECMED_Utility::getIconByMime($media['fileType'],$media['active'],'32x32');
					$rows['icon'] = str_replace('<ecms>MEDIA_ID</ecms>', $media['Media_ID'], (string)$rows['icon']);
					$rows['icon'] = str_replace('<ecms>SIZE_ID</ecms>', '64x64', (string)$rows['icon']);

					// Title
					$rows['title'] = $media['title'];

					// Type
					$rows['row1'] = $media['fileType'];

					// Size
					if ($media['fileSize'] < 1024) {
						$rows['row2'][] = number_format($media['fileSize'],1).'B';
					} elseif ($media['fileSize'] >= 1024 && $media['fileSize'] < 1048576) {
						$rows['row2'][] = number_format(($media['fileSize']/1024),1).'KB';
					} else {
						$rows['row2'][] = number_format(($media['fileSize']/1048576),1).'MB';
					}

					// Dimensions
					if (isset($media['fileWidth']) && isset($media['fileHeight'])) {
						$rows['row2'][] = $media['fileWidth'].' x '.$media['fileHeight'];
					}

					// Usage
					$rows['row2'][] = 'Used '.$media['used'].'x';

					// Condense Row 2
					$rows['row2'] = implode(', ',$rows['row2']);

					// Last Edit
					$rows['row3'] = ECUTL_Datetime::format($media['lastedit']);

					// Link
					$link = $this->_getLink($media);

					// Context menu variables.
					$context['id'] = $media['Media_ID'];

				}

				// Add this record to the list.
				$list->addRecord($rows,$link,$context);
			} }

			break;
		case('icons'):
		case('thumbnails'):
			// Create the list object and include the context menu.
			$list = new ECMS_View_Icons(array('container_id'=>'med_files','item_class'=>'med_file'));
			$list->addContext( new Ckeditor_Context_Media );
			if ($format == 'icons') { $list->setSize('icon'); } else { $list->setSize('thumb'); }

			// Add records.
			if ($this->get_total()) { foreach ($this->get_results() as $media) {
				$parameters = array();
				$context = array();

				if (is_object($media)) {

					// TODO: Expand to accommodate object lists as well.

				} else {

					// Media Icon / Thumbnail
					$parameters['icon'] = ECMED_Utility::getIconByMime($media['fileType'],$media['active']);
					$parameters['icon'] = str_replace('<ecms>MEDIA_ID</ecms>', $media['Media_ID'], (string)$parameters['icon']);

					// Title
					$parameters['title'] = $media['title'];

					// Hover
					$parameters['hover'] = $media['title'];

					// Link
					$link = $this->_getLink($media);

					// Context menu variables.
					$context['id'] = $media['Media_ID'];

				}

				// Add this record to the list.
				$list->addRecord($parameters,$link,$context);
			} }
			break;
		endswitch;
		return $list;
	}
}

/**
 * ECMS Media Warehouse :: Media Picker Context Menu
*
*
*
*
*
*
* @package		ECORE
*/

class Ckeditor_Context_Media implements ECMS_List_Context_Interface {

	/**
	 * Construct the list context menu.
	 * @return object
	 */
	public static function get($container,$item_class) {
		$context = new ECUTL_Context($container,$item_class);
		$context->addItem(new ECUTL_Context_Item('preview','Preview',array('type'=>'js','script'=>"<ecms>PREVIEW</ecms>"),'fa fa-eye','fa fa-eye ecms-status__disabled'),1);
		return $context;
	}

}
