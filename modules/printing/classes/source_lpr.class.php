<?php

/**
 * Source LPR PRINTING
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSourceLPR 
 * Source LPR
 */

CAppUI::requireModuleClass("printing", "source_printer");

class CSourceLPR extends CSourcePrinter {
  // DB Table key
  var $source_lpr_id = null;
  
  // DB Fields
  var $user         = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'source_lpr';
    $spec->key   = 'source_lpr_id';
    return $spec;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs["user"]         = "str";
    return $specs;
  }
  
  function sendDocument($file) {
    $lpr = new CLPR;
    $lpr->init($this);
    $lpr->printFile($file);
  }
}
?>