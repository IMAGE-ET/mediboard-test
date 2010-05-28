<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$can->needsRead();

$debut    = CValue::get("debut", mbDate("-1 YEAR"));
$fin      = CValue::get("fin", mbDate());
$prat_id  = CValue::get("prat_id");
$salle_id = CValue::get("salle_id");
$bloc_id  = CValue::get("bloc_id");
$codeCCAM = CValue::get("codeCCAM");

$prat = new CMediusers;
$prat->load($prat_id);

$salle = new CSalle;
$salle->load($salle_id);

$where = array();
$where['stats'] = " = '1'";
if($salle_id) {
  $where['salle_id'] = " = '$salle_id'";
} elseif($bloc_id) {
  $where['bloc_id'] = "= '$bloc_id'";
}

$salles = $salle->loadList($where);
$list = array();

foreach($salles as $salle) {
  $query = "SELECT DISTINCT(operations.operation_id) AS op_id,
              DATE_FORMAT(plagesop.date, '%Y - %m') AS mois,
              DATE_FORMAT(plagesop.date, '%Y-%m-01') AS orderitem
            FROM operations
            INNER JOIN sallesbloc ON operations.salle_id = sallesbloc.salle_id
            LEFT JOIN plagesop ON plagesop.plageop_id = operations.plageop_id
            LEFT JOIN user_log ON user_log.object_id = operations.operation_id
              AND user_log.object_class = 'COperation'
            WHERE plagesop.date BETWEEN '$debut' AND '$fin'
              AND user_log.type = 'store'
              AND DATE(user_log.date) = plagesop.date
              AND user_log.fields LIKE '%annulee%'
              AND operations.annulee = '1'";

  if($prat_id)  $query .= "\nAND operations.chir_id = '$prat_id'";
  if($codeCCAM) $query .= "\nAND operations.codes_ccam LIKE '%$codeCCAM%'";

  $query .= "AND sallesbloc.salle_id = '$salle->_id'
             ORDER BY orderitem";

  $result = $prat->_spec->ds->loadlist($query);
  
  foreach($result as $res) {
    if (!isset($list[$res['mois']]))
      $list[$res['mois']] = array();
      
    if (!isset($list[$res['mois']][$salle->_view]))
      $list[$res['mois']][$salle->_view] = array();
    
    $operation = new COperation;
    $operation->load($res['op_id']);
    $operation->loadRefsFwd();
    
    $list[$res['mois']][$salle->_view][$operation->_id] = $operation;
  }
}

ksort($list);

// Set up the title for the graph
$title = "Interventions annulées le jour même";
if($prat_id)  $subtitle .= " - Dr $prat->_view";
if($salle_id) $subtitle .= " - $salle->nom";
if($codeCCAM) $subtitle .= " - CCAM : $codeCCAM";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("list", $list);
$smarty->assign("title", $title);

$smarty->display("vw_cancelled_operations.tpl");
