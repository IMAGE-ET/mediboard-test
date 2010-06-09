<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Check params
if (null == $dsn = CValue::get("dsn")) {
  CAppUI::stepAjax("Aucun DSN spécifié", UI_MSG_ERROR);
}

if (!@CSQLDataSource::get($dsn)) {
  CAppUI::stepAjax("Connexion vers la DSN '$dsn' échouée", UI_MSG_ERROR);
}

CAppUI::stepAjax("Connexion vers la DSN '$dsn' réussie");
