<?php

/**
 * Impression d'un fichier par une imprimante réseau
 *
 * @category CompteRendu
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

$printer_id = CValue::get("printer_id");
$file_id    = CValue::get("file_id");

$file = new CFile();
$file->load($file_id);

$printer = new CPrinter();
$printer->load($printer_id);

$printer->loadRefSource()->sendDocument($file);
