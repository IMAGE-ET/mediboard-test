<?php

/**
 * Entête et pied de page à la volée
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$compte_rendu_id = CValue::get("compte_rendu_id");
$object_class    = CValue::get("object_class");
$type            = CValue::get("type");

$compte_rendu = new CCompteRendu;
$compte_rendu->load($compte_rendu_id);

if ($compte_rendu->user_id) {
  $owner = 'prat';
  $id = $compte_rendu->user_id;
}
else if ($compte_rendu->function_id) {
  $owner = 'func';
  $id = $compte_rendu->function_id;
}
else if ($compte_rendu->group_id) {
  $owner = 'etab';
  $id = $compte_rendu->group_id;
}
else {
  $owner = 'etab';
  $id = CGroups::loadCurrent()->_id;
}

$components = CCompteRendu::loadAllModelesFor($id, $owner, $object_class, $type);

$smarty = new CSmartyDP();

$smarty->assign("type"        , $type);
$smarty->assign("components"  , $components);

$smarty->display("inc_headers_footers.tpl");
