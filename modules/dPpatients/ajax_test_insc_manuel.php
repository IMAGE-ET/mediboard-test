<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$list_person = CValue::get("listPerson");
$list_person = json_decode(stripslashes($list_person));

foreach ($list_person as $_person) {

  $birthDate = $_person->date;
  $firstName = CINSPatient::formatString($_person->prenom);
  if (!$_person->nirCertifie) {
    $_person->insc = "Impossible de calculer";
    continue;
  }
  list($nir, $nirKey) = explode(" ", $_person->nirCertifie);
  $_person->insc = CINSPatient::calculInsc($nir, $nirKey, $firstName, $birthDate);
}

$smarty = new CSmartyDP();
$smarty->assign("list_person", $list_person);
$smarty->display("ins/inc_test_insc_manuel.tpl");