<?php

/**
 * $Id$
 *  
 * @category DMP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Description
 */
class CXDSSubmissionLot extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $cxds_submissionlot_id;
  public $title;
  public $comments;
  public $date;
  public $type;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "cxds_submissionlot";
    $spec->key    = "cxds_submissionlot_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    $backProps["submissionset_document"] = "CXDSSubmissionLotToDocument submissionlot_id";

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["title"]    = "str";
    $props["comments"] = "str";
    $props["date"]     = "dateTime";
    $props["type"]     = "str notNull";

    return $props;
  }
}