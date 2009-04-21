<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$livretTherap = new CBcbProduitLivretTherapeutique();
$livretTherap->Synchronize();

CAppUI::stepAjax("Livret Thérapeutique synchronisé");

?>

