<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

// Appel de la fonction d'extraction du RPUSender
$rpu_sender = CExtractPassages::getRPUSender();
if (!$rpu_sender) {
  CAppUI::stepAjax("Aucun sender définit dans le module dPurgences.", UI_MSG_ERROR);
}

mbTrace($rpu_sender->showEncryptKey());