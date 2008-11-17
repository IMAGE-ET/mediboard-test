<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision$
 *  @author Fabien Ménager
 */

global $can;
$can->needsAdmin();

$months_count    = mbGetValueFromGetOrSession("months_count", 12);
$months_relative = mbGetValueFromGetOrSession("months_relative", 0);
$filters    = mbGetValueFromGetOrSession("filters", array());
$evts       = mbGetValueFromGet("evenements");
$comparison = mbGetValueFromGetOrSession("comparison", "elem_concerne");

$series = array();
$series_by_name = array();

$dates = array();
$ticks = array();
$options = array();

$list_evts = explode('|', $evts);

$fiche = new CFicheEi();
$enums = $fiche->getEnumsTrans();
for ($i = $months_count - 1; $i >= 0; --$i) {
	$mr = $months_relative+$i;
	$sample_end = mbTransformTime("-$mr MONTHS", mbDate(), "%Y-%m-31 23:59:59");
	$sample_start = mbTransformTime("-$mr MONTHS", mbDate(), "%Y-%m-01 00:00:00");
	$dates[$sample_start] = array(
	  'start' => $sample_start,
	  'end' => $sample_end,
	);
}

foreach ($enums as $key => &$val) {
  if (!$fiche->_specs[$key]->notNull) {
    $val['unknown'] = 'Inconnu';
  }
  if (!isset($filters[$key])) {
  	$filters[$key] = null;
  }
}

$where = array();
foreach ($filters as $key => $val) {
  if ($val != null) {
    $where[$key] = ($val == 'unknown' ? 'IS NULL' : " = '$val'");
  }
}

if (isset($enums[$comparison])) {
	foreach ($enums[$comparison] as $li => $tr) {
		$series[] = array(
		  'label' => utf8_encode($tr), 
		  'data' => array()
	  );
	  $series_data[$li] = &$series[count($series)-1]['data'];
	}
	
	foreach ($series_data as $id => &$data) {
		$i = 0;
		foreach ($dates as $month => $date) {
			$where[$comparison] = ($id == 'unknown' ? 'IS NULL' : "= '$id'");
			$where['date_fiche'] = "BETWEEN '{$date['start']}' AND '{$date['end']}'";
			$count = 0;
			if (!$evts || count($list_evts) <= 0) {
			  $count = $fiche->countList($where);
			}
			else {
				$where['evenements'] = 'IS NOT NULL';
				$list = $fiche->loadList($where);

				foreach ($list as &$f) {
					$fiche_evts = explode('|', $f->evenements);
					
					foreach ($fiche_evts as $e) {
					  if (in_array($e, $list_evts)) {
					  	$count++;
					  }
					}
				}
			}
			
			$data[] = array($i, $count);
			$ticks[$i] = array($i, mbTransformTime(null, $month, "%m/%y"));
			++$i;
		}
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
  foreach($cat->_ref_items as $keyItem => $valueItem) {
    if (in_array($keyItem, $list_evts)) {
    	$cat->_ref_items[$keyItem]->checked = true;
    	$count_checked[$key]++;
    }
    else {
    	$cat->_ref_items[$keyItem]->checked = false;
    }
  }
}

$options['xaxis'] = array('ticks' => $ticks);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("fiche",           $fiche);
$smarty->assign("list_categories", $list_categories);

$smarty->assign("months_count",    $months_count);
$smarty->assign("months_relative", $months_relative);
$smarty->assign("comparison",      $comparison);
$smarty->assign("filters",         $filters);
$smarty->assign("evenements",      $evts);
$smarty->assign("enums",           $enums);

$smarty->assign("count_checked",   $count_checked);
$smarty->assign("series",  $series);
$smarty->assign("options", $options);

$smarty->display("vw_stats.tpl");

?>