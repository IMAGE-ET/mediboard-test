<?php

/**
 * Stats sur les documents
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

global $m;
$m = "dPfiles";
$_GET["doc_class"] = "CCompteRendu";
CAppUI::requireModuleFile($m, "vw_stats");
$m = "dPcompteRendu";
