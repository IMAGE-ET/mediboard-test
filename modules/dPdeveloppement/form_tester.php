<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Fabien Ménager
*/

global $can;
$can->needsRead();

$specs = array(
  'ref'       => 'CRefSpec',
  'str'       => 'CStrSpec',
  'numchar'   => 'CNumcharSpec',
  'num'       => 'CNumSpec',
  'bool'      => 'CBoolSpec',
  'enum'      => 'CEnumSpec',
  'date'      => 'CDateSpec',
  'time'      => 'CTimeSpec',
  'dateTime'  => 'CDateTimeSpec',
  'birthDate' => 'CBirthDateSpec',
  'float'     => 'CFloatSpec',
  'currency'  => 'CCurrencySpec',
  'pct'       => 'CPctSpec',
  'text'      => 'CTextSpec',
  'html'      => 'CHtmlSpec',
  'email'     => 'CEmailSpec',
  'code'      => 'CCodeSpec',
  'password'  => 'CPasswordSpec',
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign('specs', $specs);
$smarty->display('form_tester.tpl');

?>