<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$prescription_id = CValue::get("prescription_id");

// Chargement des administrations
$administrations = CAdministration::getPerop($prescription_id, true);

foreach ($administrations as $_adm) {
  $_adm->loadTargetObject();
  
  if (
      $_adm->_ref_object instanceof CPrescriptionLineMedicament ||
      $_adm->_ref_object instanceof CPrescriptionLineMixItem
  ) {
    $_produit = $_adm->_ref_object->_ref_produit;
    $_produit->loadRapportUnitePriseByCIS();
    $_produit->updateRatioMassique();
    
    if ($_produit->_ratio_mg) {
      $_adm->_quantite_mg = $_adm->quantite / $_produit->_ratio_mg;
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("administrations", $administrations);
$smarty->display("inc_vw_perop_administrations.tpl");
