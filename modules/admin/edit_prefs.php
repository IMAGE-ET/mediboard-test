<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $utypes;

// Utilisateur demandé
$user_id = mbGetValueFromGet("user_id" , 0);

// Vérification des droit
if ($canEdit){
  $user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);
}else{
  $user_id = $AppUI->user_id;
}  

// Chargement User demandé
$user = null;
if($user_id){
  $user = new CUser;
  $user->load($user_id);
}

// load the preferences
$sql = "
SELECT pref_name, pref_value
FROM user_preferences
WHERE pref_user = $user_id
";
$prefs = db_loadHashList( $sql );

if(!array_key_exists("LOCALE",$prefs)){
  $prefs["LOCALE"] = null;
}
if(!array_key_exists("UISTYLE",$prefs)){
  $prefs["UISTYLE"] = null;
}

// Chargement des languages
$locales = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn", $locales);

// Chargement des styles
$styles = $AppUI->readDirs("style");
mbRemoveValuesInArray(".svn", $styles);


// Création du template
$smarty = new CSmartyDP(1);
$smarty->assign("user"     , $user);
$smarty->assign("user_id"  , $user_id);
$smarty->assign("locales"  , $locales);
$smarty->assign("styles"   , $styles);
$smarty->assign("prefs"    , $prefs);

$smarty->display("edit_prefs.tpl");
?>
