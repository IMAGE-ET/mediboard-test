<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatient
* @version $Revision$
* @author Fabien Ménager
*/

global $can;

$keywords = CValue::post("_view");

if($can->read && $keywords) {
  $medecin = new CMedecin();
  $g = CValue::getOrSessionAbs("g", CAppUI::$instance->user_group);
  $indexGroup = new CGroups;
  $indexGroup->load($g);
  
  
  $where = array();
  if($indexGroup->_cp_court) $where['cp'] = "LIKE '".$indexGroup->_cp_court."___'";
  $where[] = "nom LIKE '$keywords%' OR prenom LIKE '$keywords%'";
  $matches = $medecin->loadList($where, 'nom', 20);
  
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("keywords", $keywords);
  $smarty->assign("matches", $matches);
  $smarty->assign("nodebug", true);

  $smarty->display("httpreq_do_medecins_autocomplete.tpl");
}
