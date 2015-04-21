<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Nom de la classe à prendre en compte
$class = CValue::getOrSession("class");

// Tableau des erreurs a prendre en compte
$t          = CValue::getOrSession('types');

// Liste des noms des classes installées
$installed_classes = CApp::getInstalledClasses();

// Liste des noms de classes selectionnées
$selected_classes = array();

 // Liste des classes analysées
$list_classes = array();

// On regarde si la classe existe vraiment
if (array_search($class, $installed_classes) === false) {
  $class = null;
  $selected_classes =& $installed_classes;
}
else {
  $selected_classes[] = $class;
}
CValue::setSession('class', $class);

CView::enforceSlave();

// Types d'erreurs qu'on peut prendre en compte
$error_types = array('type', 'params', 'unsigned', 'zerofill', 'null', 'default', 'index', 'extra');

$types = array();
foreach ($error_types as $type) {
  $types[$type] = !isset($t) || in_array($type, $t);
}

/**
 * Build an array of duplicates values for key-value array collection.
 *
 * @param mixed[][] $array Key-value array collection (array of arrays)
 * @param string    $key   The key to inspect
 *
 * @return mixed[][]
 */
function array_duplicates($array, $key) {
  $ret = array();
  $count = count($array);
  for ($i = 0; $i < $count; $i++) {
    for ($j = 0; $j < $count; $j++) {
      if ($i != $j && $array[$i][$key] == $array[$j][$key]) {
        $ret[$i][] = $array[$i];
      }
    }
  }
  return $ret;
}

// Pour toutes les classes selectionnées
foreach ($selected_classes as $_class) {
  /** @var CStoredObject $object */
  $object = new $_class;

  if (!$object->_spec->table) {
    continue;
  }

  $ds = $object->_spec->ds;

  $list_classes[$_class] = array();
  $details = &$list_classes[$_class];

  // Clé dela table
  $details['table'] = $object->_spec->table;
  $details['key'] = $object->_spec->key;
  $details['db_key'] = null;
  $details['fields'] = array();

  // Extraction des champs de la classe
  $fields = $object->getPlainFields();
  foreach ($fields as $k => $v) {
    $details['fields'][$k] = array();

    // object fields
    $details['fields'][$k]['object'] = array(
      'spec' => null,
      'db_spec' => null
    );

    $details['fields'][$k]['db'] = null;

    $object->getSpecs();
    $is_key = $k == $details['key'];
    // db fields
    if ($spec = @$object->_specs[$k]) {
      //mbTrace($spec);
      $details['fields'][$k]['object']['db_spec'] = CMbFieldSpec::parseDBSpec($spec->getDBSpec());

      $db_spec = &$details['fields'][$k]['object']['db_spec'];
      $db_spec['index'] = (
        in_array(array($k), $object->_spec->uniques) ||
        isset($spec->class) || 
        $spec instanceof CDateTimeSpec || 
        $spec instanceof CDateSpec || 
        $k == $details['key'] ||
        $spec->autocomplete ||
        $spec->index
      );
      $db_spec['null'] = !(isset($spec->notNull)) && !$is_key;

      $default = null;
      if (isset($spec->default) || $spec->notNull) {
        if ($spec->default === "NULL") {
          $default = "NULL";
        }
        elseif ($spec->default !== null) {
          $default = "{$spec->default}";
        }
        elseif ($spec->notNull && ($spec instanceof CNumSpec || $spec instanceof CFloatSpec  || $spec instanceof CRefSpec) ) {
          $default = "0";
        }
      }

      $db_spec['default'] = $default;

      // Some keys from external tables are str
      if ($is_key && $spec instanceof CRefSpec) {
        $db_spec['unsigned'] = true;
      }

      if ($k == $details['key'] && $object->_spec->incremented) {  
        $db_spec['extra'] = 'auto_increment';
      }
    }
    $details['fields'][$k]['db'] = null;
  }

  // Extraction des propriétés de la classe
  foreach ($object->_props as $k => $v) {
    if (isset($k[0]) && $k[0] != '_') {
      $details['fields'][$k]['object']['spec'] = $v;
    }
  }

  // Extraction des champs de la BDD
  if ($ds && $object->_spec->table && $ds->loadTable($object->_spec->table)) {
    $details['no_table'] = false;

    $sql = "SHOW COLUMNS FROM `{$object->_spec->table}`";
    $list_fields = $ds->loadList($sql);

    foreach ($list_fields as $curr_field) {
      $details['fields'][$curr_field['Field']]['db'] = array();
      if (!isset($details['fields'][$curr_field['Field']]['object'])) {
        $details['fields'][$curr_field['Field']]['object'] = array();
        $details['fields'][$curr_field['Field']]['object']['spec'] = null;
      }
      $field =& $details['fields'][$curr_field['Field']]['db'];

      $props = CMbFieldSpec::parseDBSpec($curr_field['Type']);

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
    $details['duplicates'] = $duplicates;

    foreach ($list_indexes as $curr_index) {
      $details['fields'][$curr_index['Column_name']]['db']['index'] = $curr_index['Key_name'];

      if ($curr_index['Key_name'] == 'PRIMARY') {
        $details['db_key'] = $curr_index['Column_name'];
        if ($object->_spec->incremented) {
          $details['fields'][$curr_index['Column_name']]['object']['db_spec']['extra'] = 'auto_increment';
        }
      }
    }
  }
  else {
    $details['no_table'] = true;
    $details['duplicates'] = array();
  }
  $details['suggestion'] = null;
}

// Tableau indiquant si chaque champ contient une erreur
$list_errors = array();
foreach ($list_classes as $_class => &$class_details) {
  $list_errors[$_class] = array();

  if ($class && $class != $_class) {
    $list_errors[$_class] = null;
    return;
  }

  $show = false;
  foreach ($class_details['fields'] as $curr_field_name => &$curr_field) {
    $list_errors[$_class][$curr_field_name] = false;

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
        $list_errors[$_class][$curr_field_name] = true;
        $show = true;
      }
    }
  }
  if (!$show) {
    $list_errors[$_class] = null;
  }
}

//
//
/**
 * Construit la requete correspondant à la spec d'une classe
 * avec prise en compte des erreurs que l'on souhaite mettre en évidence
 *
 * @param string $class  Le nom de la classe
 * @param bool[] $errors Collection d'erreurs sélectionnées
 *
 * @return string
 */
function get_query_for_class($class, $errors = array()) {
  $change = array();
  $add = array();
  $add_index = array();
  $drop_index = array();
  $ret = '';

  if (!isset($class['fields']) || count($class['fields']) == 0) {
    return;
  }

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
        $errors['default']  && ($spec_obj['default']  != $spec_db['default']) ||
        $errors['extra']    && ($spec_obj['extra']    != $spec_db['extra']) ||
        $errors['null']     && ($spec_obj['null']     != $spec_db['null'])
    ) {

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
         ($spec_obj['unsigned'] ? ' UNSIGNED' : '').
         ($spec_obj['zerofill'] ? ' ZEROFILL' : '').
         ($spec_obj['null']     ? '' : ' NOT NULL').
         ($spec_obj['default'] !== null ? " DEFAULT '{$spec_obj['default']}'" : '').
         ($spec_obj['extra']    ? " {$spec_obj['extra']}" : '').
         (($name == $class['key']) ? ' PRIMARY KEY' : '');
    }

    // creation des lignes d'ajout suppression des index
    if ($errors['index'] && $spec_obj['index'] && !$spec_db['index'] && $class['key'] != $name) {
      $add_index[$name] = "ADD INDEX (`$name`)";
    }

    if ($errors['index'] && !$spec_obj['index'] && $spec_db['index']) {
      $drop_index[$spec_db['index']] = "# DROP INDEX (`{$spec_db['index']}`)";
    }
  }

  $glue = ",\n                ";

  // creation / modification de la table
  if (count($change) > 0) {
    if ($add_table) {
      $ret = "CREATE TABLE `{$class['table']}` (\n                "
        . implode($glue, $change) . "\n              )/*! ENGINE=MyISAM */;";
    }
    else {
      $ret = "ALTER TABLE `{$class['table']}` \n                "
        . implode($glue, $change) . ";";
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
    $ret .= "\nALTER TABLE `{$class['table']}` \n                "
      . implode($glue, $q) . ";";
  }

  if (count($class['duplicates']) > 0) {
    $ret .= "\n# Il y a probablement des index en double sur cette table";
  }

  return $ret;
}

// Enregistre les suggestion pour chaque classe
foreach ($list_classes as $_class => &$class_details) {
  $class_details['suggestion'] = get_query_for_class($class_details, $types);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('installed_classes', $installed_classes);
$smarty->assign('list_classes',      $list_classes);
$smarty->assign('list_errors',       $list_errors);
$smarty->assign('types',             $types);
$smarty->assign('class',             $class);

$smarty->display('mnt_table_classes.tpl');
