<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

if( isset($_POST["_do_empty_pdf"])) {
  $compte_rendu_id = CValue::post("compte_rendu_id");
  $compte_rendu = new CCompteRendu();
  $compte_rendu->load($compte_rendu_id);

  $compte_rendu->loadRefsFiles();
  foreach($compte_rendu->_ref_files as $_file) {
    $_file->file_empty();
  }
  CApp::rip();
}

$do = new CDoObjectAddEdit("CCompteRendu", "compte_rendu_id");
$do->redirectDelete = "m=dPcompteRendu&new=1";

// Récupération des marges du modele en fast mode
if (isset($_POST["fast_edit"]) && $_POST["fast_edit"] == 1 && isset($_POST["object_id"]) && $_POST["object_id"] != '') {
  $compte_rendu = new CCompteRendu;
  $compte_rendu->load($_POST["modele_id"]);
  
  if ($compte_rendu->_id) {
    $do->request["margin_top"]    = $compte_rendu->margin_top;
    $do->request["margin_bottom"] = $compte_rendu->margin_bottom;
    $do->request["margin_left"]   = $compte_rendu->margin_left;
    $do->request["margin_right"]  = $compte_rendu->margin_right;
  }
}

if (isset($_POST["_source"])) {
  $_POST["_source"] = stripslashes($_POST["_source"]);
}

$check_to_empty_field = CAppUI::conf("dPcompteRendu CCompteRendu check_to_empty_field");

// Remplacement des zones de texte libre
if (isset($_POST["_texte_libre"])) {
  $compte_rendu = new CCompteRendu();
  $fields = array();
  $values = array();

  // Remplacement des \n par des <br>
  foreach($_POST["_texte_libre"] as $key=>$_texte_libre) {
    if (($check_to_empty_field && isset($_POST["_empty_texte_libre"][$key])) ||
        (!$check_to_empty_field && !isset($_POST["_empty_texte_libre"][$key])) ||
        $_POST["_texte_libre"][$key] != '') {
      $fields[] = "[[Texte libre - " . $_POST["_texte_libre_md5"][$key] . "]]";
      $values[] = nl2br($_POST["_texte_libre"][$key]);
    }
  }
  $_POST["_source"] = str_ireplace($fields, $values, $_POST["_source"]);
  $_POST["_texte_libre"] = null;
}

$destinataires = array();
$correspondants_courrier = array();
$ids_corres    = "";
$do_merge = CValue::post("do_merge", 0);

if (isset($_POST["_source"])) {
  // Ajout d'entête / pied de page à la volée
  $header_id = CValue::post("header_id");
  $footer_id = CValue::post("footer_id");
  if (($header_id || $footer_id) && isset($_POST["object_id"]) && $_POST["object_id"] != null) {
    $cr = new CCompteRendu;
    $_POST["_source"] = $cr->generateDocFromModel($_POST["_source"], $header_id, $footer_id);
  }
  
  // Application des listes de choix
  $fields = array();
  $values = array();
  if (isset($_POST["_CListeChoix"])) {
    $listes = $_POST["_CListeChoix"];
    foreach ($listes as $list_id => $options) {
      $options = array_map('htmlentities', $options);
      $list = new CListeChoix;
      $list->load($list_id);
      
      if (($check_to_empty_field && isset($_POST["_empty_list"][$list_id])) ||
          (!$check_to_empty_field && !isset($_POST["_empty_list"][$list_id]))) {
        $values[] = "";
      }
      else {
        if ($options === array(0 => "undef")) {
          continue;
        }
        CMbArray::removeValue("undef", $options);
        $values[] = nl2br(implode(", ", $options));
      }
      $nom = str_replace("#039;", "#39;", htmlentities($list->nom, ENT_QUOTES));
      $fields[] = "[Liste - ".$nom."]";
    }
  }
  
  $_POST["_source"] = str_ireplace($fields, $values, $_POST["_source"]);
  
  // Pour un nouveau document
  if (!$_POST["compte_rendu_id"]) {
    // Application des destinataires
    foreach($_POST as $key => $value) {
      // Remplacement des destinataires
      if(preg_match("/_dest_([\w]+)_([0-9]+)/", $key, $dest)) {
        $destinataires[] = $dest;
      }
    }
  }
  else {
    $compte_rendu = new CCompteRendu;
    $compte_rendu->load($_POST["compte_rendu_id"]);
    $correspondants_courrier = $compte_rendu->loadRefsCorrespondantsCourrier();
    foreach ($correspondants_courrier as $_corres) {
      $_corres->active = CValue::post("_corres_{$_corres->_id}");
      if ($msg = $_corres->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
  
  if ((count($destinataires) || count($correspondants_courrier)) && $do_merge) {
    
    $object = new $_POST["object_class"];
    $object->load($_POST["object_id"]);
    
    CDestinataire::makeAllFor($object);
    $allDest = CDestinataire::$destByClass;
    
    $bodyTag = '<div id="body">';

    // On sort l'en-tête et le pied de page
    $posBody      = strpos($_POST["_source"], $bodyTag);

    if($posBody) {
      $headerfooter = substr($_POST["_source"], 0, $posBody);
      $index_div    = strrpos($_POST["_source"], "</div>")-($posBody+strlen($bodyTag));
      $body         = substr($_POST["_source"], $posBody+strlen($bodyTag), $index_div);
    }
    else {
      $headerfooter = "";
      $body         = $_POST["_source"];
    }
    
    // On fait le doBind avant le foreach si la config est à 1.
    if (CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants")) {
      $do->doBind();
    }
    
    $allSources = array();
    $modele_base = clone $do->_obj;
    $source_base = $body;
    $has_object_id = $do->_obj->object_id;
    $doc_id = $do->_obj->_id;

    // Si c'est un modèle existant, on utilise les correspondants courrier
    if ($_POST["compte_rendu_id"]) {
      $destinataires = $correspondants_courrier;
    }

    foreach ($destinataires as &$curr_dest) {
      // Si le correspondant n'est pas actif, on continue
      if ($_POST["compte_rendu_id"] && !$curr_dest->active) continue;
      
      $fields = array(
        htmlentities("[Courrier - nom destinataire]"),
        htmlentities("[Courrier - adresse destinataire]"),
        htmlentities("[Courrier - cp ville destinataire]"),
        htmlentities("[Courrier - copie à]"),
        htmlentities("[Courrier - copie à (complet)]")
      );
      
      // Champ copie à : on reconstruit en omettant le destinataire.
      $copyTo = "";
      $copyToComplet = "";
      
      // Pour un nouveau modèle
      if (!$_POST["compte_rendu_id"]) {
        foreach ($destinataires as $_dest) {
          if ($curr_dest[0] == $_dest[0]) continue;  
          $copyTo .= $allDest[$_dest[1]][$_dest[2]]->nom."; ";
          $copyToComplet .= $allDest[$_dest[1]][$_dest[2]]->nom. " - " .
                            $allDest[$_dest[1]][$_dest[2]]->adresse. " ".
                            $allDest[$_dest[1]][$_dest[2]]->cpville. "; ";
        }
        
        $values = array(
          $allDest[$curr_dest[1]][$curr_dest[2]]->nom,
          $allDest[$curr_dest[1]][$curr_dest[2]]->adresse,
          $allDest[$curr_dest[1]][$curr_dest[2]]->cpville,
          $copyTo,
          $copyToComplet
        );
      }
      // Pour un modèle existant
      else {
        foreach ($destinataires as &$_dest) {
          if ($_dest == $curr_dest || !$_dest->active) continue;
          $copyTo .= $_dest->nom."; ";
          $copyToComplet .= "$_dest->nom - $_dest->adresse - $_dest->cp_ville";
        }
        
        $values = array(
          $curr_dest->nom,
          $curr_dest->adresse,
          $curr_dest->cp_ville,
          $copyTo,
          $copyToComplet
        );
      }
      
      if (!CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants")) {
        $allSources[] = str_ireplace($fields, $values, $body);
      }
      else {
        // Création d'un document par correspondant
        $body = str_ireplace($fields, $values, $source_base);
        
        $content = $body;
        
        if ($headerfooter) {
          $content = $headerfooter . "<div id=\"body\">" . $body . "</div>";
        }
        
        // Si le compte-rendu a déjà été enregistré et que l'on applique le premier destinataire,
        // on modifie le compte-rendu existant
        if ($do->_obj->_id && $curr_dest === reset($destinataires)) {
          $compte_rendu = $do->_obj;
        }
        // Sinon clone du modèle
        else {
          $compte_rendu = clone $modele_base;
          $compte_rendu->_id     = null;
          $compte_rendu->_ref_content = null;
          $compte_rendu->user_id    = null;
          $comte_rendu->function_id = null;
          $compte_rendu->group_id   = null;
          $do->_obj = $compte_rendu;
        }
        
        $compte_rendu->_source = $content;
        if ($_POST["compte_rendu_id"]) {
          $compte_rendu->nom .= " à $curr_dest->nom";
        }
        else {
          $compte_rendu->nom    .= " à {$allDest[$curr_dest[1]][$curr_dest[2]]->nom}";
        }
        $do->doStore();
        $ids_corres .= "{$do->_obj->_id}-";
      }
    }
    if (!CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants")) {
      // On concatène les en-tête, pieds de page et body's
      if ($headerfooter) {
        $_POST["_source"] = $headerfooter;
        $_POST["_source"] .= "<div id=\"body\">";
        $_POST["_source"] .= implode("<hr class=\"pageBreak\" />", $allSources);
        $_POST["_source"] .= "</div>";
      }
      else {
        $_POST["_source"] = implode("<hr class=\"pageBreak\" />", $allSources);
      }
    }
    
    // Suppression des correspondants courrier
    foreach ($correspondants_courrier as $_corres) {
      $_corres->delete();
    }
  }
}

if (!CAppUI::conf("dPcompteRendu CCompteRendu multiple_doc_correspondants") || !$do_merge) {
  $do->doBind();
  if (intval(CValue::post("del"))) {
    $do->doDelete();
  }
  else {
    $do->doStore();
    // Pour le fast mode en impression navigateur, on envoie la source du document complet.
    $margins = array(
      $do->_obj->margin_top,
      $do->_obj->margin_right,
      $do->_obj->margin_bottom,
      $do->_obj->margin_left);
    $do->_obj->_entire_doc = CCompteRendu::loadHTMLcontent($do->_obj->_source, "doc",'','','','','',$margins);
  }
}

// Gestion des CCorrespondantCourrier
if (!$do_merge && !intval(CValue::post("del")) && strpos($do->_obj->_source, "[Courrier -")) {
  // Nouveau modèle

  if (!$do->_objBefore->_id) {
    $object = new $_POST["object_class"];
    $object->load($_POST["object_id"]);
    CDestinataire::makeAllFor($object);
    $allDest = CDestinataire::$destByClass;
    
    foreach ($allDest as $class => $_dest_by_class) {
      foreach ($_dest_by_class as $i => $_dest) {
        $corres = new CCorrespondantCourrier;
        $corres->compte_rendu_id = $do->_obj->_id;
        $corres->nom = $_dest->nom;
        $corres->adresse = $_dest->adresse;
        $corres->cp_ville = $_dest->cpville;
        $corres->email = $_dest->email;
        $corres->tag = $_dest->tag;
        $corres->object_class = $class;
        $corres->active = isset($_POST["_dest_{$class}_$i"]) ? 1 : 0;
        
        if ($msg = $corres->store()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
      }
    }
  }
  // Modèle existant
  else {
    $correspondants_courrier = $do->_obj->loadRefsCorrespondantsCourrier();
    foreach ($correspondants_courrier as $_corres) {
      $_corres->active = CValue::post("_corres_{$_corres->_id}");
      if ($msg = $_corres->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
}

if (strlen($ids_corres)) {
  $do->_obj->_ids_corres = $ids_corres;
}

if ($do->ajax) {
  $do->doCallback();
}
else {
  // Si c'est un compte rendu
  if($do->_obj->object_id && !intval(CValue::post("del"))) {
    $do->redirect = "m=dPcompteRendu&a=edit_compte_rendu&dialog=1&compte_rendu_id=".$do->_obj->_id;
  }
  else if (intval(CValue::post("del") && isset($_POST["_tab"]))) {
    $do->redirect = "m=dPcompteRendu&a=vw_modeles";
  }
  // Si c'est un modèle de compte rendu
  else { 
    $do->redirect = "m=dPcompteRendu&tab=addedit_modeles&compte_rendu_id=".$do->_obj->_id;
  }
  $do->doRedirect();
}
?>