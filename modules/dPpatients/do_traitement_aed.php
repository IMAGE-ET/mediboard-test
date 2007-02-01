<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$autoadd_default = isset($AppUI->user_prefs["AUTOADDSIGN"]) ? $AppUI->user_prefs["AUTOADDSIGN"] : 1 ;

class CDoTraitementAddEdit extends CDoObjectAddEdit {
  function CDoTraitementAddEdit() {
    $this->CDoObjectAddEdit("CTraitement", "traitement_id");
    
    $this->createMsg = "Traitement créé";
    $this->modifyMsg = "Traitement modifié";
    $this->deleteMsg = "Traitement supprimé";
  }
  
  function doIt() {
    global $autoadd_default ;
    $this->doBind();

    if (intval(dPgetParam($_POST, 'del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }
    if(! (!$this->isNotNew && isset($_POST["consultation_anesth_id"]) && $this->_obj->object_class == "CPatient" && $autoadd_default == 0)){
      $this->doRedirect();
    }
  }
}

$do = new CDoTraitementAddEdit;
$do->doIt();

$_POST["object_class"] = "CConsultAnesth";
$_POST["object_id"]    = $_POST["consultation_anesth_id"];

$copyDo = new CDoObjectAddEdit("CTraitement", "traitement_id");
$copyDo->createMsg = "Traitement créé";
$copyDo->modifyMsg = "Traitement modifié";
$copyDo->deleteMsg = "Traitement supprimé";

$copyDo->doBind();
$copyDo->ajax            = $do->ajax;
$copyDo->suppressHeaders = $do->suppressHeaders;
$copyDo->callBack        = "reloadAntecedentsAnesth";
$copyDo->doStore();
$copyDo->doRedirect();
?>