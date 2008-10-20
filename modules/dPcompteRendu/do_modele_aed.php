<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CCompteRendu", "compte_rendu_id");
$do->redirectDelete = "m=$m&new=1";

// Application des listes de choix
if(isset($_POST["source"])) {
  $fields = array();
  $values = array();
  $destinataires = array();
  foreach($_POST as $key => $value) {
    if(preg_match("/_liste([0-9]+)/", $key, $result)) {
      $temp = new CListeChoix;
      $temp->load($result[1]);
      if($value != "undef") {
        // @todo : passer en regexp
        //$fields[] = "<span class=\"name\">[Liste - ".htmlentities($temp->nom)."]</span>";
        //$values[] = "<span class=\"choice\">$value</span>";
        $fields[] = "[Liste - ".htmlentities($temp->nom)."]";
        $values[] = nl2br("$value");
      }
    } elseif(preg_match("/_dest_([\w]+)_([0-9]+)/", $key, $dest)) {
      $destinataires[] = $dest;
    }
  }
  $_POST["source"] = str_replace($fields, $values, $_POST["source"]);
  if(count($destinataires)) {
    $object = new $_POST["object_class"];
    $object->load($_POST["object_id"]);
    CDestinataire::makeAllFor($object);
    $allDest = CDestinataire::$destByClass;
    // On sort l'en-tête et le pied de page
    $posBody      = strpos($_POST["source"], '<div id=\"body\">');
    if($posBody) {
      $headerfooter = substr($_POST["source"], 0, $posBody);
      $body         = substr($_POST["source"], $posBody+strlen('<div id=\"body\">'), -strlen("</div>"));
    } else {
      $headerfooter = "";
      $body         = $_POST["source"];
    }
    // On crée les fichiers pour chaque destinataire
    $copyTo = "";
    foreach($destinataires as $curr_dest) {
      $copyTo .= $allDest[$curr_dest[1]][$curr_dest[2]]->nom."; ";
    }
    $allSources = array();
    foreach($destinataires as &$curr_dest) {
      $fields = array(
        htmlentities("[Courrier - nom destinataire]"),
        htmlentities("[Courrier - adresse destinataire]"),
        htmlentities("[Courrier - cp ville destinataire]"),
        htmlentities("[Courrier - copie à]")
      );
      $values = array(
        $allDest[$curr_dest[1]][$curr_dest[2]]->nom,
        $allDest[$curr_dest[1]][$curr_dest[2]]->adresse,
        $allDest[$curr_dest[1]][$curr_dest[2]]->cpville,
        $copyTo
      );
      $allSources[] = str_replace($fields, $values, $body);
    }
    // On concatène les en-tête, pieds de page et body's
    if($headerfooter) {
      $_POST["source"] = $headerfooter;
      $_POST["source"] .= "<div id=\"body\">";
      $_POST["source"] .= implode("<hr class=\"pageBreak\" />", $allSources);
      $_POST["source"] .= "</div>";
    } else {
      $_POST["source"] = implode("<hr class=\"pageBreak\" />", $allSources);
    }
  }
  
}

$do->doBind();
if (intval(dPgetParam($_POST, "del"))) {
  $do->doDelete();
} else {
  $do->doStore();
}


if($do->ajax){
 $idName   = $do->objectKeyGetVarName;
 $callBack = $do->callBack;
 $idValue  = $do->_obj->$idName;
 echo $AppUI->getMsg();
 if ($callBack) {
   echo "\n<script type='text/javascript'>$callBack($idValue);</script>";
 }
 CApp::rip();
 
}else{

  if($do->_obj->object_id && !intval(dPgetParam($_POST, "del"))) {
    $do->redirectStore = "m=$m&a=edit_compte_rendu&dialog=1&compte_rendu_id=".$do->_obj->_id;
  ?>
    <script language="javascript">
      var url = 'index.php?m=dPcompteRendu&a=edit_compte_rendu&dialog=1&compte_rendu_id=';
      url += '<?php echo $do->_obj->_id ?>';
      window.location.href = url;
    </script>
  <?php
  } else { 
    $do->redirectStore = "m=$m&compte_rendu_id=".$do->_obj->_id;
    $do->doRedirect();
  }

}
?>