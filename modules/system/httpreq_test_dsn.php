<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$can->needsAdmin();

// Check params
if (null == $dsn = mbGetValueFromGet("dsn")) {
  $AppUI->stepAjax("Aucun DSN spécifié", UI_MSG_ERROR);
}

if (!CSQLDataSource::get($dsn)) {
  $AppUI->stepAjax("Connexion vers la DSN '$dsn' échouée", UI_MSG_ERROR);
}

$AppUI->stepAjax("Connexion vers la DSN '$dsn' réussie");