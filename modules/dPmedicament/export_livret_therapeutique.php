<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$out = fopen('php://output', 'w');
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="LivretTherapeutique.csv"');

$group = CGroups::loadCurrent();
$group->loadRefLivretTherapeutique(null, 2000, false);

foreach($group->_ref_produits_livret as $_livret){
	$fields = array();
	$fields["code_cip"] = $_livret->code_cip;
	$fields["prix_hopital"] = $_livret->prix_hopital;
  $fields["prix_ville"] = $_livret->prix_ville;
  $fields["date_prix_ville"] = $_livret->date_prix_ville;
  $fields["date_prix_hopital"] = $_livret->date_prix_hopital;
  $fields["commentaire"] = $_livret->commentaire;
	$fields["code_interne"] = $_livret->code_interne;
	fputcsv($out, $fields);
}

?>