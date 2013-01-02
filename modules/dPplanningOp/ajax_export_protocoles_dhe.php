<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPplanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$chir_id     = CValue::get("chir_id");
$function_id = CValue::get("function_id");

$function = new CFunctions();
$function->load($function_id);

$chir = new CMediusers();
$chir->load($chir_id);

$protocole = new CProtocole();
$protocole->chir_id = $chir_id ? $chir_id : null;
$protocole->function_id = $function_id ? $function_id : null;
$protocole->for_sejour = 0;

$protocoles = $protocole->loadMatchingList("libelle");

if (!$function->_id) {
  $function = $chir->loadRefFunction();
}

$csv = new CCSVFile();

$csv->writeLine(array(
  "Nom de la fonction",
  "Nom du praticien",
  "Prénom du praticien",
  "Motif d'hospitalisation",
  "Durée d'intervention",
  "Actes CCAM",
  "Type d'hospitalisation",
  "Durée d'hospitalisation",
  "Durée USCPO",
  "Durée préop",
  "Présence préop",
  "Présence postop",
  "UF d'hébergement",
  "UF de soins",
  "UF médicale",
));

foreach ($protocoles as $_protocole) {
  $_protocole->loadRefUfHebergement();
  $_protocole->loadRefUfMedicale();
  $_protocole->loadRefUfSoins();
  
  $csv->writeLine(array(
    $function->text,
    $chir->_user_last_name,
    $chir->_user_first_name,
    $_protocole->libelle,
    mbTransformTime($_protocole->temp_operation, null, "%H:%M"),
    $_protocole->codes_ccam,
    $_protocole->type,
    $_protocole->duree_hospi,
    $_protocole->duree_uscpo,
    $_protocole->duree_preop ? mbTransformTime($_protocole->duree_preop, null, "%H:%M") : "",
    $_protocole->presence_preop ? mbTransformTime($_protocole->presence_preop, null, "%H:%M") : "",
    $_protocole->presence_postop ? mbTransformTime($_protocole->presence_postop, null, "%H:%M") : "",
    $_protocole->_ref_uf_hebergement->code,
    $_protocole->_ref_uf_medicale->code,
    $_protocole->_ref_uf_soins->code,
  ));
}

$csv->stream("export-protocoles-".($chir_id ? $chir->_view : $function->text));
