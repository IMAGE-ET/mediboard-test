<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$object_guid = CValue::getOrSession("object_guid");

/** @var CCodable $objet */
$objet = CMbObject::loadFromGuid($object_guid);
$objet->loadRefsActes();
foreach ($objet->_ref_actes_ccam as &$acte) {
  $acte->loadRefsFwd();
  $acte->guessAssociation();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("objet", $objet);

$smarty->display("inc_confirm_actes_ccam.tpl");
