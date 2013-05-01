<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Recuperation de l'id de l'etablissement externe
$etab_id = CValue::getOrSession("etab_id");

// Récupération des etablissements externes
$etab_externe = new CEtabExterne();
if ($etab_id) {
  $etab_externe->load($etab_id);
  $etab_externe->loadRefsNotes($etab_id);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("etab_externe", $etab_externe);

$smarty->display("inc_etab_externe.tpl");
