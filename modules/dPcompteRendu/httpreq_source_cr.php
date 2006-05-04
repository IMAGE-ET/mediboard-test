<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers'));
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu'));
require_once( $AppUI->getModuleClass('dPcompteRendu', 'listeChoix'));
require_once( $AppUI->getModuleClass('dPcompteRendu', 'templatemanager'));

if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$compte_rendu_id = dPgetParam($_GET, "compte_rendu_id", 0);

// Faire ici le test des différentes variables dont on a besoin

$compte_rendu = new CCompteRendu;
if($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
}

echo $compte_rendu->source;

?>