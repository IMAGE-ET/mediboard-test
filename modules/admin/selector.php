<?php /* PUBLIC $Id$ */

function selPermWhere( $table, $idfld ) {
	global $AppUI;

	// get any element denied from viewing
	$sql = "SELECT $idfld"
		."\nFROM $table, permissions"
		."\nWHERE permission_user = $AppUI->user_id"
		."\n	AND permission_grant_on = '$table'"
		."\n	AND permission_item = $idfld"
		."\n	AND permission_value = 0";

	$deny = db_loadColumn( $sql );
	echo db_error();

	return "permission_user = $AppUI->user_id"
		."\nAND permission_value <> 0"
		."\nAND ("
		."\n	(permission_grant_on = 'all')"
		."\n	OR (permission_grant_on = '$table' and permission_item = -1)"
		."\n	OR (permission_grant_on = '$table' and permission_item = $idfld)"
		."\n	)"
		. (count($deny) > 0 ? "\nAND $idfld NOT IN (" . implode( ',', $deny ) . ')' : '');
}

$debug = false;
$callback = dPgetParam( $_GET, 'callback', 0 );
$table = dPgetParam( $_GET, 'table', 0 );

$ok = $callback & $table;

$title = "Generic Selector";
$select = '';
$from = $table;
$where = '';
$order = '';

switch ($table) {
case 'users':
  $title = 'User';
  $select = "user_id,CONCAT_WS(' ',user_first_name,user_last_name)";
  $order = 'user_first_name';
  break;
case 'functions_mediboard':
  $title = "une fonction";
  $select = "function_id, text";
  $order = "text";
  break;
default:
	$ok = false;
	break;
}

if (!$ok) {
  echo "Incorrect parameters passed\n";
  if ($debug) {
    echo "<br />callback = $callback \n";
    echo "<br />table = $table \n";
    echo "<br />ok = $ok \n";
  }
} else {
  $sql = "SELECT $select FROM $table";
  $sql .= $where ? " WHERE $where" : '';
  $sql .= $order ? " ORDER BY $order" : '';
  //echo "<pre>$sql</pre>";

  $list = db_loadHashList($sql);
  echo db_error();
}

// Template creation
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('callback', $callback);
$smarty->assign('title', $title);
$smarty->assign('list', $list);

$smarty->display('selector.tpl');
?>
