<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

$list_sejours = explode("-", mbGetValueFromGet("list_sejours"));
$date_debut   = mbGetValueFromGet("date_debut"  , mbDate("-1 day"));
$date_fin     = mbGetValueFromGet("date_fin"    , mbDate());

return true;

// Chargement de l'etablissement courant
$etab = CGroups::loadCurrent();

$results = array();
if (null != $soap_url = CImeds::getSoapUrl()) {	
  $ids = CImeds::getIdentifiants();
  $requestParams = array (
    "strIDC"           => $ids["cidc"],
    "strDIV"           => $ids["cdiv"],
    "strSDV"           => $ids["csdv"],
    "dateDebutPeriode" => $date_debut,
    "dateFinPeriode"   => $date_fin,
    "listeNumSejours"  => $list_sejours,
    "listePatients"    => array(),
    "PWD"              => ""
  );
    
  if (!url_exists($soap_url)) {
    CAppUI::stepAjax("Serveur IMeds inatteignable à l'adresse : $soap_url", UI_MSG_ERROR);
  }
  
  $client = new SoapClient($soap_url."?WSDL", array('exceptions' => 0));
    
  $results = $client->GetInfoLabo($requestParams);
  $countResults = $results->GetInfoLaboResult;
  
  CAppUI::stepAjax("$countResults résultats labo trouvés", UI_MSG_OK);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("httpreq_soap_labo_results.tpl");

?>