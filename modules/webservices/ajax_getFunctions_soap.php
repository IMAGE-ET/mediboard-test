<?php
/**
 * Get functions
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

require_once "ajax_connexion_soap.php";

CAppUI::stepAjax("Liste des fonctions SOAP publiées");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("exchange_source", $exchange_source);
$smarty->assign("functions"      , $soap_client->getFunctions());
$smarty->assign("types"          , $soap_client->getTypes());
$smarty->assign("form_name"      , CValue::get("form_name"));

$smarty->display("inc_soap_functions.tpl");