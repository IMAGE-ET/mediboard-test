<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimSanteRecordPayment
 * Record payment, message XML
 */
class CHPrimSanteRecordFiles extends CHPrimSanteMessageXML {
  /**
   * @see parent::getContentNodes
   */
  function getContentNodes() {
    $data = array();

    $this->queryNodes("//P"  , null, $data, true); // get ALL the P segments
    $this->queryNodes("//OBX", null, $data, true); // get ALL the OBX segments

    return $data;
  }

  /**
   * @see parent::handle
   */
  function handle($ack, CMbObject $object, $data) {
    //@todo récupération document
  }
}