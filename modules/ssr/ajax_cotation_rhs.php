<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Séjour concernés
$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
if (!$sejour->_id) {
  CAppUI::stepAjax("Séjour inexistant", UI_MSG_ERROR);
}

if ($sejour->type != "ssr") {
  CAppUI::stepAjax("Le séjour sélectionné n'est pas un séjour de type SSR (%s)", UI_MSG_ERROR, $sejour->type);
}

// Chargment du bilan
$bilan = $sejour->loadRefBilanSSR();

// Liste des RHSs du séjour
$_rhs = new CRHS();
$rhss = CRHS::getAllRHSsFor($sejour);
foreach ($rhss as $_rhs) {
  $_rhs->loadRefSejour();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour"        , $sejour);
$smarty->assign("bilan"         , $bilan);
$smarty->assign("rhss"          , $rhss);

$smarty->display("inc_cotation_rhs.tpl");

?>