<?php 

/**
 * $Id$
 *  
 * @category Etablissement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

// Récupération de l'entité juridique sélectionée
$legal_entity = new CLegalEntity();
$legal_entity->load(CValue::getOrSession("legal_entity_id"));
$legal_entity->loadRefUser();

$legal_status = array();
if (CSQLDataSource::get('sae', true)) {
  $legal_status = new CLegalStatus();
  $legal_status = $legal_status->loadList();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("legal_entity" , $legal_entity);
$smarty->assign("legal_status" , $legal_status);

$smarty->display("inc_vw_legal_entity.tpl");