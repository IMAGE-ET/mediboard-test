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

CCanDo::checkRead();

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Récupération des services
$order = "group_id, nom";
$where = array();
$where["group_id"]  = CSQLDataSource::prepareIn(array_keys($etablissements));
$where["cancelled"] = "= '0'";
$services = new CService();
$services = $services->loadList($where, $order);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("etablissements" , $etablissements);
$smarty->assign("services"       , $services);

$smarty->display("httpreq_get_services_offline.tpl");
