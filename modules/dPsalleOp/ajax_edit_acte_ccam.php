<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23384 $
 */

$acte_id = CValue::get("acte_id");

$acte = new CActeCCAM();
$acte->load($acte_id);
$acte->canDo();

if (!$acte->_can->edit) {
  CAppUI::redirect("m=system&a=access_denied");
}

$acte->getTarif();

// Chargement du code, de l'activité et de la phase CCAM
$code     = $acte->_ref_code_ccam;
$activite = $code->activites[$acte->code_activite];
$phase    = $activite->phases[$acte->code_phase];

$listModificateurs = $acte->modificateurs;
foreach ($phase->_modificateurs as $modificateur) {
  $position = strpos($listModificateurs, $modificateur->code);
  if ($position !== false) {
    if ($modificateur->_double == "1") {
      $modificateur->_checked = $modificateur->code;
    }
    elseif ($modificateur->_double == "2") {
      $modificateur->_checked = $modificateur->code.$modificateur->_double;
    }
    else {
      $modificateur->_checked = null;
    }
  }
  else {
    $modificateur->_checked = null;
  }
}

/* Vérification et précodage des modificateurs */
CCodageCCAM::precodeModifiers($phase->_modificateurs, $acte, $acte->loadRefObject());
$acte->getMontantModificateurs($phase->_modificateurs);

// Liste des dents CCAM
$liste_dents = reset(CDentCCAM::loadList());

// Chargement des listes de praticiens
$user = new CMediusers();
$listAnesths = $user->loadAnesthesistes(PERM_DENY);
$listChirs   = $user->loadPraticiens(PERM_DENY);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("acte"       , $acte);
$smarty->assign("code"       , $code);
$smarty->assign("activite"   , $activite);
$smarty->assign("phase"      , $phase);
$smarty->assign("liste_dents", $liste_dents);
$smarty->assign("listAnesths", $listAnesths);
$smarty->assign("listChirs"  , $listChirs);

$smarty->display("inc_edit_acte_ccam.tpl");