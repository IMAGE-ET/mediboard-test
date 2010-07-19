<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPfiles
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

//$can->needsRead();

$object_class  = CValue::getOrSession("selClass", null);
$object_id     = CValue::getOrSession("selKey"  , null);
$typeVue       = CValue::getOrSession("typeVue" , 0);
$accordDossier = CValue::get("accordDossier"    , 0);
$reloadlist = 1;

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));
$listCategory = CFilesCategory::listCatClass($object_class);

// Id de l'utilisateur courant
$user_id = $AppUI->user_id;

// Chargement de l'utilisateur courant
$mediuser = new CMediusers;
$mediuser->load($user_id);
$mediuser->loadRefs();

$object = null;
$canFile  = new CCanDo;
$praticienId = null;
$affichageFile = array();

if($object_id && $object_class){
  // Chargement de l'objet
  $object = new $object_class;
  $object->load($object_id);
  $canFile = $object->canDo();
  
  // To add the modele selector in the toolbar
  if ($object_class == 'CConsultation') {
    $praticienId = $object->_praticien_id;
  } 
  else if ($object_class == 'CConsultAnesth') {
    $praticienId = $object->_ref_consultation->_praticien_id;
  }
  else if ($object_class == 'CSejour') {
    $praticienId = $object->praticien_id;
  }
  else if ($object_class == 'COperation') {
    $praticienId = $object->chir_id;
  }
  else if ($mediuser->isPraticien()) {
    $praticienId = $mediuser->_id;
  }
  
  $affichageFile = CFile::loadDocItemsByObject($object);

  foreach($affichageFile as $_cat) {
    if (!isset($_cat["items"])) break;

    foreach($_cat["items"] as $data) {
      if ($data->_class_name == "CCompteRendu" && CAppUI::conf("dPcompteRendu CCompteRendu pdf_thumbnails") == 1) {
        $data->loadRefsFwd();
        $data->loadFile();
        $data->loadContent();

        if (!isset($data->_ref_file->_id)) {
          $data->updateFormFields();
          $file = new CFile();
          $file->setObject($data);
          $file->private = 0;
          $file->file_name  = $data->nom . ".pdf";
          $file->file_type  = "application/pdf";
          $file->file_owner = $user_id;
          $file->fillFields();
          $file->updateFormFields();
          $file->forceDir();

          if ($data->_page_format == "") {
            $page_width  = round((72 / 2.54) * $data->page_width, 2);
            $page_height = round((72 / 2.54) * $data->page_height, 2);
            $data->_page_format = array(0, 0, $page_width, $page_height);
          }

          $content = $data->loadHTMLcontent($data->_source, '','','','','','', array($data->margin_top, $data->margin_right, $data->margin_bottom, $data->margin_left));
          $htmltopdf = new CHtmlToPDF;
          $htmltopdf->generatePDF($content, 0, $data->_page_format, $data->_orientation, $file);
          $file->file_size = filesize($file->_file_path);
          $file->store();
          $data->_ref_file = $file;
        }
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affichageFile", $affichageFile);
$smarty->assign("canFile"      , $canFile);
$smarty->assign("reloadlist"   , $reloadlist); 
$smarty->assign("listCategory" , $listCategory);
$smarty->assign("praticienId"  , $praticienId);
$smarty->assign("selClass"     , $object_class);
$smarty->assign("selKey"       , $object_id);
$smarty->assign("object"       , $object);
$smarty->assign("typeVue"      , $typeVue);
$smarty->assign("accordDossier", $accordDossier);

switch($typeVue) {
  case 0 :
    $smarty->display("inc_list_view.tpl");
    break;
  case 1 :
    $smarty->display("inc_list_view_colonne.tpl");
    break;
}

?>