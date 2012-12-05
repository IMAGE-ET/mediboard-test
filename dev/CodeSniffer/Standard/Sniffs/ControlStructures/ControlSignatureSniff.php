<?php
/**
 * Verifies that control statements conform to their coding standards.
 *
 * PHP version 5.1
 *
 * @category PHP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id: $ 
 * @link     http://www.mediboard.org
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * Verifies that control statements conform to their coding standards.
 *
 * @category PHP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id: $ 
 * @link     http://www.mediboard.org
 */
class Standard_Sniffs_ControlStructures_ControlSignatureSniff 
  extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{
  /**
   * Constructs a Standard_Sniffs_ControlStructures_ControlSignatureSniff.
   */
  public function __construct() {
    parent::__construct(true);
  }

  /**
   * Returns the patterns that this test wishes to verify.
   *
   * @return array(string)
   */
  protected function getPatterns() {
	  return array(
      'do {EOL...} while (...);EOL',
      'while (...) {EOL',
      'for (...) {EOL',
      'if (...) {EOL',
      'switch (...) {EOL',
      'foreach (...) {EOL',
      '}EOLelse if (...) {EOL',
      '}EOLelseif (...) {EOL',
      '}EOLelse {EOL',
      'do {EOL',
    );
  }
}

?>
