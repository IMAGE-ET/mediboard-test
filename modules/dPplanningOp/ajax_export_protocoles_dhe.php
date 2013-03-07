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
  "Pr�nom du praticien",
  "Motif d'hospitalisation",
  "Dur�e d'intervention",
  "Actes CCAM",
  "Type d'hospitalisation",
  "Dur�e d'hospitalisation",
  "Dur�e USCPO",
  "Dur�e pr�op",
  "Pr�sence pr�op",
  "Pr�sence postop",
  "UF d'h�bergement",
  "UF de soins",
  "UF m�dicale",
));

CMbObject::massLoadFwdRef($protocoles, "chir_id");
CMbObject::massLoadFwdRef($protocoles, "function_id");

foreach ($protocoles as $_protocole) {
  $_protocole->loadRefUfHebergement();
  $_protocole->loadRefUfMedicale();
  $_protocole->loadRefUfSoins();
  $_protocole->loadRefChir();
  $_protocole->loadRefFunction();

  $csv->writeLine(array(
    $_protocole->_ref_function->text,
    $_protocole->_ref_chir->_user_last_name,
    $_protocole->_ref_chir->_user_first_name,
    $_protocole->libelle,
    CMbDT::transform($_protocole->temp_operation, null, "%H:%M"),
    $_protocole->codes_ccam,
    $_protocole->type,
    $_protocole->duree_hospi,
    $_protocole->duree_uscpo,
    $_protocole->duree_preop ? CMbDT::transform($_protocole->duree_preop, null, "%H:%M") : "",
    $_protocole->presence_preop ? CMbDT::transform($_protocole->presence_preop, null, "%H:%M") : "",
    $_protocole->presence_postop ? CMbDT::transform($_protocole->presence_postop, null, "%H:%M") : "",
    $_protocole->_ref_uf_hebergement->code,
    $_protocole->_ref_uf_medicale->code,
    $_protocole->_ref_uf_soins->code,
  ));
}

$csv->stream("export-protocoles-".($chir_id ? $chir->_view : $function->text));
