<?php

/**
 * $Id$
 *  
 * @category CLI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mediboard command executer
 */
class MediboardCommand extends Command {
  /**
   * @see parent::initialize()
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    $style = new OutputFormatterStyle('blue', null, array('bold'));
    $output->getFormatter()->setStyle('b', $style);

    $style = new OutputFormatterStyle(null, 'red', array('bold'));
    $output->getFormatter()->setStyle('error', $style);
  }

  /**
   * Output timed text
   *
   * @param OutputInterface $output Output interface
   * @param string          $text   Text to print
   *
   * @return void
   */
  protected function out(OutputInterface $output, $text) {
    $output->writeln(strftime("[%Y-%m-%d %H:%M:%S]")." - $text");
  }
}
