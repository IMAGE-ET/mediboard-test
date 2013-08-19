<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$secteur_id = CValue::get("secteur_id");

$secteur = new CSecteur;
$secteur->load($secteur_id);
$secteur->loadRefsServices();

$smarty = new CSmartyDP;

$smarty->assign("secteur", $secteur);

$smarty->display("inc_services_secteur.tpl");
