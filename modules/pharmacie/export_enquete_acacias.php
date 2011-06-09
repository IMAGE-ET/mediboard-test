<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// http://www.acacias-consultants.fr/Drees/
// http://www.acacias-consultants.fr/Drees/Telechargement/cahier_des_charges_ES_2011(pdf).pdf

CCanDo::checkRead();

$year = CValue::get("year", mbTransformTime(null, null, "%Y"));
$separator = CValue::get("separator", ";");
$delimiter = CValue::get("delimiter", '"');
$fixed_width = CValue::get("fixed_width");

$date_min = "$year-01-01";
$date_max = "$year-12-31";

$cols = array(
  "V1" => "Code UCD",
  "V2" => "Libellé UCD",
  "V3" => "Prix moyen ponderé",
  "V4" => "Dernier prix d'achat",
  "V5" => "Quantités achetées en UCD",
  "V6" => "Quantités rétrocédées en UCD",
  "V7" => "Quantités délivrées au unités de soins",
);

/*
    [CODE_CIP] => 3004352
    [LIBELLE_ABREGE] => ANAFRANIL
    [DOSAGE] => 25 mg
    [CODE_FORME_GALENIQUE] => AA10
    [CODE_PRESENTATION] => 0
    [NB_PRESENTATION] => 1
    [CODE_UNITE_DE_PRESENTATION] => 8
    [NB_UNITE_DE_PRESENTATION] => 50
    [CODE_CONDITIONNEMENT] => 1
    [CODE_UNITE_DE_PRISE1] => 48
    [CODE_UNITE_DE_CONTENANCE1] => 5
    [NB_UP1] => 0.04
    [ARRONDI_UP1] => 100
    [JETER_LE_RESTE_UP1] => 0
    [CODE_UNITE_DE_PRISE2] => 1
    [CODE_UNITE_DE_CONTENANCE2] => 5
    [NB_UP2] => 1
    [ARRONDI_UP2] => 1
    [JETER_LE_RESTE_UP2] => 0
    [DOPANT] => 0
    [DOSAGEQTE1] => 25
    [DOSAGEUNITE1] => 8
    [DOSAGESEPARATEUR1] => 
    [DOSAGEQTE2] => 0
    [DOSAGEUNITE2] => 0
    [DOSAGESEPARATEUR2] => 
    [DOSAGEQTE3] => 0
    [DOSAGEUNITE3] => 0
    [CODE_UCD] => 9004614
    [CODECIS] => 69235342
    [DOSAGELIBELLELONG] => -1
    [CODE_MAJ] => C
 */

$ds_std = CSQLDataSource::get("std");
$ds_bcb = CBcbObject::getDataSource();

$out = fopen("php://output", "w");
header("Content-Type: application/csv");
header("Content-Disposition: attachment; filename=\"Enquete ACACIAS.csv\"");

function pad_num($value, $float = false, $length = 10) {
	return str_pad(($float ? number_format($value, 2, ",", "") : round($value)), $length, 0, STR_PAD_LEFT);
}

$product = new CProduct;
$req = new CRequest;
$req->addTable($product->_spec->table);
$req->addSelect("code");
$req->addWhere(array(
  "code IS NOT NULL",
  "LENGTH(code) = 7",
  "code REGEXP '[0-9]{7}'",
  "cancelled = '0'",
));
//$req->setLimit(100);
$res = $req->getRequest();
$list_cip = CMbArray::pluck($product->_spec->ds->loadList($res), "code");

foreach($list_cip as $cip) {
	$query = "SELECT * FROM `IDENT_PRODUITS`
            WHERE `IDENT_PRODUITS`.`CODE_CIP` = '$cip';";
  $data = $ds_bcb->loadHash($query);
	
	if (empty($data["CODE_UCD"])) continue;
	
	$where = array(
	  "code" => $ds_std->prepare("=%", $cip),
	);
	
	$product = new CProduct;
	$product->loadObject($where);
  
  $qa = $product->getSupply($date_min, $date_max);
  $qr = 0;
  $qd = max(0, $product->getConsumption($date_min, $date_max, null, false));
	
	$dpa = 0;
  $ref = reset($product->loadRefsReferences());
  if ($ref) {
    $dpa = $ref->price + ($ref->price * ($ref->tva/100));
  }
  
  $pmp = $qa ? $product->getWAP($date_min, $date_max) : $dpa;
	
  $line = array(
	  "V1" => $data["CODE_UCD"],
	  "V2" => str_pad(substr($data["LIBELLE_ABREGE"]." ".$data["DOSAGE"], 0, 30), 30, " ", STR_PAD_RIGHT),
	  "V3" => pad_num($pmp, true),
	  "V4" => pad_num($dpa, true),
	  "V5" => pad_num($qa),
	  "V6" => pad_num($qr),
	  "V7" => pad_num($qd),
	);
	
	//echo "<pre>";
	fputs($out, implode($separator, $line)."\n");
  //fputcsv($out, $line, $separator);
}
