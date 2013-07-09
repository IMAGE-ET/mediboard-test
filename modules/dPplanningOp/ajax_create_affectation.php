<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$sejour_id = CValue::get("sejour_id");
$lit_id    = CValue::get("lit_id");

$sejour = new CSejour;
$sejour->load($sejour_id);

$affectation = new CAffectation;
$affectation->sejour_id = $sejour_id;
$affectation->lit_id = $lit_id;
$affectation->entree = $sejour->entree;
$affectation->sortie = $sejour->sortie;

if ($msg = $affectation->store()) {
  return CAppUI::setMsg($msg, UI_MSG_ERROR);
}
