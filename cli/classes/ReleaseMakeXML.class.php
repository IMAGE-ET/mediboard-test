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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\InvalidArgumentException;

use SVNClient\WorkingCopy;

/**
 * release:makexml / release:mx command
 */
class ReleaseMakeXML extends MediboardCommand {
  /**
   * @see parent::configure()
   */
  protected function configure() {
    $aliases = array(
      'release:mx'
    );

    $this
      ->setName('release:makexml')
      ->setAliases($aliases)
      ->setDescription('Make release XML file')
      ->setHelp('Makes a release.xml file containing release information')
      ->addOption(
        'other',
        'o',
        InputOption::VALUE_OPTIONAL,
        'Global pattern for which we need a release.xml file'
      )
      ->addOption(
        'path',
        'p',
        InputOption::VALUE_OPTIONAL,
        'Working copy root for which we want to build release.xml',
        realpath(__DIR__."/../../")
      );
  }

  /**
   * @param WorkingCopy $wc
   * @param string      $path
   * @param string      $current_branch
   *
   * @return DOMDocument
   */
  public function getReleaseXML($wc, $path, $current_branch) {
    /*$release_log_file = "$path/release-log.xml";
    if (file_exists($release_log_file)) {
      $log = file_get_contents($release_log_file);
    }
    else {
      $log = $wc->log($path, 1000, true);
    }*/
    $log = $wc->log($path, 1000, true);

    $dom_log = new DOMDocument();
    $dom_log->loadXML($log);
    $xpath_log = new DOMXPath($dom_log);

    /** @var DOMElement $last_log_lement */
    $last_log_lement = $xpath_log->query("(/log/logentry)[last()]")->item(0);

    $release_date = $xpath_log->query("date", $last_log_lement)->item(0)->nodeValue;
    $release_rev = $last_log_lement->getAttribute("revision");

    // release.xml file
    $release_element = new DOMElement("release");

    $dom_release = new DOMDocument();
    $dom_release->appendChild($release_element);

    $release_element->setAttribute("code",     $current_branch);
    $release_element->setAttribute("date",     $release_date);
    $release_element->setAttribute("revision", $release_rev);

    return $dom_release;
  }

  /**
   * @see parent::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $path = $input->getOption('path');

    if (!is_dir($path)) {
      throw new InvalidArgumentException("'$path' is not a valid directory");
    }

    $wc = new WorkingCopy($path);
    $url = $wc->getURL();

    // Find the current branch name
    $current_branch = "trunk";

    $matches = array();
    if (preg_match("@/branches/(.*)@", $url, $matches)) {
      $current_branch = $matches[1];
    }

    $this->out($output, "Current branch: '<b>$current_branch</b>'");

    // Make GPL release.xml
    $dom_release = $this->getReleaseXML($wc, $path, $current_branch);

    file_put_contents("$path/release.xml", $dom_release->saveXML());
    $this->out($output, "release.xml file written in: '<b>$path/release.xml</b>'");

    $wc->add(array("$path/release.xml"));
    $this->out($output, "'<b>$path/release.xml</b>' added to version control");

    $other = $input->getOption("other");

    if ($other) {
      $base_path = dirname($other);

      $add_files = array();

      $other_wc = new WorkingCopy($base_path);
      $other_url = $other_wc->getURL();

      // Find the current branch name
      $other_branch = "trunk";

      $matches = array();
      if (preg_match("@/branches/(.*)/@", $other_url, $matches)) {
        $other_branch = $matches[1];
      }

      if ($other_branch != $current_branch) {
        $this->out($output, "<error>WARNING: current branch ($current_branch) is not the same as other branch ($other_branch)</error>");
        return;
      }

      $list = glob($other);

      foreach ($list as $_path) {
        $_dom_release = $this->getReleaseXML($other_wc, $_path, $other_branch);
        file_put_contents("$_path/release.xml", $_dom_release->saveXML());

        $add_files[] = "$_path/release.xml";

        $this->out($output, "release.xml file written in: '<b>$_path/release.xml</b>'");
      }

      $other_wc->add($add_files);
      $this->out($output, count($add_files)." files added to version control, <b>ready to commit</b> !");
    }
  }
}
