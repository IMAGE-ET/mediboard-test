<?php 

/**
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$service_id   = CValue::getOrSession("service_id");
$chambre_id   = CValue::getOrSession("chambre_id");
$lit_id       = CValue::getOrSession("lit_id");
$group        = CGroups::loadCurrent();

// Chargement de la chambre à ajouter/editer
$chambre = new CChambre();
$chambre->service_id = $service_id;
$chambre->load($chambre_id);
$chambre->loadRefsNotes();
$chambre->loadRefService();
foreach ($chambre->loadRefsLits(true) as $_lit) {
  $_lit->loadRefsNotes();
}

// Récupération des chambres/services
$where = array();
$where["group_id"] = "= '$group->_id'";
$order = "nom";

$service = new CService();
$services = $service->loadListWithPerms(PERM_READ, $where, $order);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("services"    , $services);
$smarty->assign("chambre"     , $chambre);
$smarty->assign("tag_chambre" , CChambre::getTagChambre($group->_id));
$smarty->assign("tag_lit"    , CLit::getTagLit($group->_id));
$smarty->display("inc_vw_chambre.tpl");