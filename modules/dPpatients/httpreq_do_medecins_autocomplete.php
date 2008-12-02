<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatient
* @version $Revision$
* @author Fabien Ménager
*/

global $can, $AppUI;

$keywords = mbGetValueFromPost("_view");

if($can->read && $keywords) {
  $medecin = new CMedecin();
  $default_cp = str_pad(@$AppUI->user_prefs["DEPARTEMENT"], 2, "0", STR_PAD_LEFT);
  $where = array();
  $where['cp'] = "LIKE '".$default_cp."___'";
  $where[] = "nom LIKE '%$keywords%' OR prenom LIKE '%$keywords%'";
  $matches = $medecin->loadList($where, 'nom', 30);
  
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("keywords", $keywords);
  $smarty->assign("matches", $matches);
  $smarty->assign("nodebug", true);

  $smarty->display("httpreq_do_medecins_autocomplete.tpl");
}
?>
