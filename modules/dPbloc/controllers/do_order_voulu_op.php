<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$plageop_id = CValue::post("plageop_id");

$plageop = new CPlageOp();
$plageop->load($plageop_id);

$plageop->loadRefsOperations(false, "rank, rank_voulu, horaire_voulu", true);
$plageop->reorderOp("validate");

CAppUI::stepAjax("Placement effectué");
CApp::rip();
