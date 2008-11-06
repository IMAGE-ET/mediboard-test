<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatient
* @version $Revision$
* @author Fabien Ménager
*/

global $can;

$keywords = @$_GET[$_GET["keywords_field"]];

if($can->read && $keywords) {
  $medecin = new CMedecin();
  $matches = $medecin->seek(explode(' ', $keywords), 30);
  
  // Création du template
  $smarty = new CSmartyDP();

  $smarty->assign("keywords", $keywords);
  $smarty->assign("matches", $matches);
  $smarty->assign("nodebug", true);

  $smarty->display("httpreq_do_medecins_autocomplete.tpl");
}
?>
