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
 * hprimsante error
 */
class CHPrimSanteError {

  public $type_error;
  public $code_error;
  public $address;
  public $field;
  public $type;
  public $sous_type;
  public $exchange;
  public $comment;

  /**
   * Constructor
   *
   * @param CExchangeHprimSante $exchange   Exchange
   * @param String              $type_error Error type
   * @param String              $code_error Error code
   * @param String[]            $address    Error address
   * @param String              $field      Error field
   * @param String              $comment    Comment
   */
  function CHPrimSanteError($exchange, $type_error, $code_error, $address, $field, $comment = null) {
    $this->type_error = $type_error;
    $this->code_error = $code_error;
    $this->address    = $address;
    $this->field      = $field;
    $this->type       = $exchange->type;
    $this->sous_type  = $exchange->sous_type;
    $this->exchange   = $exchange;
    $this->comment    = $comment;
  }

  /**
   * get the comment error
   *
   * @return string
   */
  function getCommentError() {
    $comment_error = CAppUI::tr("CHPrimSanteEvent-$this->sous_type-$this->type-$this->code_error");
    if ($this->comment) {
      $comment_error .= " : $this->comment";
    }
    $comment_error = str_replace("\r", "", $comment_error);
    return $comment_error;
  }
}