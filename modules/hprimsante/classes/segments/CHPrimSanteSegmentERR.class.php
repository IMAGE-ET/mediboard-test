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
 * Class CHPrimSanteSegmentERR
 * ERR - Represents an HPR ERR message segment (Error)
 */

class CHPrimSanteSegmentERR extends CHPrimSanteSegment {
  public $name = "ERR";

  /** @var CHPrimSanteAcknowledgment */
  public $acknowledgment;

  /**
   * @see parent::build
   */
  function build(CHPrimSanteEvent $event) {
    parent::build($event);

    /** @var CHPrimSanteAcknowledgment $acknowledgment */
    $acknowledgment = $this->acknowledgment;
    /** @var CExchangeHprimSante $exchange_hpr */
    $exchange_hpr   = $event->_exchange_hpr;
    /** @var CHPrimSanteError $error */
    $error          = $acknowledgment->_error;
    list($segment, $rang, $identifier) = $error->address;

    $data = array();

    // ERR-1: Segment Row
    $data[] = $rang;

    // ERR-2: Filename
    $data[] = $exchange_hpr->nom_fichier;

    // ERR-3: Date / Time of receipt
    $data[] = $exchange_hpr->date_production;

    // ERR-4: Severity
    $data[] = $error->type_error;

    // ERR-5: Line number
    $data[] = null;

    // ERR-6: Error Location
    $data[] = array(array($segment, $rang, array_values($identifier)));

    // ERR-7: Field Position
    $data[] = $error->field;

    // ERR-8: Error value
    $data[] = null;

    // ERR-9: Error type
    $data[] = null;

    // ERR-10: Original Text
    $data[] = $error->getCommentError();


    $this->fill($data);
  }
}
