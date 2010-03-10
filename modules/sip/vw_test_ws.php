<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7248 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $can;

$can->needsRead();

$operation = CValue::post("operation");
$entier1   = CValue::post("entier1");
$entier2   = CValue::post("entier2");
$file      = CValue::read($_FILES, "file");

$result = $acquittement = $errors = null;

// Si Client
if (!CAppUI::conf('sip server')) {  
  if ($operation && $entier1 && $entier2) {
  	$dest_hprim = new CDestinataireHprim();
	  $dest_hprim->type = "sip";
    $dest_hprim->group_id = CGroups::loadCurrent()->_id;
	  $dest_hprim->loadMatchingObject();
	  
    $source = CExchangeSource::get($dest_hprim->_guid);
    $source->setData(array($operation, $entier1, $entier2), true);
    $source->send("calculatorAuth");
    $result = $source->receive();
  }
  
  if (is_array($file)) {
  	$dest_hprim = new CDestinataireHprim();
	  $dest_hprim->type = "sip";
    $dest_hprim->group_id = CGroups::loadCurrent()->_id;
	  $dest_hprim->loadMatchingObject();
	  
    $document = new CMbXMLDocument();
    $document->load($file["tmp_name"]);
    $msgEvt = utf8_encode($document->saveXML());
    
    $source = CExchangeSource::get($dest_hprim->_guid);
    $source->setData($msgEvt);
    $source->send("evenementPatient");
    $acquittement = $source->receive();
  
    $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
    $domGetAcquittement->loadXML(utf8_decode($acquittement));  
    $errors = $domGetAcquittement->schemaValidate(null, true);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("operation", $operation);
$smarty->assign("entier1"  , $entier1);
$smarty->assign("entier2"  , $entier2);
$smarty->assign("result"   , $result);

$smarty->assign("errors"      , $errors);
$smarty->assign("acquittement", $acquittement);

 
$smarty->display("vw_test_ws.tpl");
?>
