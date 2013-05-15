<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// Chargement su séjour s'il y en a un
$sejour = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$service_id = $sejour->service_sortie_id;
$service = new CService();
$service->load($service_id);

// Chargement des services
$order = "nom";
$services = $service->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("service", $service);
$smarty->assign("services", $services);

$smarty->display("inc_vw_services.tpl");
