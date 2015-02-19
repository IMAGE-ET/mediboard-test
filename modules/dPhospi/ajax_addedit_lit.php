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

$group          = CGroups::loadCurrent();
$chambre_id     = CValue::getOrSession("chambre_id");
$lit_id         = CValue::getOrSession("lit_id");

// Récupération de la chambre à ajouter/editer
$chambre = new CChambre();
$chambre->load($chambre_id);
$chambre->loadRefsNotes();
$chambre->loadRefService();
/** @var CChambre[] $chambres */
$chambres = $chambre->loadRefsLits(true);
foreach ($chambres as $_chambre) {
  $_chambre->loadRefsNotes();
}

if (!$chambre->_id) {
  CValue::setSession("lit_id", 0);
}

// Chargement du lit à ajouter/editer
$lit = new CLit();
$lit->load($lit_id);
$lit->loadRefChambre();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("_lit"        , $lit);
$smarty->assign("tag_lit"    , CLit::getTagLit($group->_id));
$smarty->assign("chambre"    , $chambre);
$smarty->assign("tag_chambre", CChambre::getTagChambre($group->_id));
$smarty->display("inc_vw_lit_line.tpl");