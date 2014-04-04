<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage cim10
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

global $can;
$can->needsAdmin();

// Extraction des codes supplémentaires de l'ATIH
$targetDir = 'tmp/cim10';
$sourcePath = 'modules/dPcim10/base/cim10_modifs.tar.gz';
$targetPath = 'tmp/cim10/cim10_modifs.csv';
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive cim10_modifs.csv", UI_MSG_ERROR);
}
CAppUI::stepAjax("Extraction de $nbFiles fichier(s) [cim10_modifs.csv]", UI_MSG_OK);

$ds = CSQLDataSource::get('cim10', true);
if (!$ds) {
  CAppUI::stepAjax('La source de données cim10 n\'existe pas.', UI_MSG_ERROR);
  CApp::rip();
}

$import = new CImportCim10($targetPath, $ds);
$import->run();