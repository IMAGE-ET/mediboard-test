<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision: 7816 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkAdmin();

$path      = CValue::get("path");
$do_import = CValue::get("do_import");
$type      = CValue::get("type");
$limit     = CValue::get("limit");

$path = CAppUI::conf("dPfiles CFile upload_directory")."/$path";

$count = CMbPath::countFiles($path);

if (!$do_import) {
  CAppUI::stepAjax("$count fichiers '$type' à passer en échanges HPRIM");
} else {
  if ($count <= 0) {
    CAppUI::stepAjax("Aucun fichier à passer en échange HPRIM", UI_MSG_ERROR);
  }
  
  $evt = $class = null;
  if ($type == "pmsi") {
    $domEvenement = new CHPrimXMLEvenementsPmsi();
    $evt = "evenementsPMSI";
  } elseif ($type ==  "actes") {
    $domEvenement = new CHPrimXMLEvenementsServeurActes();
    $evt = "evenementsServeurActes";
  } else {
    CAppUI::stepAjax("Type de l'échange invalide", UI_MSG_ERROR);
  }

  $files = CAppUI::readFiles($path);
	
	ini_set("memory_limit", "512M");
  set_time_limit(360);
  CMbObject::$useObjectCache = false;
  $counter = 0;
  foreach ($files as $_file) {
    $xmlfile = file_get_contents("$path/$_file");
    // Chargement du fichier XML
    $domEvenement->loadXML($xmlfile);

    // Création de l'échange
    $echg_hprim = new CEchangeHprim();
    $data = $domEvenement->getEnteteEvenementXML($evt);
    $data = array_merge($data, $domEvenement->getContentsXML());
    $dest_hprim = new CDestinataireHprim();
    $dest_hprim->register($data['idClient']);
    $echg_hprim->date_production = $data['dateHeureProduction'];
    $echg_hprim->date_echange    = date("Y-m-d H:m:s", filemtime("$path/$_file"));
    $echg_hprim->group_id        = CGroups::loadCurrent()->_id;
    $echg_hprim->destinataire_id = $dest_hprim->_id;
    $echg_hprim->type            = $domEvenement->type;
    $echg_hprim->sous_type       = $domEvenement->sous_type;
    $echg_hprim->_message         = utf8_encode($xmlfile);
    $doc_valid                   = $domEvenement->schemaValidate();
    $echg_hprim->message_valide  = $doc_valid ? 1 : 0;
    if ($type == "pmsi") {
      $echg_hprim->object_class = "CSejour";
      $echg_hprim->object_id    = str_replace("sj", "", $data['idSourceVenue']);
      $echg_hprim->id_permanent = $data['idCibleVenue'];
    } elseif ($type ==  "actes") {
      $echg_hprim->object_class = "COperation";
      $echg_hprim->object_id    = str_replace("op", "", $data["idSourceIntervention"]);
    }
    $echg_hprim->store();
           
    // Passage du séjour/opération en facturé
    $object = new $echg_hprim->object_class;
    $object->load($echg_hprim->object_id);
    $object->facture = 1;
    $msg = $object->store();

    // Suppression du fichier sur le disque
    if (!$msg) {
      unlink("$path/$_file");
    }
    
    $counter++;
    
    if ($counter == $limit) {
      CAppUI::stepAjax("Traitement de $counter fichiers terminé");
      return;
    }
  }
}

?>