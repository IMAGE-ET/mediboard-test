<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien Ménager
 */

global $AppUI, $can, $m;

$can->needsRead();

$track = array();
$tracked_classes = array(
  'CProductDelivery'
);
$orderby = 'date DESC';
$where['code'] = 'IS NOT NULL';

$product = new CProduct();
$product->loadList();

foreach ($tracked_classes as $class) {
	if (class_exists($class)) {
		$type = new $class;
		$type = $type->loadList($where, $orderby);

		foreach ($type as $tracked_item) {
			$tracked_item->updateFormFields();
			$track[$tracked_item->code][$class][] = $tracked_item;
			
		  foreach ($tracked_classes as $c) {
        if (!isset($track[$tracked_item->code][$c])) {
          $track[$tracked_item->code][$c] = array();
        }
      }
		}
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('track', $track);
$smarty->assign('tracked_classes', $tracked_classes);

$smarty->display('vw_traceability.tpl');

?>