<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Thomas Despoix
 */
 
global $can, $m, $AppUI, $dPconfig, $remote_name;

$can->needsAdmin();

function importCatalogue($cat, $parent_id = null) {
  global $remote_name;
  
  $catalogue = new CCatalogueLabo;
  $catalogue->identifiant = (string) $cat->identifiant;
  $catalogue->libelle = (string) $cat->libelle;

  mbTrace($catalogue->getProps(), "Synchronisation du catalogue");
  
  $catAtt = $cat->attributes();
  
  $idCat = new CIdSante400;
  $idCat->tag = $remote_name;
  $idCat->id400 = (string) $catAtt["id"];
  
  $idCat->bindObject($catalogue);
    
  mbTrace($idCat->getProps(), "Identifiant Cat");

  foreach ($cat->sections->catalogue as $_cat) {
    importCatalogue($_cat ,$catalogue->_id);
  }
}

$config = $dPconfig[$m]["CCatalogueLabo"];

if (null == $remote_name = $config["remote_name"]) {
  $AppUI->stepAjax("Remote name not configured", UI_MSG_ERROR);
}

if (null == $remote_url = $config["remote_url"]) {
  $AppUI->stepAjax("Remote URL not configured", UI_MSG_ERROR);
}

if (false === $content = file_get_contents($remote_url)) {
  $AppUI->stepAjax("Couldn't connect to remote url", UI_MSG_ERROR);
}

$cat = new SimpleXMLElement($content);
importCatalogue($cat);

mbTrace($cat, "Catalogue");

?>
