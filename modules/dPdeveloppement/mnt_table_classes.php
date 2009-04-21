<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Sébastien Fillonneau
* @author Fabien Ménager
*/

global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get('std');

// Nom de la classe à prendre en compte
$class_name = mbGetValueFromGetOrSession('class_name', null);

// Tableau des erreurs a prendre en compte
$t          = mbGetValueFromGetOrSession('types');

// Liste des noms des classes installées
$list_class_names = getInstalledClasses();

$list_selected_classes = array(); // Liste des noms de classes selectionnées
$list_classes = array(); // Liste des classes

// On regarde si la classe existe vraiment
if(array_search($class_name, $list_class_names) == false) {
  $class_name = null;
  $list_selected_classes =& $list_class_names;
}
else {
  $list_selected_classes[] = $class_name;
}
mbSetValueToSession('class_name', $class_name);

// Types d'erreurs qu'on peut prendre en compte
$error_types = array('type', 'params', 'unsigned', 'zerofill', 'null', 'default', 'index', 'extra');

$types = array();
foreach ($error_types as $type) {
  $types[$type] = !isset($t) || in_array($type, $t);
}

// Fonction d'extraction des proprietés d'un type SQL (du genre "UNSIGNED INT(11)")
function extract_props($type) {
  $props = array(
    'type' => null,
    'params' => null,
    'unsigned' => null,
    'zerofill' => null,
  );
  $props['type']    = $type;
  $props['unsigned'] = stristr($type, 'unsigned') != false;
  $props['zerofill'] = stristr($type, 'zerofill') != false;
  $props['type'] = trim(str_ireplace(array('unsigned', 'zerofill'), '', $props['type']));
  $props['params']  = null;
  if ($pos = strpos($props['type'], '(')) {
    $props['params'] = explode(',', substr($props['type'], $pos+1, strpos($props['type'], ')')-$pos-1));
    $props['params'] = array_map('trim', $props['params']);
    $props['type']   = substr($props['type'], 0, $pos);
  }
  $props['type'] = strtoupper($props['type']);
  return $props;
}

function array_duplicates($array, $field) {
  $ret = array();
  $count = count($array);
  for ($i = 0; $i < $count; $i++) {
    for ($j = 0; $j < $count; $j++) {
      if ($i != $j && $array[$i][$field] == $array[$j][$field]) {
        $ret[$i][] = $array[$i];
      }
    }
  }
  return $ret;
}

// Pour toutes les classes selectionnées
foreach ($list_selected_classes as $curr_class_name) {
  $object = new $curr_class_name;
  
  if ($object->_spec->table) {
  $list_classes[$curr_class_name] = array();
  $class = &$list_classes[$curr_class_name];
  
  // Clé dela table
  $class['table'] = $object->_spec->table;
  $class['key'] = $object->_spec->key;
  $class['db_key'] = null;
  $class['fields'] = array();
  
  // Extraction des champs de la classe
  $db_fields = $object->getDBFields();
  foreach ($db_fields as $k => $v) {
    $class['fields'][$k] = array();
    
    // object fields
    $class['fields'][$k]['object'] = array();
    $class['fields'][$k]['object']['spec'] = null;
    $class['fields'][$k]['object']['db_spec'] = null;
    
    $class['fields'][$k]['db'] = null;
    
    $object->getSpecs();
    $is_key = $k == $class['key'];
    // db fields
    if ($spec = @$object->_specs[$k]) {
      //mbTrace($spec);
      $class['fields'][$k]['object']['db_spec'] = extract_props($spec->getDBSpec());
      
      $specs_obj = $object->getSpecs();
      $db_spec = &$class['fields'][$k]['object']['db_spec'];
      $db_spec['index'] = (isset($spec->class) || 
                           $spec instanceof CDateTimeSpec || 
                           $spec instanceof CDateSpec || 
                           $spec instanceof CTimeSpec || 
                           $k == $class['key']);
      $db_spec['null'] = !(isset($spec->notNull)) && !$is_key;
      if ($is_key) {
        $db_spec['unsigned'] = true;
      }
      if ($k == $class['key']) {
        $db_spec['extra'] = 'auto_increment';
      }
      $db_spec['default'] = (isset($specs_obj['default']) ? 
                            $specs_obj['default'] : 
                            (isset($specs_obj['defaultOption']) ?
                              $specs_obj['defaultOption'] :
                              ''));
    }
    $class['fields'][$k]['db'] = null;
  }
  
  // Extraction des propriétés de la classe
  foreach($object->_props as $k => $v) {
    if (isset($k[0]) && $k[0] != '_') {
      $class['fields'][$k]['object']['spec'] = $v;
    }
  }
  
  // Extraction des champs de la BDD
  if($object->_spec->table && $ds->loadTable($object->_spec->table)) {
    $class['no_table'] = false;

	  $sql = "SHOW COLUMNS FROM `{$object->_spec->table}`";
	  $list_fields = $ds->loadList($sql);
	  
	  foreach($list_fields as $curr_field){
	    $class['fields'][$curr_field['Field']]['db'] = array();
	    if (!isset($class['fields'][$curr_field['Field']]['object'])) {
	      $class['fields'][$curr_field['Field']]['object'] = array();
	      $class['fields'][$curr_field['Field']]['object']['spec'] = null;
	    }
	  	$field =& $class['fields'][$curr_field['Field']]['db'];
	  	
	  	$props = extract_props($curr_field['Type']);
	  	
	    $field['type']     = $props['type'];
	    $field['params']   = $props['params'];
	    $field['unsigned'] = $props['unsigned'];
	    $field['zerofill'] = $props['zerofill'];
	    $field['null']     = ($curr_field['Null'] != 'NO');
	    $field['default']  = $curr_field['Default'];
	    $field['index']    = null;
	    $field['extra']    = $curr_field['Extra'];
	  }
	  
	  // Extraction des Index
	  $sql = "SHOW INDEX FROM `{$object->_spec->table}`";
    
	  $list_indexes = $ds->loadList($sql);
	  
    $duplicates = array_duplicates($list_indexes, 'Column_name');
    $class['duplicates'] = $duplicates;
	  
	  foreach($list_indexes as $curr_index) {
      $class['fields'][$curr_index['Column_name']]['db']['index'] = $curr_index['Key_name'];
      
	    if($curr_index['Key_name'] == 'PRIMARY'){
        $class['db_key'] = $curr_index['Column_name'];
        $class['fields'][$curr_index['Column_name']]['object']['db_spec']['extra'] = 'auto_increment';
      }
	  }
  } else {
    $class['no_table'] = true;
    $class['duplicates'] = array();
  }
  $class['suggestion'] = null;
  }
}

//mbTrace($class);

// Tableau indiquant si chaque champ contient une erreur
$list_errors = array();
foreach ($list_classes as $curr_class_name => &$curr_class) {
  $list_errors[$curr_class_name] = array();
  
  if ($class_name && ($class_name != $curr_class_name)) {
    $list_errors[$curr_class_name] = null;
    return;
  }
  
  $show = false;
  foreach ($curr_class['fields'] as $curr_field_name => &$curr_field) {
    $list_errors[$curr_class_name][$curr_field_name] = false;
    
    if (!isset($curr_field['db'])) {
      $curr_field['db'] = array();
      $curr_field['db']['no_column'] = true;
    }
    
    if (!isset($curr_field['object'])) {
      $curr_field['object'] = array();
    }
    
    if (!isset($curr_field['object']['db_spec'])) {
      $curr_field['object']['db_spec'] = array();
    }

    foreach ($error_types as $err) {
      if (!isset($curr_field['db'][$err])) {
        $curr_field['db'][$err] = null;
      }
      
      if (!isset($curr_field['object']['db_spec'][$err])) {
        $curr_field['object']['db_spec'][$err] = null;
      }
      
      if ($types[$err] && $curr_field['db'][$err] != $curr_field['object']['db_spec'][$err]) {
        $list_errors[$curr_class_name][$curr_field_name] = true;
        $show = true;
      }
    }
  }
  if (!$show) {
    $list_errors[$curr_class_name] = null;
  }
}

// Construit la requete correspondant à la spec d'une classe 
// (avec prise en compte des erreurs qu'on souhaite)
function get_query_for_class($class, $errors = array()) {
  $change = array();
  $add = array();
  $add_index = array();
  $drop_index = array();
  $ret = '';
  
  if (!isset($class['fields']) || count($class['fields']) == 0) return;
  
  // On compte les champs de BDD de la classe, si c'est null, on crée la classe 
  $add_table = $class['no_table'];
  
  // Gestion des ALTER TABLE
  foreach ($class['fields'] as $name => $field) {
    $spec_obj = $field['object']['db_spec'];
    $spec_db = $field['db'];
    
    $add_field = isset($spec_db['no_column']);
    
    // creation des lignes de specification des champs
    if ($add_field || $add_table ||
        $errors['type']     && ($spec_obj['type']     != $spec_db['type']) ||
        $errors['params']   && ($spec_obj['params']   != $spec_db['params']) ||
        $errors['unsigned'] && ($spec_obj['unsigned'] != $spec_db['unsigned']) ||
        $errors['zerofill'] && ($spec_obj['zerofill'] != $spec_db['zerofill']) ||
        $errors['extra']    && ($spec_obj['extra']    != $spec_db['extra']) ||
        $errors['null']     && ($spec_obj['null']     != $spec_db['null'])) {
          
      if ($add_field && !$add_table) {
        $change[$name] = "ADD `$name` ";
      }
      else if ($add_table) {
        $change[$name] = "`$name` ";
      }
      else {
        $change[$name] = "CHANGE `$name` `$name` ";
      }
      $change[$name] .= strtoupper($spec_obj['type']);
      
      if (count($spec_obj['params']) > 0) {
        $change[$name] .= ' (' . implode(',', $spec_obj['params']) . ')';
      }
      $change[$name] .=
         ($spec_obj['unsigned']?' UNSIGNED':'').
         ($spec_obj['zerofill']?' ZEROFILL':'').
         ($spec_obj['null']?'':' NOT NULL').
         ($spec_obj['extra']?" {$spec_obj['extra']}":'').
         (($name == $class['key'])?' PRIMARY KEY':'');
    }
    
    // creation des lignes d'ajout suppression des index
    if ($errors['index'] && $spec_obj['index'] && !$spec_db['index'] && $class['key'] != $name) {
      $add_index[$name] = "ADD INDEX (`$name`)";
    }
    
    if ($errors['index'] && !$spec_obj['index'] && $spec_db['index']) {
      $drop_index[$spec_db['index']] = "# DROP INDEX (`{$spec_db['index']}`)";
    }
  }
  
  $glue = ",\n\t";
  
  // creation / modification de la table
  if (count($change) > 0) {
    if ($add_table) {
      $ret = "CREATE TABLE `{$class['table']}` (\n\t" . implode($glue, $change) . "\n) TYPE=MYISAM;";
    }
    else {
      $ret = "ALTER TABLE `{$class['table']}` \n\t" . implode($glue, $change) . "\n;";
    }
  }
  
  // ajout / suppression des index
  if (count($add_index) > 0 || count($drop_index) > 0) {
      $q = array();
      if (count($add_index) > 0) {
        $q[] = implode($glue, $add_index);
      }
      if (count($drop_index) > 0) {
        $q[] = implode($glue, $drop_index);
      }
      $ret .= "\nALTER TABLE `{$class['table']}` \n\t" . implode($glue, $q) . "\n;";
  }
  
  if (count($class['duplicates']) > 0) {
    $ret .= "\n# Il y a probablement des index en double sur cette table";
  }
  
  return $ret;
}

// Enregistre les suggestion pour chaque classe
foreach ($list_classes as $curr_class_name => &$curr_class) {
  $curr_class['suggestion'] = get_query_for_class($curr_class, $curr_class_name, $types);
}



// Création du template
$smarty = new CSmartyDP();

$smarty->assign('list_classes',      $list_classes);
$smarty->assign('types',             $types);
$smarty->assign('list_errors',       $list_errors);
$smarty->assign('class_name',        $class_name);
$smarty->assign('list_class_names',  $list_class_names);

$smarty->display('mnt_table_classes.tpl');
?>