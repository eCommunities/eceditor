<?php
require_once(__DIR__.'/../../../../ecms/ECMS.inc.php');
require_once('classes/Ckeditor.list.folders.class.php');
require_once('classes/Ckeditor.list.media.class.php');

ECUGM_Utility::is_authorized();

// Set or reset any important session vars.
if (isset($_REQUEST['category_id'])) { $_SESSION['ECMED_CategoryID'] = $_REQUEST['category_id']; } else { $_SESSION['ECMED_CategoryID'] = NULL; }
$_SESSION['ECMED_TemplateID'] = NULL;

// Set the list style.
$ECMS_listFormatID = 'ECMED_listMedia';
$ECMS_listFormat = array("details" => "Details","tiles" => "Tiles","icons" => "Icons","thumbnails" => "Thumbnails");
$ECMS_listFormatPref = ECMS_Registry::get('user')->get_pref($ECMS_listFormatID);
if (!$ECMS_listFormatPref) { $ECMS_listFormatPref = 'tiles'; }

/**
 * Media Filters Preference
 *  1: Images
 *  2: Documents
 *  4: Audio/Video
 *  8: Applications
 * 16: Other
 */
$medFiltersDec = ECMS_Registry::get('user')->get_pref('filters','ECMED');
if (!isset($medFiltersDec)) { $medFiltersDec = 31; }
$medFiltersBin = decbin($medFiltersDec);

// Limit media to requested type...
switch ($_REQUEST['type']) :
case ('doc'):
case ('document'):
	$filter = ECMED_Utility::ECMED_DOCUMENT;
	break;
case('img'):
case('image'):
	$filter = ECMED_Utility::ECMED_IMAGE;
	break;
default:
	trigger_error('Supplied filter type not valid.',E_USER_ERROR);
	break;
endswitch;

// Current Folder
if (!isset($_SESSION['ECMED_CategoryID']) || !ECUTL_Uuid::is_valid($_SESSION['ECMED_CategoryID'])) {
	// Root Media Folder
	$roots = new ECMED_Folder_List(array('categories'=>FALSE,'module'=>'ECMED','template_function'=>'catset'));
	$_SESSION['ECMED_RootID'] = $roots->get_results(0,'Category_ID');
	$category = NULL;
	$parentLink = '#';
} else {
	/* @var $cat_mgr ECCAT_Category */
	$cat_mgr = ECMS_Service_Locator::ECCAT('category');
	$category = $cat_mgr->read($_SESSION['ECMED_CategoryID']);

	$link = parse_url($_SERVER['REQUEST_URI']);
	$queries = explode('&',$link['query']);
	foreach ($queries as $qk => $qv) {
		list($key,$val) = explode('=',$qv);
		$ql[$key] = $val;
	}
	// Reasign the CID
	$ql['category_id'] = $category->get_parent();

	foreach ($ql as $qk => $qv) { $ql[$qk] = $qk.'='.$qv; }
	$queries = implode('&',$ql);
	$parentLink = $link['path'].'?'.$queries;
}

// Set Tab
if (isset($_REQUEST['tab'])) { $tab = $_REQUEST['tab']; } else { $tab = 'picker'; }

// PICKER TAB

// Contents List
$cLimit = 100;
$cOffset = 0;
$cQueries = array('sby_contents'=>NULL,'sor_contents'=>NULL,'order'=>NULL,'terms'=>NULL);
if (isset($_REQUEST['sby_contents'])) { $cQueries['sby_contents'] = $_REQUEST['sby_contents']; }
if (isset($_REQUEST['sor_contents'])) { $cQueries['sor_contents'] = $_REQUEST['sor_contents']; }
if (isset($_REQUEST['terms']) && ($_REQUEST['terms'])) { $cQueries['terms'] = $_REQUEST['terms']; }
if (isset($_REQUEST['page'])) { $cQueries['page'] = $_REQUEST['page']; if ($_REQUEST['page'] > 1) { $cOffset = (($_REQUEST['page']-1) * $cLimit); } } else { $cQueries['page'] = 1; }
if (isset($_SESSION['ECMED_CategoryID'])) { $cQueries['category_id'] = $_SESSION['ECMED_CategoryID']; }
$cQueries['type'] = $_REQUEST['type'];
$cQueries['list_views'] = array('details'=>TRUE,'tiles'=>TRUE,'icons'=>TRUE,'thumbnails'=>TRUE);
$contents = new ECXML_List_Iterator(array('limit'=>array($cLimit,$cOffset)));
$contents->register('Ckeditor_List_Folders',array('categories'=>($_SESSION['ECMED_CategoryID']?$_SESSION['ECMED_CategoryID']:$_SESSION['ECMED_RootID']),'module'=>'ECMED','template_function'=>'category','status'=>1));
$contents->register('Ckeditor_List_Media',array('categories'=>($_SESSION['ECMED_CategoryID']?$_SESSION['ECMED_CategoryID']:$_SESSION['ECMED_RootID']),'status'=>1,'types'=>$filter,'filters'=>$medFiltersDec));
$contents->run();

$mediaList = new ECMS_Console_List_Publisher('ecmed_list_media',$contents,$cQueries);
$cNav = new ECUTL_Pagination($_SERVER['REQUEST_URI'], $contents->get_total(),$cLimit,$cQueries['page'],$cQueries);

// Tab
$picker_tab = new ECMS_Console_Tab('picker', 'Media Picker', array('active'=>($tab == 'picker' ? TRUE : FALSE)));
ECMS_Console::add_tab( $picker_tab );

// Menu
$picker_tab->add_menu_item( new ECMS_Console_Menu_Item('upload', 'Upload Files', ECMS_WEBROOT.'ECMED/add_multi_media.php?type='.$_REQUEST['type'].(isset($_REQUEST['category_id']) ? '&category_id='.$_REQUEST['category_id'] : NULL), 'fa fa-upload') );
$picker_tab->add_menu_item( new ECMS_Console_Menu_Item('up_dir', 'Up Directory', $parentLink, 'fa fa-folder-open') );
$picker_tab->add_menu_item( new ECMS_Console_Menu_Item('cancel', 'Cancel Selection', 'add_multi_media.php', 'ECORE.inactive.png',NULL,"$('html', window.parent.document).css('overflow','visible'); $('#ecms_warehouse_browser', window.parent.document).remove();") );

// Body
$picker_tab->set_body('children',
		'<h3>Media Explorer'.
		(strlen($medFiltersBin) >= 5 && substr($medFiltersBin,-5,1) == 1 ?
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec-16).'"><img src="'.ECMS_ICON16.'MIME.unknown.png" alt="Other" title="Other" /><img src="'.ECMS_ICON16.'ECORE.check.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Displayed" /></a>'
				:
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec+16).'"><img src="'.ECMS_ICON16.'MIME.unknown.off.png" alt="Other" title="Other" /><img src="'.ECMS_ICON16.'ECORE.cross.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Filtered" /></a>'
		).
		(strlen($medFiltersBin) >= 4 && substr($medFiltersBin,-4,1) == 1 ?
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec-8).'"><img src="'.ECMS_ICON16.'ECMED.application.png" alt="Applications" title="Applications" /><img src="'.ECMS_ICON16.'ECORE.check.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Displayed" /></a>'
				:
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec+8).'"><img src="'.ECMS_ICON16.'ECMED.application.off.png" alt="Applications" title="Applications" /><img src="'.ECMS_ICON16.'ECORE.cross.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Filtered" /></a>'
		).
		(strlen($medFiltersBin) >= 3 && substr($medFiltersBin,-3,1) == 1 ?
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec-4).'"><img src="'.ECMS_ICON16.'ECMED.av.png" alt="Audio / Video" title="Audio / Video" /><img src="'.ECMS_ICON16.'ECORE.check.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Displayed" /></a>'
				:
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec+4).'"><img src="'.ECMS_ICON16.'ECMED.av.off.png" alt="Audio / Video" title="Audio / Video" /><img src="'.ECMS_ICON16.'ECORE.cross.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Filtered" /></a>'
		).
		(strlen($medFiltersBin) >= 2 && substr($medFiltersBin,-2,1) == 1 ?
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec-2).'"><img src="'.ECMS_ICON16.'ECMED.document.png" alt="Documents" title="Documents" /><img src="'.ECMS_ICON16.'ECORE.check.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Displayed" /></a>'
				:
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec+2).'"><img src="'.ECMS_ICON16.'ECMED.document.off.png" alt="Documents" title="Documents" /><img src="'.ECMS_ICON16.'ECORE.cross.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Filtered" /></a>'
		).
		(strlen($medFiltersBin) >= 1 && substr($medFiltersBin,-1,1) == 1 ?
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec-1).'"><img src="'.ECMS_ICON16.'ECMED.image.png" alt="Images" title="Images" /><img src="'.ECMS_ICON16.'ECORE.check.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Displayed" /></a>'
				:
				'<a class="ecms-nav-item-right" href="'.ECMS_WEBROOT.'ECUGM/set_preference.php?ECMS_moduleID=ECMED&amp;ECMS_preference=filters&amp;ECMS_value='.($medFiltersDec+1).'"><img src="'.ECMS_ICON16.'ECMED.image.off.png" alt="Images" title="Images" /><img src="'.ECMS_ICON16.'ECORE.cross.png" style="width:8px; height:8px; position:relative; left:-4px; bottom:-2px;" alt="Filtered" /></a>'
		).

		'<img src="'.ECMS_ICON16.'navdivide.gif" alt="" width="16" height="16" class="ecms-nav-row-right" />'.
		$mediaList->get_tool('picker','Media').
		'</h3>'.
		$cNav->get().$mediaList->get_list('There are currently no media files or folders to list.').($contents->get_set_total() >= 25 ? $cNav->get() : NULL)
);

// ECMS Page Object
ECMS_Console::set_ecms();
ECMS_Console::set_css(new ECPGS_Page_Head_Css('link','/plugins/lightview/css/lightview/lightview.css'));
ECMS_Console::set_script(new ECPGS_Page_Head_Script('link','/scripts/ecnav.tree.js'));
$canvas = new ECPGS_Page_Head_Script('link','/plugins/lightview/js/excanvas/excanvas.js');
$canvas->set_filter('if lt IE 9');
ECMS_Console::set_script($canvas);
ECMS_Console::set_script(new ECPGS_Page_Head_Script('link','/plugins/lightview/js/spinners/spinners.min.js'));
ECMS_Console::set_script(new ECPGS_Page_Head_Script('link','/plugins/lightview/js/lightview/lightview.js'));

ECMS_Console::set_script(new ECPGS_Page_Head_Script('block',
"function selectImage(url, alt, title) { window.parent.setImageData(url, alt, title); $('#ecms_warehouse_browser', window.parent.document).remove(); };".
"function selectDoc(url, icon, title, info) { window.parent.setDocData(url, icon, title, info); $('#ecms_warehouse_browser', window.parent.document).remove(); };"));

ECMS_Console::set_title((isset($category) ? 'Media Picker: '.$category->get_value_by_id('title') : "Media Picker"));
ECMS_Console::set_icon('ECMED.png');

ECMS_Console::publish();
