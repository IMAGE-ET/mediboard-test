<?php /* $Id: httpreq_do_empty_shared_memory.php 8987 2010-05-24 15:58:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8987 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// This script has to be launched via installer
global $can;

// Only check permissions when connected to mediboard, not to the installer
if ($can) {
  $can->needsAdmin();
}

CConfigConstantesMedicales::emptySHM();
