<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

global $dPconfig, $g;

$list_sejours = explode("-", mbGetValueFromGet("list_sejours", array()));
$date_debut   = mbGetValueFromGet("date_debut"  , mbDate("-1 day"));
$date_fin     = mbGetValueFromGet("date_fin"    , mbDate());

// Chargement de l'etablissement courant
$etab = new CGroups;
$etab->load($g);

// Chargement des id400 de l'etablissement courant
$idCSDV = new CIdSante400();
$idCSDV->loadLatestFor($etab, "Imeds csdv");

$idCDIV = new CIdSante400();
$idCDIV->loadLatestFor($etab,"Imeds cdiv");

$idCIDC = new CIdSante400();
$idCIDC->loadLatestFor($etab, "Imeds cidc");

$urlImeds = parse_url($dPconfig["dPImeds"]["url"]);

$serviceAdresse = $urlImeds["scheme"]."://".$urlImeds["host"]."/dllimeds/webimeddll.asmx";

$client = new SoapClient($serviceAdresse."?WSDL", array('exceptions' => 0));

$requestParams = array("strIDC"           => "$idCIDC->id400",
                       "strDIV"           => "$idCSDV->id400",
                       "strSDV"           => "$idCSDV->id400",
                       "dateDebutPeriode" => $date_debut,
                       "dateFinPeriode"   => $date_fin,
                       "listeNumSejours"  => $list_sejours,
                       "listePatients"    => array(),
                       "PWD"              => "");

$results = $client->GetInfoLabo($requestParams);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("httpreq_soap_labo_results.tpl");

?>