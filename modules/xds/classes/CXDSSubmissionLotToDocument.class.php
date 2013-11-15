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
class CXDSSubmissionLotToDocument extends CMbMetaObject {
  /**
   * @var integer Primary key
   */
  public $cxds_submissionlot_document_id;
  public $submissionlot_id;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "cxds_submissionlot_document";
    $spec->key    = "cxds_submissionlot_document_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["submissionlot_id"] = "ref class|CXDSSubmissionLot";
    $props["object_class"]     = "enum list|CCompteRendu|CFile notNull";

    return $props;
  }
}
