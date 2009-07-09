<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Global system version
$version = array (
  // Manual numbering
  "major" => 0,
  "minor" => 4,
  "patch" => 0,
  
  // Automated numbering (should be incremented at each commit)
  "build" => 459,
);

$version["string"] = implode(".", $version);
