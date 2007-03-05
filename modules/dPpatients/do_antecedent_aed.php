<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$autoadd_default = isset($AppUI->user_prefs["AUTOADDSIGN"]) ? $AppUI->user_prefs["AUTOADDSIGN"] : 1 ;

class CDoAntecedentAddEdit extends CDoObjectAddEdit {
  function CDoAntecedentAddEdit() {
    $this->CDoObjectAddEdit("CAntecedent", "antecedent_id");
    
    $this->createMsg = "Antecedent créé";
    $this->modifyMsg = "Antecedent modifié";
    $this->deleteMsg = "Antecedent supprimé";
  }
  
  function doIt() {
    global $autoadd_default ;
    $this->doBind();

    if (intval(dPgetParam($_POST, 'del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }
    if(! (!$this->isNotNew && isset($_POST["consultation_anesth_id"]) && $this->_obj->object_class == "CPatient" && $autoadd_default == 1)){
      $this->doRedirect();
    }
  }
}

$do = new CDoAntecedentAddEdit;
$do->doIt();

$_POST["object_class"] = "CConsultAnesth";
$_POST["object_id"]    = $_POST["consultation_anesth_id"];

$copyDo = new CDoObjectAddEdit("CAntecedent", "antecedent_id");
$copyDo->createMsg = "Antecedent créé";
$copyDo->modifyMsg = "Antecedent modifié";
$copyDo->deleteMsg = "Antecedent supprimé";

$copyDo->doBind();
$copyDo->ajax            = $do->ajax;
$copyDo->suppressHeaders = $do->suppressHeaders;
$copyDo->callBack        = "reloadAntecedentsAnesth";
$copyDo->doStore();
$copyDo->doRedirect();
?>