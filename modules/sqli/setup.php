<?php

/**
 * Module Setup
 *
 * @category Sqli
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */

/**
 * The setup for the SQLI module
 */
class CSetupsqli extends CSetup {

  /**
   * Constructor
   */
  function __construct() {
    parent::__construct();

    $this->mod_name = "sqli";
    $this->makeRevision("all");

    $this->mod_version = "0.1";
  }
}