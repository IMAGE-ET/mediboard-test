<?php 

/**
 * AED translation overwrite
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

$language = CValue::post("language", "fr");

$do = new CDoObjectAddEdit("CTranslationOverwrite", "translation_id");
$do->doIt();

SHM::rem("locales-$language");