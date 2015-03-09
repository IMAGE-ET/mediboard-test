<?php 

/**
 * $Id$
 *  
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$secteur_id     = CValue::getOrSession("secteur_id");
$group = CGroups::loadCurrent();

$secteur = new CSecteur;
$secteur->group_id = $group->_id;
$secteur->load($secteur_id);
$secteur->loadRefsNotes();
$secteur->loadRefsServices();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("secteur", $secteur);
$smarty->display("inc_vw_secteur.tpl");

