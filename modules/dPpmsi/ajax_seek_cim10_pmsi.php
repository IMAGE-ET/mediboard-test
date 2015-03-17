<?php 

/**
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$object_class = CValue::get('object_class');
$keywords     = CValue::post('keywords_code_pmsi');
$limit        = CValue::get('limit', 30);

/** @var CMbObject $object */
$object = new $object_class;
$ds = $object->_spec->ds;
if ($keywords == "") {
  $keywords = "%";
}
$codes    = $object->getAutocompleteList($keywords, null, $limit, null);
// Création du template
$smarty = new CSmartyDP();

$smarty->assign('codes'   , $codes);

$smarty->display('nomenclature_cim/inc_autocomplete_cim10.tpl');