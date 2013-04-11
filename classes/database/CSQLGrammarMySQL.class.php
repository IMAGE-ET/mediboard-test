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
 * Class CSQLGrammarMySQL
 */
class CSQLGrammarMySQL extends CSQLGrammar {

  /**
   * The keyword identifier wrapper format.
   *
   * @var string
   */
  protected $wrapper = '`%s`';

}