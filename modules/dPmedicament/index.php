<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_recherche", TAB_READ);
$module->registerTab("vw_idx_livret"   , TAB_READ);
$module->registerTab("vw_idx_fiche_ATC", TAB_READ);
$module->registerTab("vw_edit_produits", TAB_READ);

?>