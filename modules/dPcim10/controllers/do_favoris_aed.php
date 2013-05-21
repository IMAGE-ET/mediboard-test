<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

$do = new CDoObjectAddEdit("CFavoriCIM10");

// Amélioration des textes
$user = new CMediusers;
$user->load($_POST["favoris_user"]);
$for = " pour $user->_view";
$do->createMsg .= $for;
$do->modifyMsg .= $for;
$do->deleteMsg .= $for;

$do->redirect = null;
$do->doIt();

if (CAppUI::pref("new_search_cim10") == 1) {
  echo CAppUI::getMsg();
  CApp::rip();
}
