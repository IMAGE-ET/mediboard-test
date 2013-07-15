<?php
/**
 * $Id: $
 * Liste des consultations de sage-femme
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 19484 $
 */

CCanDo::checkRead();

global $mode_maternite;
$mode_maternite = true;

CAppUI::requireModuleFile('dPcabinet', 'vw_journee');

?>
