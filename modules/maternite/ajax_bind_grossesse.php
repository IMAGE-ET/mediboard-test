<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$patient_id = CValue::get("patient_id");

$grossesse = new CGrossesse;
$grossesse->parturiente_id = $patient_id;

$last_grossesse = reset($grossesse->loadMatchingList("terme_prevu desc"));

// Le terme prévu de la grossesse doit être dans le futur.
if ($last_grossesse && $last_grossesse->terme_prevu > mbDate()) {
  $grossesse = $last_grossesse;
}

$smarty = new CSmartyDP;

$smarty->assign("grossesse", $grossesse);

$smarty->display("inc_bind_grossesse.tpl");
?>