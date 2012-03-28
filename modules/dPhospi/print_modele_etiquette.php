<?php /* $Id: print_reception_barcodes.php 9732 2010-08-04 09:50:58Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 9732 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$modele_etiquette = new CModeleEtiquette();

$modele_etiquette->largeur_page  = CValue::post("largeur_page");
$modele_etiquette->hauteur_page  = CValue::post("hauteur_page");
$modele_etiquette->nb_lignes     = CValue::post("nb_lignes");
$modele_etiquette->nb_colonnes   = CValue::post("nb_colonnes");
$modele_etiquette->marge_horiz   = CValue::post("marge_horiz");
$modele_etiquette->marge_vert    = CValue::post("marge_vert");
$modele_etiquette->hauteur_ligne = CValue::post("hauteur_ligne");
$modele_etiquette->nom           = CValue::post("nom");
$modele_etiquette->texte         = CValue::post("texte");
$modele_etiquette->texte_2       = CValue::post("texte_2");
$modele_etiquette->texte_3       = CValue::post("texte_3");
$modele_etiquette->texte_4       = CValue::post("texte_4");
$modele_etiquette->font          = CValue::post("font");
$modele_etiquette->show_border   = CValue::post("show_border");
$modele_etiquette->text_align    = CValue::post("text_align");
$modele_etiquette->printEtiquettes();
?>