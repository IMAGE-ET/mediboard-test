<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Romain Ollivier
 *  @version $Revision: $
 */

class CTabIndex {

  var $tabBox = null;
    
  function CTabIndex($tabs = array(), $default = 0) {
    global $AppUI, $canRead, $canEdit, $m, $tab, $_GET;
    $canRead = !getDenyRead($m);
    $canEdit = !getDenyEdit($m);
    if(!$canRead) {
      $AppUI->redirect("m=system&a=access_denied");
    }
    $AppUI->savePlace();
    if(isset($_GET["tab"])) {
      $AppUI->setState($m."IdxTab", $_GET["tab"]);
    }
    $tab = $AppUI->getState($m."IdxTab") !== null ? $AppUI->getState($m."IdxTab") : $default;
    $this->tabBox = new CTabBox("?m=$m", $AppUI->cfg["root_dir"]."/modules/$m/", $tab);
    // For each tabs :
    // 0 : file name, 1 : tab name, 2 : can edit right needed
    foreach($tabs as $key => $curr_tab) {
      if($curr_tab[2]) {
        if($canEdit) {
          $this->tabBox->add($curr_tab[0], $curr_tab[1]);
        }
      } else {
        $this->tabBox->add($curr_tab[0], $curr_tab[1]);
      }
    }
  }
  
  function show() {
    $this->tabBox->show();
  }
}
