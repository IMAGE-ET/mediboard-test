<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$echange_xml_id         = CValue::get("echange_xml_id");
$echange_xml_classname  = CValue::get("echange_xml_classname");

// Chargement de l'objet
$echange_xml = new $echange_xml_classname;
$echange_xml->load($echange_xml_id);
$echange_xml->loadRefNotifications();
$echange_xml->getObservations();
$echange_xml->loadRefsDestinataireInterop();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("object", $echange_xml);
$smarty->display("inc_echange_xml.tpl");

?>