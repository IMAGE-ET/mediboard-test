<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $dPconfig;

$can->needsAdmin();

// Check params
if (null == $dsn = CValue::get("dsn")) {
  CAppUI::stepAjax("Aucun DSN spécifié", UI_MSG_ERROR);
}

if (null == @$dPconfig["db"][$dsn]) {
  CAppUI::stepAjax("Configuration pour le DSN '$dsn' inexistante", UI_MSG_ERROR);
}

$dsConfig =& $dPconfig["db"][$dsn];
if ("mysql" != $dbtype = $dsConfig["dbtype"]) {
  CAppUI::stepAjax("Seules les DSN MySQL peuvent être créées par un accès administrateur", UI_MSG_ERROR);
}

// Substitute admin access
$user = $dsConfig["dbuser"];
$pass = $dsConfig["dbpass"];
$name = $dsConfig["dbname"];

$dsConfig["dbuser"] = CValue::get("master_user");
$dsConfig["dbpass"] = CValue::get("master_pass");
$dsConfig["dbname"] = "";

if (null == $ds = CSQLDataSource::get($dsn)) {
  CAppUI::stepAjax("Connexion en tant qu'administrateur échouée", UI_MSG_ERROR);
}

CAppUI::stepAjax("Connexion en tant qu'administrateur réussie");

foreach ($ds->queriesForDSN($user, $pass, $name) as $key => $query) {
  if (!$ds->exec($query)) {
    CAppUI::stepAjax("Requête '$key' échouée", UI_MSG_WARNING);
    continue;
  }
  
  CAppUI::stepAjax("Requête '$key' effectuée");
}

