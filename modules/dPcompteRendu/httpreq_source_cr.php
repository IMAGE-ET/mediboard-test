<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m;

$can->needsEdit();

$compte_rendu_id = dPgetParam($_GET, "compte_rendu_id", 0);

// Faire ici le test des différentes variables dont on a besoin

$compte_rendu = new CCompteRendu;
if($compte_rendu_id) {
  $compte_rendu->load($compte_rendu_id);
}

echo $compte_rendu->source;

?>