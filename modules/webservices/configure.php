<?php
/**
 * Configure
 *
 * @category Webservices
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// MB SOAP server
$mb_soap_server = CExchangeSource::get("mb_soap_server", "soap", true, null, false);
if (!$mb_soap_server->_id) {
  $mb_soap_server->host = CApp::getBaseUrl()."/index.php?login=1&username=%u&password=%p&m=webservices&a=soap_server&wsdl";
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mb_soap_server", $mb_soap_server);
$smarty->display("configure.tpl");