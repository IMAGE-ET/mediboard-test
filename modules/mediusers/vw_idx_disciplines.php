<?php

/**
 * View disciplines
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */


CCanDo::checkRead();

$discipline_id = CValue::getOrSession("discipline_id");

$g = CGroups::loadCurrent();

// CHargement d'une discipline
$specialite = new CDiscipline();
$specialite->load($discipline_id);
$specialite->loadGroupRefsBack();

//Liste de toutes les disciplines
$discipline = new CDiscipline();
/** @var CDiscipline[] $listDiscipline */
$listDiscipline = $discipline->loadList();

foreach ($listDiscipline as $discipline) {
  $discipline->loadGroupRefsBack();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("group"         , $g             );
$smarty->assign("specialite"    , $specialite    );
$smarty->assign("listDiscipline", $listDiscipline);

$smarty->display("vw_idx_disciplines.tpl");

