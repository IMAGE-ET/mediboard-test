<?php
/**
 * Liste des consultations de sage-femme
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

global $mode_maternite;
$mode_maternite = true;

CAppUI::requireModuleFile('dPcabinet', 'vw_journee');
