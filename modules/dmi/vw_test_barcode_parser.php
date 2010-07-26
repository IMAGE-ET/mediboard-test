<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$tests = array(
  '+$$31303313414899 .' => array (
    'per' => '2013-03-31',
    'lot' => '3414899',
  ),
  
  '+$$05151002113AL/' => array (
    'per' => '2015-05-31',
    'lot' => '1002113A',
  ),
  
  '+$$03151005377M' => array (
    'per' => '2015-03-31',
    'lot' => '100537',
  ),
  
  '+$$03150911112AJ0' => array (
    'per' => '2015-03-31',
    'lot' => '0911112A',
  ),
  
  '+$$01150910199AD6' => array (
    'per' => '2015-01-31',
    'lot' => '0910199A',
  ),
  
  '+M423104003921/$$081309091602Y' => array (
    'ref' => '10400392',
    'per' => '2013-08-31',
    'lot' => '09091602',
  ),
  
  '+H7036307002101/1830461324862J09C' => array (
    'ref' => '630700210',
    'lot' => '61324862',
  ),
  
  '+H920246020502' => array (
    'ref' => '2460205',
  ),
  
  '+M412RM51100004B1D' => array (
    'ref' => 'RM51100004B',
  ),
  
  '+M412RM45320011C1J' => array (
    'ref' => 'RM45320011C',
  ),
  
  '+M412RM45320004C1L' => array (
    'ref' => 'RM45320004C',
  ),
  
  '+$11393812M' => array (
    'lot' => '1139381',
  ),
  
  '09091602/00736' => array (
    'lot' => '09091602',
  ),
  
  'PAR-1934BF-2' => array (
    'ref' => 'AR-1934BF-2',
  ),
  
  'T314998' => array (
    'lot' => '314998',
  ),
  
  '2808123005365310060911306301' => array (
    'ref' => '28081230',
    'sn' => '053653',
    'per' => '2013-06-30',
    'lot' => '053653',
  ),
  
  'MB01234567' => array (
    'id' => 1234567,
  ),
  
  '3257001' => array (
    'cip' => '3257001',
  ),
  
  '4325700100018000' => array (
    'remb' => '4',
    'cip' => '3257001',
    'price' => '000180',
    'key' => '00',
  ),
  
  '0103596010597243171407001050303562' => array (
    'scc' => '03596010597243',
    'per' => '2014-07-31',
    'lot' => '50303562',
    'scc_manuf' => '96010',
    'scc_part' => '59724',
    'scc_prod' => '9601059724',
  ),
  
  '2014-07' => array (
    'per' => '2014-07-31',
  ),
  
  '+H732722015370B' => array (
    'ref' => '72201537',
  ),
  
  '+$L50303562B7' => array (
    'lot' => '50303562',
  ),
  
  '+H2072120351 ' => array (
    'ref' => '212035',
  ),
  
  '+$$31303313414899 .' => array (
    'per' => '2013-03-31',
    'lot' => '3414899',
  ),
  
  '0105019279116935' => array (
    'scc' => '05019279116935',
    'scc_manuf' => '19279',
    'scc_part' => '11693',
    'scc_prod' => '1927911693'
  ),
  
  '101222008@17160800' => array (
    'lot' => '1222008',
    'per' => '2016-08-31',
  ),
  
  '650-0837' => array(),
  
  '1222008' => array(),
  
  '0100380470209838171307281008GBK212' => array (
    'scc' => '00380470209838',
    'per' => '2013-07-31',
    'lot' => '08GBK212',
    'scc_manuf' => '80470',
    'scc_part' => '20983',
    'scc_prod' => '8047020983',
  ),
  
  '0100380470188430172013081008HAK221' => array (
    'scc' => '00380470188430',
    'per' => '2013-08-31',
    'lot' => '08HAK221',
    'scc_manuf' => '80470',
    'scc_part' => '18843',
    'scc_prod' => '8047018843',
  ),
  
  '0103700401678927' => array (
    'scc' => '03700401678927',
    'scc_manuf' => '00401',
    'scc_part' => '67892',
    'scc_prod' => '0040167892',
  ),
  
  '1715032710209-025392/Q216' => array (
    'per' => '2015-03-31',
    'lot' => '209-025392/Q216',
  ),
  
  '0103700401604803' => array (
    'scc' => '03700401604803',
    'scc_manuf' => '00401',
    'scc_part' => '60480',
    'scc_prod' => '0040160480',
  ),
  
  '1715043010210-026414/Q121' => array (
    'per' => '2015-04-30',
    'lot' => '210-026414/Q121',
  ),
  
  'CABSAIRC7 100109' => array (),
  
  '0103700401601819' => array (
    'scc' => '03700401601819',
    'scc_manuf' => '00401',
    'scc_part' => '60181',
    'scc_prod' => '0040160181',
  ),
  
  '1715043010210-026633/Q122' => array (
    'per' => '2015-04-30',
    'lot' => '210-026633/Q122',
  ),
  
  '+H12400112114001/1309010CA18101C105' => array (
    'ref' => '0011211400',
    'lot' => '10CA18101',
  ),
  
  '032015A00798' => array (
    'per' => '2015-03-31',
    'lot' => 'A00798',
  ),
  
  '0100380655710005171303001010E05C' => array (
    'scc' => '00380655710005',
    'per' => '2013-03-31',
    'lot' => '10E05C',
    'scc_manuf' => '80655',
    'scc_part' => '71000',
    'scc_prod' => '8065571000',
  ),
);

$results = array();

foreach($tests as $barcode => $good) {
  $parsed = CBarcodeParser::parse($barcode);
  $comp = $parsed['comp'];
  unset($comp['raw']);
  CMbArray::removeValue("", $comp);
  
  $results[$barcode] = array( 
    'good'  => $good,
    'parsed' => $comp,
    'ok' => $comp == $good,
  );
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("tests", $tests);
$smarty->assign("results", $results);
$smarty->display("vw_test_barcode_parser.tpl");
