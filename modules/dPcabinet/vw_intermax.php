<?php /* $Id: vw_compta.php 1738 2007-03-19 16:33:47Z maskas $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1738 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->edit &= $AppUI->user_prefs["GestionFSE"];
$can->needsEdit();

$intermaxFunctions = array(
  "Fonctions Intégrées" => array(
    "Lire CPS",
    "Lire Vitale",
    "Consulter Vitale",
    "Formater FSE",
  ),
  "Générales" => array(
    "Professionnels de santé",
    "Feuilles de soins",
    "Visites",
    "Télétransmission",
    "Liaison comptable",
  ),
  "Préférences" => array(
    "Configuration",
    "Coordonnées",
    "Préférences Liaison comptable",
    "Fichiers RSP",
    "Tiers-payant",
    "Type Emetteur",
  ),
  "Fichiers de base" => array(
    "Titres",
    "Jours fériés",
    "Communes",
    "Prescripteurs",
    "Lettres clés",
    "Organismes AMO",
    "Organismes AMC",
    "Centre de service",
    "Thésaurus",
  ),
  "Utilitaires" => array(
    "Modification référentiel CCAM",
    "Vérification des données",
    "Recalcul des soldes patients",
    "Emettre vers le CNDA",
    "Déblocage CPS",
    "Mode de Trace",
    "Initialisation des compteurs",
  ),
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("intermaxFunctions", $intermaxFunctions);
$smarty->display("vw_intermax.tpl");

