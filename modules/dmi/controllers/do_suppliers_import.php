<?php /* $Id: httpreq_do_sync_products.php 7207 2009-11-03 12:03:30Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: 7207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

// Recuperation du fichier
$file = CValue::read($_FILES, "datafile");

$first_line_label = "Raison sociale";
$delim = ",";

$map = array(
   0 => "name",
 //1 => Raison sociale du distributeur
   2 => "distributor_code",
 //3 => Nature,
   4 => "address",
   5 => "address",
   6 => "postal_code",
   7 => "city",
 //8 => Minimum de commande,
   9 => "carriage_paid",
  10 => "code",
  11 => "phone",
  12 => "fax",
  13 => "email",
  14 => "customer_code",
//15 => Identifiant interne,
//16 => Facturé par,
//17 => Reglé par,
//18 => Code interne,
);

if (strtolower(CMbPath::getExtension($file["name"]) != 'csv')) 
  CAppUI::setMsg("Le fichier doit être de type CSV", UI_MSG_ERROR);

$csv = fopen($file["tmp_name"], 'r');

// On avance jusqu'a trouver la premiere ligne
while (($data = fgetcsv($csv, null, $delim)) !== false && ($data[0] !== $first_line_label));

$n = 10;
while ((($data = fgetcsv($csv, null, $delim)) !== false)/* && $n--*/) {
  $data = array_map("trim", $data);
  
  $societe = new CSociete;
  $societe->name = $societe->_spec->ds->escape($data[0]);
  $societe->loadMatchingObject();
  
  $existed = $societe->_id;
  
  $prec_col = null;
  foreach($map as $from => $to) {
    // Concatenation
    if ($prec_col == $to) {
      $v = ($data[$from] ? "{$societe->$to}\n{$data[$from]}" : $societe->$to);
    }
    else {
      if (in_array($to, array("phone", "fax"))) {
        $data[$from] = preg_replace("/\\(\\d\\)/", "", $data[$from]);
        $data[$from] = preg_replace("/[\.-\\s\\(\\)\\/]/", "", $data[$from]);
        if (strlen($data[$from]) == 9) {
          $data[$from] = "0{$data[$from]}";
        }
      }
      $v = $data[$from];
    }
    $societe->$to = $v;
    
    $prec_col = $to;
  }
  
  $societe->repair();
  if ($msg = $societe->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::setMsg($existed ? "Société mise à jour" : "Société importée", UI_MSG_OK);
  }
}

fclose($csv);
