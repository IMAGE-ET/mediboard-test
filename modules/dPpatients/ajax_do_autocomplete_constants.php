<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CCanDo::check();

$needle = CValue::request('_search_constants', 0);

$list_constantes = CConstantesMedicales::$list_constantes;

$results = array();
if ($needle) {
  foreach ($list_constantes as $_constant => $params) {
    if (strpos($_constant, 'cumul') !== false) {
      continue;
    }

    $search_elements   = array();
    $search_elements[] = CMbString::removeDiacritics(strtolower($_constant));
    $search_elements[] = CMbString::removeDiacritics(strtolower(CAppUI::tr("CConstantesMedicales-$_constant")));
    $search_elements[] = CMbString::removeDiacritics(strtolower(CAppUI::tr("CConstantesMedicales-$_constant-court")));
    $search_elements[] = CMbString::removeDiacritics(strtolower(CAppUI::tr("CConstantesMedicales-$_constant-desc")));
    if (strpos(implode('|', $search_elements), $needle) !== false) {
      $results[] = $_constant;
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign('results', $results);
$smarty->display('inc_autocomplete_constants.tpl');