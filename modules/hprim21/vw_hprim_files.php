<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision: 9209 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkAdmin();

// Création du template
$smarty = new CSmartyDP();
$smarty->display("vw_hprim_files.tpl");

