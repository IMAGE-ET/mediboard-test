<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link       http://www.mediboard.org
 */

/**
 * Class CSQLExpression
 */
class CSQLExpression {

  /**
   * The value of the expression.
   *
   * @var mixed
   */
  protected $value;

  /**
   * Create a new raw query expression.
   *
   * @param  mixed  $value
   * @return void
   */
  public function __construct($value)
  {
    $this->value = $value;
  }

  /**
   * Get the value of the expression.
   *
   * @return mixed
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Get the value of the expression.
   *
   * @return string
   */
  public function __toString()
  {
    return (string) $this->getValue();
  }

}