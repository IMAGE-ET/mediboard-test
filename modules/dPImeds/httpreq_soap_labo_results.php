<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

$list_sejours = explode("-", mbGetValueFromGet("list_sejours", array()));
$date_debut   = mbGetValueFromGet("date_debut"  , mbDate("-1 day"));
$date_fin     = mbGetValueFromGet("date_fin"    , mbDate());

// Chargement de l'etablissement courant
$etab = CGroups::loadCurrent();

// Chargement des id400 de l'etablissement courant
$idCSDV = new CIdSante400();
$idCSDV->loadLatestFor($etab, "Imeds csdv");

$idCDIV = new CIdSante400();
$idCDIV->loadLatestFor($etab,"Imeds cdiv");

$idCIDC = new CIdSante400();
$idCIDC->loadLatestFor($etab, "Imeds cidc");

$results = array();

if (CAppUI::conf("dPImeds url") != '') {
	$urlImeds = parse_url(CAppUI::conf("dPImeds url"));
	$urlImeds['path'] = "/dllimeds/webimeddll.asmx";
	$serviceAdresse = make_url($urlImeds);
  
  if (!url_exists($serviceAdresse)) {
    CAppUI::stepAjax("Serveur IMeds inatteignable à l'addresse : $serviceAdresse", UI_MSG_ERROR);
  }
  
  $client = new SoapClient($serviceAdresse."?WSDL", array('exceptions' => 0));
  
  $requestParams = array (
    "strIDC"           => "$idCIDC->id400",
    "strDIV"           => "$idCSDV->id400",
    "strSDV"           => "$idCSDV->id400",
    "dateDebutPeriode" => $date_debut,
    "dateFinPeriode"   => $date_fin,
    "listeNumSejours"  => $list_sejours,
    "listePatients"    => array(),
    "PWD"              => ""
  );
  
  $results = $client->GetInfoLabo($requestParams);
  $countResults = $results->GetInfoLaboResult;
  
  CAppUI::stepAjax("$countResults résultats labo trouvés", UI_MSG_OK);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("httpreq_soap_labo_results.tpl");

?>