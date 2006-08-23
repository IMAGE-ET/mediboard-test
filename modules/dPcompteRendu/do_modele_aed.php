<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPcabinet", "consultation"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPcompteRendu", "listeChoix"));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CCompteRendu", "compte_rendu_id");
$do->createMsg = "Document créé";
$do->modifyMsg = "Document modifié";
$do->deleteMsg = "Document supprimé";
$do->redirectDelete = "m=$m&new=1";

// Application des listes de choix
if(isset($_POST["source"])) {
  $fields = array();
  $values = array();
  foreach($_POST as $key => $value) {
    if(preg_match("/_liste([0-9]+)/", $key, $result)) {
      $temp = new CListeChoix;
      $temp->load($result[1]);
      if($value != "undef") {
        // @todo : passer en regexp
        //$fields[] = "<span class=\"name\">[Liste - ".htmlentities($temp->nom)."]</span>";
        //$values[] = "<span class=\"choice\">$value</span>";
        $fields[] = "[Liste - ".htmlentities($temp->nom)."]";
        $values[] = "$value";
      }
    }
  }
  $_POST["source"] = str_replace($fields, $values, $_POST["source"]);
}

$do->doBind();
if (intval(dPgetParam($_POST, "del"))) {
  $do->doDelete();
} else {
 $do->doStore();
}

if($do->_obj->object_id && !intval(dPgetParam($_POST, "del"))) {
  $do->redirectStore = "m=$m&a=edit_compte_rendu&dialog=1&compte_rendu_id=".$do->_obj->compte_rendu_id;
?>
  <script language="javascript">
    var url = 'index.php?m=dPcompteRendu&a=edit_compte_rendu&dialog=1&compte_rendu_id=';
    url += '<?php echo $do->_obj->compte_rendu_id ?>';
    window.location.href = url;
  </script>
<?php
} else { 
  $do->redirectStore = "m=$m&compte_rendu_id=".$do->_obj->compte_rendu_id;
  $do->doRedirect();
}

?>