<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsEdit();

CMedicap::makeURLs();
$serviceURL = CMedicap::$urls["soap"]["documents"];

if (!url_exists($serviceURL)) {
  CAppUI::stepMessage(UI_MSG_ERROR, "Serveur wep inatteignable à l'adresse : $serviceURL");
  return;
}

$client = new SoapClient("$serviceURL?WSDL", array('exceptions' => 0));

$requestParams = array (
  "aLoginApplicatif"       => CAppUI::conf("ecap soap user"),
  "aPasswordApplicatif"    => CAppUI::conf("ecap soap pass"),
  "aTypeIdentifiantActeur" => 1,
  "aIdentifiantActeur"     => "pr1",
  "aIdClinique"            => "021", //CAppUI::conf("dPsante400 group_id"),
  "aTypeObjet"             => "",
);

class CEcapTypeDocument {
  var $level = 0;
  var $id = null;
  var $libelle = "";
  var $cnCode = "";
  var $cnType = "";

	static function flattenTypes($listeTypeDocument, $level = 0) {
	  $types = array();
	  
	  foreach ($listeTypeDocument->typeDocument as $typeDocument) {
	    $type = new CEcapTypeDocument();
	    $type->level = $level;
	    $type->id      = utf8_decode($typeDocument->idTypeDocument);
	    $type->libelle = utf8_decode($typeDocument->libelleTypeDocument);
	    $type->cnCode  = utf8_decode($typeDocument->classificationNationaleCode);
	    $type->cnType  = utf8_decode($typeDocument->classificationNationaleType);
	    $types[] = $type;
	    
	    $types = array_merge($types, self::flattenTypes($typeDocument->listeTypeDocument, $level+1));
	  }
	  
	  return $types;
	}
}

$typesEcapByMbClass= array();
foreach (CEcDocumentSender::$sendables as $mbClass => $typesEcapByEcObject) {
  foreach ($typesEcapByEcObject as $ecObject) {
    $requestParams["aTypeObjet"] = $ecObject;
    $result = $client->ListerTypeDocument($requestParams);
		$result = simplexml_load_string($result->ListerTypeDocumentResult->any);
		if ($result->codeRetour != "0") {
		  $warning = sprintf("Erreur d'appel au service web e-Cap avec les paramètres '%s' : [%s] %s ", 
		    http_build_query($requestParams),
		    $result->codeRetour,
		    utf8_decode($result->descriptionRetour));
		  trigger_error($warning, E_USER_WARNING);
		  continue;
		}
		
		$typesEcapByMbClass[$mbClass][$ecObject] = CEcapTypeDocument::flattenTypes($result->listeTypeDocument);
  }
}

$categories = CFilesCategory::loadListByClass();
foreach ($categories as &$_catsByClass) {
  foreach ($_catsByClass as &$_category) {
    $idEcap = new CIdSante400();

    $idEcap->loadLatestFor($_category, "ecap type");
    $idsEcap[$_category->_id] = $idEcap;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("typesEcapByMbClass", $typesEcapByMbClass);
$smarty->assign("idsEcap"           , $idsEcap);
$smarty->assign("categories"        , $categories);

$smarty->display("manage_categories.tpl");

?>