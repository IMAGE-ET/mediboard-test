<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

class CSetupdicom extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = "dicom";
    $this->makeRevision("all");
    
    $this->mod_version = "0.1";
  }
}
?>