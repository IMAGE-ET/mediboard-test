<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$acte_dentaire_id = CValue::post("acte_dentaire_id");
$rank = CValue::post("rank");

$acte_dentaire = new CActeDentaire;
$acte_dentaire->load($acte_dentaire_id);

$devenir_dentaire = new CDevenirDentaire;
$devenir_dentaire->load($acte_dentaire->devenir_dentaire_id);

$actes_dentaires = $devenir_dentaire->loadRefsActesDentaires();

foreach ($actes_dentaires as &$_acte_dentaire) {
  if ($_acte_dentaire->_id == $acte_dentaire_id) {
    continue;
  }
  if ($_acte_dentaire->rank > $acte_dentaire->rank) {
    $_acte_dentaire->rank --;
  }
}

foreach ($actes_dentaires as &$_acte_dentaire) {
  if ($_acte_dentaire->_id == $acte_dentaire_id) {
    continue;
  }
  if ($_acte_dentaire->rank >= $rank) {
    $_acte_dentaire->rank ++;
  }
}

unset($acte_dentaire);

$actes_dentaires[$acte_dentaire_id]->rank = $rank;

foreach ($actes_dentaires as &$_acte_dentaire) {
  if ($msg = $_acte_dentaire->store()) {
    CAppUI::setMsg($msg);
  }
}

CApp::rip();
