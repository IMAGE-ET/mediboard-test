<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Romain Ollivier
 *  @version $Revision: $
 */

global $uistyle, $m, $AppUI, $tab;

class CTabIndex {

  var $tabBox = null;
    
  function CTabIndex($tabs = array(), $default = 0) {
    global $AppUI, $canRead, $canEdit, $m, $tab, $_GET;
    if(!$canRead) {
      $AppUI->redirect("m=system&a=access_denied");
    }
    $AppUI->savePlace();
    if(isset($_GET["tab"])) {
      $AppUI->setState($m."IdxTab", $_GET["tab"]);
    }
    
    // For each tabs :
    // 0 : file name, 1 : tab name, 2 : can edit right needed
    foreach($tabs as $key => $curr_tab) {
      if($curr_tab[2]) {
        if($canEdit) {
          $this->tabBox[]= array($curr_tab[0], $curr_tab[1]);
        }
      } else {
        $this->tabBox[]= array($curr_tab[0], $curr_tab[1]);
      }
    }
    
    $tab = $AppUI->getState($m."IdxTab") !== null ? $AppUI->getState($m."IdxTab") : $default;
    if(is_numeric($tab)){
      if(array_key_exists($tab,$this->tabBox)){
        $tab = $this->tabBox[$tab][0];
      }else{ 
        $tab = @$this->tabBox[0][0];
      }
    }
  }
  
  function show() {
  	global $uistyle, $m, $AppUI,$tab;

    require_once( $AppUI->getSystemClass("smartydp"));
    $smartyStyle = new CSmartyDP(1);
    $smartyStyle->template_dir = "style/$uistyle/templates/";
    $smartyStyle->compile_dir  = "style/$uistyle/templates_c/";
    $smartyStyle->config_dir   = "style/$uistyle/configs/";
    $smartyStyle->cache_dir    = "style/$uistyle/cache/";
    
    $smartyStyle->assign("tabs" , $this->tabBox);
    $smartyStyle->assign("tab" , $tab);
    $smartyStyle->assign("fintab" , false);
      
    $smartyStyle->display("tabbox.tpl");
    require($AppUI->cfg["root_dir"]."/modules/$m/".$tab.".php");
  
    $smartyStyle->assign("fintab" , true);
    $smartyStyle->display("tabbox.tpl");
  }
}
