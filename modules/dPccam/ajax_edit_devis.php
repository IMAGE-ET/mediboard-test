<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$devis_id       = CValue::get('devis_id');
$action         = CValue::get('action', 'open');


$devis = new CDevisCodage();

if ($devis_id) {
  $devis->load($devis_id);
  $devis->loadRefCodable();
}

if ($devis->_id) {
  $devis->canDo();
  $devis->loadRefPatient();
  $devis->loadRefPraticien();
  $devis->getActeExecution();
  $devis->countActes();
  $devis->loadRefsActes();

  foreach ($devis->_ref_actes as $_acte) {
    $_acte->loadRefExecutant();
  }

  $devis->loadPossibleActes();

  if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
    // Chargement des règles de codage
    $devis->loadRefsCodagesCCAM();
    foreach ($devis->_ref_codages_ccam as $_codages_by_prat) {
      foreach ($_codages_by_prat as $_codage) {
        $_codage->loadPraticien()->loadRefFunction();
        $_codage->loadActesCCAM();
        $_codage->getTarifTotal();
        foreach ($_codage->_ref_actes_ccam as $_acte) {
          $_acte->getTarif();
        }
      }
    }
  }
}

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

//Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($devis);
// Liste des dents CCAM
$liste_dents = reset(CDentCCAM::loadList());

$user = CMediusers::get();
$user->isPraticien();

$smarty = new CSmartyDP();
$smarty->assign('devis'         , $devis);
$smarty->assign("acte_ngap"     , $acte_ngap);
$smarty->assign("liste_dents"   , $liste_dents);
$smarty->assign("listAnesths"   , $listAnesths);
$smarty->assign("listChirs"     , $listChirs);
$smarty->assign('user'          , $user);

if ($action == 'open') {
  $smarty->display('inc_edit_devis_container.tpl');
}
else {
  $smarty->display('inc_edit_devis.tpl');
}
