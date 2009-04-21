<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

class CDoRepasAddEdit extends CDoObjectAddEdit {
  
  var $synchro         = null;
  var $synchroConfirm  = null;
  var $synchroDatetime = null;
  var $ds = null;
  
  function CDoRepasAddEdit() {
    global $m;
    
    $this->CDoObjectAddEdit("CRepas", "repas_id");
    
    $this->redirect  = "m=$m&tab=vw_planning_repas";
    
    // Synchronisation Offline
    $this->synchro         = mbGetValueFromPost("_syncroOffline"  , false);
    $this->synchroConfirm  = mbGetValueFromPost("_synchroConfirm" , null);
    $this->synchroDatetime = mbGetValueFromPost("_synchroDatetime", null);
    $this->ds = CSQLDataSource::get("std");
    
  }
  
  function doRedirect($demandeSynchro = false) {
    global $AppUI, $root;

    if ($this->ajax) {
      $idName = $this->objectKeyGetVarName;
      $idValue = $this->_obj->$idName;
      $callBack = $this->callBack;
      
      if($this->synchro){
        $del          = mbGetValueFromPost("del", 0);
        $tmp_repas_id = mbGetValueFromPost("_tmp_repas_id", 0);
        $msgSystem    = $AppUI->getMsg();

        $smarty       = new CSmartyDP("modules/dPrepas");

        $smarty->assign("del"            , $del);
        $smarty->assign("tmp_repas_id"   , $tmp_repas_id);
        $smarty->assign("demandeSynchro" , $demandeSynchro);
        $smarty->assign("msgSystem"      , $msgSystem);
        $smarty->assign("callBack"       , $callBack);
        if($demandeSynchro){
          $smarty->assign("object"     , $this->_objBefore);
        }
        $smarty->display("add_del_repas_offline.tpl");
      }
			CApp::rip();
    }

    if ($this->redirect !== null) {
      $AppUI->redirect($this->redirect);
    }
  }

  function doIt() {
    global $AppUI;
    $this->doBind();
    
    if($this->synchro){
       if(!$this->_objBefore->_id && $this->_obj->repas_id){
         // Repas supprimé depuis la derniere synchro
         $AppUI->setMsg("Le repas a été supprimé depuis la dernière synchronisation.", UI_MSG_ERROR );
         $this->doRedirect();
       }
       //Test suppression de ref
       $error_ref = null;
       $object    = $this->_obj;
       $plats     = new CPlat;
       $object->loadRemplacements();
       $object->loadRefAffectation();
       $object->loadRefMenu();
       if(!$object->_ref_affectation->affectation_id){
         $AppUI->setMsg("L'affectation n'existe pas.", UI_MSG_ERROR );
         $error_ref = true;
       }
       if($object->menu_id && !$object->_ref_menu->menu_id){
         $AppUI->setMsg("Le menu n'existe pas.", UI_MSG_ERROR );
         $error_ref = true;
       }
       foreach($plats->_specs["type"]->_list as $curr_typePlat){
         if($object->$curr_typePlat && !$object->{"_ref_".$curr_typePlat}){
           $AppUI->setMsg("Le Plat de remplacement ".$curr_typePlat." n'existe pas.", UI_MSG_ERROR );
           $error_ref = true;
         }
       }
       if($error_ref){
         $this->doRedirect();
       }
       if(!$this->synchroConfirm && $this->_objBefore->_id){
         $object = $this->_objBefore;
         
         $select = "count(`user_log_id`) AS `total`";
         $table  = "user_log";
         $where = array();
         $where["object_id"]    = "= '$object->_id'";
         $where["object_class"] = "= '$this->className'";
         $where["date"]         = ">= '".strftime("%Y-%m-%d %H:%M:%S", $this->synchroDatetime)."'";
         
         $sql = new CRequest();
         $sql->addTable($table);
         $sql->addSelect($select);
         $sql->addWhere($where);
         
         $nbLogs = $this->ds->loadResult($sql->getRequest());
         
         if($nbLogs){
           $AppUI->setMsg("Le repas a été modifié depuis la dernière synchronisation. Voulez-vous tout de même l'enregistrer ?", UI_MSG_WARNING);
           $this->doRedirect(true);
         }
       }
    }

    if (intval(dPgetParam($_POST, 'del'))) {
      $this->doDelete();
    } else {
      $this->doStore();
    }

    $this->doRedirect();
  }  
  
}

$do = new CDoRepasAddEdit;
$do->doIt();

?>