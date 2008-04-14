<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision: $
* @author Romain Ollivier
*/

global $dPconfig, $g;

$list_sejours = mbGetValueFromGet("list_sejours", array());
$date_debut   = mbGetValueFromGet("date_debut"  , mbDate());
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

mbTrace($requestParams, "Paramètres requete");

$results = $client->GetInfoLabo($requestParams);

mbTrace($results, "Résultats");

/*
$results = array(
  "listeInfoLabo" => array(
    1 => array(
      "NumSejour"       => "12345",
      "IsLabo"          => 1,
      "IsLaboEntreDate" => 1,
      "DateLaboDernier" => $date_debut
    ),
    2 => array(
      "NumSejour"       => "67890",
      "IsLabo"          => 1,
      "IsLaboEntreDate" => 1,
      "DateLaboDernier" => $date_debut
    ),
  ),
  "ex" => 0
);
*/
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("results", $results);

$smarty->display("httpreq_soap_labo_results.tpl");

?>