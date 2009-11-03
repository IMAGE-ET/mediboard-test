<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPqualite
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsAdmin();

$months_count    = CValue::getOrSession("months_count", 12);
$months_relative = CValue::getOrSession("months_relative", 0);
$filters         = CValue::getOrSession("filters", array());
$evts            = CValue::get("evenements");
$comparison      = CValue::getOrSession("comparison", array("elem_concerne", "_criticite", "evenements"));

$graphs = array();

$dates = array();
$ticks = array();
$options = array();

$list_evts = explode('|', $evts);

$fiche = new CFicheEi();
$enums = array();
foreach($fiche->_specs as $field => $spec) {
  if ($spec instanceof CEnumSpec || $spec instanceof CBoolSpec) {
  	$enums[$field] = $spec->_locales;
  }
}

for ($i = $months_count - 1; $i >= 0; --$i) {
	$mr = $months_relative+$i;
	$sample_end = mbTransformTime("-$mr MONTHS", mbDate(), "%Y-%m-31 23:59:59");
	$sample_start = mbTransformTime("-$mr MONTHS", mbDate(), "%Y-%m-01 00:00:00");
	$dates[$sample_start] = array(
	  'start' => $sample_start,
	  'end' => $sample_end,
	);
}

$types = new CEiCategorie;
$types = $types->loadList();
$enums['evenements'] = array();
foreach($types as $key => $type) {
	$enums['evenements'][$key] = $type->nom;
}
$enums['_criticite'] = array();
$range = range(1, 3);
foreach($range as $n) {
  $enums['_criticite'][$n] = $n;
}



foreach ($enums as $key => &$enum) {
  if ((isset($fiche->_specs[$key]) && !$fiche->_specs[$key]->notNull) || $key == 'evenements') {
    $enum['unknown'] = 'Inconnu';
  }
  if (!isset($filters[$key])) {
    $filters[$key] = null;
  }
}

$ljoin = array();
$ljoin['users_mediboard'] = 'users_mediboard.user_id = fiches_ei.user_id';
$ljoin['functions_mediboard'] = 'functions_mediboard.function_id = users_mediboard.function_id';

foreach ($comparison as $comp) {
	if (isset($enums[$comp]) && $enums[$comp]) {
		$series = array();
		$series_data = array();

		foreach ($enums[$comp] as $li => $tr) {
			if (@$filters[$comp] == null || $filters[$comp] == $li){
				$series[] = array(
				  'label' => utf8_encode($tr), 
				  'data' => array()
			  );
			  $series_data[$li] = &$series[count($series)-1]['data'];
			}
		}
		array_push($series, array(
		  'data' => array(), 
		  'label' => 'Total', 
		  'hide' => true
		));
		$series_data['total'] = &$series[count($series)-1]['data'];
		
		$where = array();
		$where['functions_mediboard.group_id'] = "='$g'";
		$where['fiches_ei.annulee'] = "!= '1'";
		$where['fiches_ei.date_validation'] = "IS NOT NULL";
		foreach ($filters as $key => $val) {
		  if ($val != null) {
		    $where[$key] = ($val == 'unknown' ? 'IS NULL' : " = '$val'");
		  }
		}
		
		foreach ($series_data as $id => &$data) {
			if ($id != 'total') {
				$i = 0;
				
				foreach ($dates as $month => $date) {
					$where['date_fiche'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
					$count = 0;
					
					switch ($comp) {
						case 'evenements':
						  // Filtrage sur les types d'evenements
	            /*if (!$evts || count($list_evts) <= 0) {
	              $count = $fiche->countList($where);
	            }
	            else {*/
	              $list = $fiche->loadList($where, null, null, null, $ljoin);
	
	              if ($id != 'unknown') {
	                $where['evenements'] = 'IS NOT NULL';
	                $types[$id]->loadRefsBack();
	                $list_types = $types[$id]->_ref_items;
	                
	                foreach ($list as &$f) {
	                  $fiche_evts = explode('|', $f->evenements);
	                  
	                  foreach ($fiche_evts as $e) {
	                    if (array_key_exists($e, $list_types)) {
	                      $count++;
	                    }
	                  }
	                }
	              }
	              else {
	                $where['evenements'] = 'IS NULL';
	              }
	            //}
						break;
						
						case '_criticite':
				      $list = $fiche->loadList($where, null, null, null, $ljoin);
              foreach ($list as $key => $fiche) {
              	$fiche->loadCriticite();
              	if ($id == $fiche->_criticite || ($id == 'unknown' && !$fiche->_criticite)) $count++;
              }
					  break;
					  
						default: 
						  $where[$comp] = ($id == 'unknown' ? 'IS NULL' : "= '$id'");
	            
	            // Filtrage sur les types d'evenements
	            if (!$evts || count($list_evts) <= 0) {
	              $count = $fiche->countList($where, null, null, null, $ljoin);
	            }
	            else {
	              $where['evenements'] = 'IS NOT NULL';
	              $list = $fiche->loadList($where, null, null, null, $ljoin);
	      
	              foreach ($list as &$f) {
	                $fiche_evts = explode('|', $f->evenements);
	                
	                foreach ($fiche_evts as $e) {
	                  if (in_array($e, $list_evts)) {
	                    $count++;
	                  }
	                }
	              }
	            }
					}
					
					if (!isset($series_data['total'][$i])) { 
					  $series_data['total'][$i] = array($i, 0);
					}
					$series_data['total'][$i][1] += $count;
					
					$data[] = array($i, $count);
					$ticks[$i] = array($i, mbTransformTime(null, $month, "%m/%y"));
					++$i;
				}
			}
		}
		$graphs[$comp] = $series;
	}
}

$list_categories = new CEiCategorie;
$list_categories = $list_categories->loadList(null, "nom");

$count_checked = array(); 

foreach ($list_categories as $key => &$cat){
  if(!isset($first_cat)){
    $first_cat = $key;
  }
  $cat->loadRefsBack();
  $count_checked[$key] = 0;
  foreach($cat->_ref_items as $keyItem => &$item) {
    if (in_array($keyItem, $list_evts)) {
    	$item->checked = true;
    	$count_checked[$key]++;
    }
    else {
    	$item->checked = false;
    }
  }
}

$options['xaxis'] = array('ticks' => $ticks, 'labelsAngle' => $months_count > 12 ? 45 : 0);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("fiche",           $fiche);
$smarty->assign("list_categories", $list_categories);
$smarty->assign("list_categories_json", array_keys($list_categories));
$smarty->assign("list_months",     range(0, 24));

$smarty->assign("months_count",    $months_count);
$smarty->assign("months_relative", $months_relative);
$smarty->assign("comparison",      $comparison);
$smarty->assign("filters",         $filters);
$smarty->assign("evenements",      $evts);
$smarty->assign("enums",           $enums);

$smarty->assign("count_checked",   $count_checked);
$smarty->assign("graphs",  $graphs);
$smarty->assign("options", $options);

$smarty->display("vw_stats.tpl");

?>