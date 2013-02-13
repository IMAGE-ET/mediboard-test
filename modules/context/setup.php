<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage dicom
 * @version $Revision$
 * @author SARL OpenXtrem
 */

/**
 * The setup for the context module
 */
class CSetupcontext extends CSetup {

  /**
   * Constructor
   */
  function __construct() {
    parent::__construct();

    $this->mod_name = "context";
    $this->makeRevision("all");

    $this->mod_version = "0.1";
  }
}