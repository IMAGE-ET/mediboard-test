<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $m;

$entree = $_POST["entree"];
$sortie = $_POST["sortie"];

// Modifier la première affectation
$do = new CDoObjectAddEdit("CAffectation", "affectation_id");

$_POST["entree"] = $entree;
$_POST["sortie"] = $_POST["_date_split"];

$do->redirect = null;
$do->redirectStore = null;
$do->doIt();

// Créer le second
$do = new CDoObjectAddEdit("CAffectation", "affectation_id");

$_POST["entree"] = $_POST["_date_split"];
$_POST["sortie"] = $sortie;
$_POST["lit_id"] = $_POST["_new_lit_id"] ;
$_POST["affectation_id"] = null;

$do->redirectStore = "m={$m}";
$do->doIt();

?>