<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPhospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

global $m;

$entree = $_POST["entree"];
$sortie = $_POST["sortie"];


// Modifier la première affectation
$do = new CDoObjectAddEdit("CAffectation", "affectation_id");

$_POST["entree"] = $entree;
$_POST["sortie"] = $_POST["_date_split"];

$do->redirect = null;
$do->redirectStore = null;
$do->doIt();

$first_affectation = $do->_obj;

// Créer la seconde
$do = new CDoObjectAddEdit("CAffectation", "affectation_id");

$_POST["ajax"] = 1;
$_POST["entree"] = $_POST["_date_split"];
$_POST["sortie"] = $sortie;
$_POST["lit_id"] = $_POST["_new_lit_id"];
$_POST["affectation_id"] = null;

$do->doSingle(false);

// Gérer le déplacement du ou des bébés si nécessaire
if (CModule::getActive("maternite")) {
  $affectations_enfant = $first_affectation->loadBackRefs("affectations_enfant");
  
  foreach ($affectations_enfant as $_affectation) {
    $save_sortie = $_affectation->sortie;
    
    $_affectation->sortie = $_POST["_date_split"];
    
    if ($msg = $_affectation->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
    
    $affectation = new CAffectation;
    $affectation->lit_id = $_POST["_new_lit_id"];
    $affectation->sejour_id = $_affectation->sejour_id;
    $affectation->parent_affectation_id = $do->_obj->_id;
    $affectation->entree = $_POST["_date_split"];
    $affectation->sortie = $save_sortie;
    
    if ($msg = $affectation->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
  }
}

$do->doRedirect();
