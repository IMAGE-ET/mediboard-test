<?php

/**
 * $Id: DeployOperation.class.php 21950 2014-02-06 15:42:21Z phenxdesign $
 *
 * @category CLI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 21950 $
 * @link     http://www.mediboard.org
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Process\Exception\InvalidArgumentException;

use SVNClient\WorkingCopy;

/**
 * deploy:mep command
 */
abstract class DeployOperation extends MediboardCommand {
  protected $patterns_to_check = array(
    ".*/setup\.php"
  );

  /**
   * Display header information
   *
   * @param OutputInterface $output Output
   *
   * @return mixed
   */
  abstract protected function showHeader(OutputInterface $output);

  /**
   * Test to apply in order to determine if update will be performed
   *
   * @param string $master_branch Current MASTER release
   * @param string $release_code  Instance release
   *
   * @return mixed
   */
  abstract protected function testBranch($master_branch, $release_code);

  /**
   * @see parent::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    throw new Exception(__METHOD__ . " must be redefined");
  }

  /**
   * Get MASTER branch
   *
   * @param string          $path   Current root path
   * @param OutputInterface $output Output
   *
   * @return string|bool
   */
  protected function getMasterBranch($path, OutputInterface $output) {
    $wc  = new WorkingCopy($path);
    $url = $wc->getURL();

    // Find the current branch name
    $current_branch = "trunk";

    $matches = array();
    if (preg_match("@/branches/(.*)@", $url, $matches)) {
      $current_branch = $matches[1];
    }

    $this->out($output, "Current MASTER branch: '<b>$current_branch</b>'");

    if ($current_branch == "trunk") {
      $this->out($output, "<error>Cannot perform operation: MASTER branch is TRUNK.</error>");

      return false;
    }

    return $current_branch;
  }

  /**
   * Select instances
   *
   * @param string          $path   Current root path
   * @param OutputInterface $output Output
   *
   * @return array
   */
  protected function promptInstances($path, OutputInterface $output) {
    $dialog = $this->getHelperSet()->get('dialog');

    $rsyncupdate_conf = "$path/cli/conf/rsyncupdate.xml";

    if (!is_readable($rsyncupdate_conf)) {
      throw new InvalidArgumentException("'$rsyncupdate_conf' is not readable");
    }

    $dom = new DOMDocument();
    $dom->load($rsyncupdate_conf);
    $xpath = new DOMXPath($dom);

    /** @var DOMNodeList $groups */
    $groups = $xpath->query("//group");

    $all_instances = array();
    foreach ($groups as $_group) {
      $group_name = $_group->getAttribute("name");

      if (!isset($all_instances[$group_name])) {
        $all_instances[$group_name] = array();
      }

      /** @var DOMNodeList $instance_nodes */
      $instance_nodes = $xpath->query("instance", $_group);

      foreach ($instance_nodes as $_instance) {
        $_path                        = $_instance->getAttribute("path");
        $all_instances[$group_name][] = $_path;
      }
    }

    $instances = array("[ALL]");
    foreach ($all_instances as $_group => $_instances) {
      $instances[] = "[$_group]";

      foreach ($_instances as $_instance) {
        $instances[] = "[$_group] => $_instance";
      }
    }

    $selected = $dialog->select(
      $output,
      'Select instance (or [group] in order to select all of it)',
      $instances,
      0,
      false,
      'Value "%s" is not valid',
      true // active l'option multiselect
    );

    $selected_values = array_map(function ($c) use ($instances) {
      return $instances[$c];
    }, $selected);

    $output->writeln('Selected: ' . implode(', ', $selected_values));

    $selected = array();
    foreach ($selected_values as $_selected) {
      if (preg_match("/\[([A-Za-z]+)\]$/", $_selected, $matches)) {

        // All instances
        if ($matches[1] == "ALL") {
          $all = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($all_instances)), false);

          return $all;
        }

        // All instances from given GROUP
        if (in_array($matches[1], array_keys($all_instances))) {
          foreach ($all_instances[$matches[1]] as $_instance) {
            $selected[] = $_instance;
          }
        }
      }
      else {
        // Single instance
        if (preg_match("/\[[A-Za-z]+\] =\> (.*)/", $_selected, $path)) {
          $selected[] = $path[1];
        }
      }
    }

    // Remove duplicate entries if GROUP and group instances are selected
    $selected = array_unique($selected);

    return $selected;
  }

  /**
   * Checks remote instances state
   *
   * @param string          $master_branch Current MASTER branch
   * @param array           $instances     Selected instances
   * @param OutputInterface $output        Output
   *
   * @return array
   */
  protected function checkBranches($master_branch, $instances, OutputInterface $output) {
    $to_perform = array();

    foreach ($instances as $_instance) {
      $release_file = "$_instance/release.xml";
      $release_file = explode(":", $release_file);

      $dom = new DOMDocument();

      // Local file
      if (count($release_file) == 1) {
        $release_file = $release_file[0];

        if (is_readable($release_file)) {
          $dom->load($release_file);
        }
        else {
          throw new InvalidArgumentException("'$release_file' is not readable");
        }
      }
      else {
        // Remote file
        $result = $this->getRemoteRelease($release_file[0], $release_file[1], $output);

        if ($result) {
          $dom->loadXML($result);
        }
        else {
          throw new InvalidArgumentException("$release_file[0]:$release_file[1] is empty");
        }
      }

      $root = $dom->documentElement;

      $release_code = $root->getAttribute("code");
      $to_perform[] = array(
        "path"         => $_instance,
        "release_code" => $release_code,
        "perform"      => $this->testBranch($master_branch, $release_code)
      );
    }

    return $to_perform;
  }

  /**
   * Checks particular files
   *
   * @param string          $result RSYNC command result
   * @param OutputInterface $output Output
   */
  protected function checkFilePresence($result, OutputInterface $output) {
    if (preg_match_all("#(" . implode("|", $this->patterns_to_check) . ")#m", $result, $matches)) {
      $this->out($output, "<comment>Particular files:</comment>");

      foreach ($matches[1] as $_file) {
        $output->writeln("- <b>$_file</b>");
      }
    }
  }

  /**
   * Performs rsync
   *
   * @param string          $path     Root path
   * @param array           $files    Files to include and files to exclude from RSYNC
   * @param string          $instance Instance to RSYNC
   * @param OutputInterface $output   Output
   * @param boolean         $dry_run  Dry run mode toggle
   *
   * @return array|bool
   */
  protected function rsync($path, $files, $instance, OutputInterface $output, $dry_run = false) {
    $msg = "";
    if ($dry_run) {
      $dry_run = "-n ";
      $msg     = "(DRY RUN)";
    }

    $cmd = "rsync -apgzC --out-format='%n%L' $dry_run" . escapeshellarg($path . "/") . " --delete " . escapeshellarg($instance) . " " . $files["excluded"] . " " . $files["included"];

    // Executes RSYNC
    $result = array();
    exec($cmd, $result, $state);

    if ($state !== 0) {
      $this->out($output, "<error>Error occurred during $instance RSYNC... $dry_run</error>");

      return false;
    }
    else {
      if (!$dry_run) {
        $this->out($output, "<b>$instance rsync-ed!</b>");
      }
    }

    // Log files RSYNC
    foreach ($files["included_logfiles"] as $_file) {
      $cmd = "rsync -azp --out-format='%n%L' $dry_run" . escapeshellarg($path . "/" . $_file["file"]) . " " . escapeshellarg($instance . $_file["dest"]);

      $log = array();
      exec($cmd, $log, $log_state);

      if ($log_state !== 0) {
        $this->out($output, "<error>Error occurred during log files RSYNC...</error>");

        return false;
      }

      $result = array_merge($result, $log);
    }

    return $result;
  }

  /**
   * Ask and validate operation by typing MASTER release_code
   *
   * @param string          $current_branch Current MASTER branch
   * @param DialogHelper    $dialog         Dialog helper
   * @param OutputInterface $output         Output
   */
  protected function confirmOperation($current_branch, DialogHelper $dialog, OutputInterface $output) {
    $bundle = $dialog->askAndValidate(
      $output,
      "\nConfirm operation by typing MASTER release code: ",
      function ($answer) use ($current_branch) {
        if ($current_branch !== trim($answer)) {
          throw new \RunTimeException("Wrong release code: $answer");
        }

        return $answer;
      }
    );
  }

  /**
   * Get excluded and included files
   *
   * @param string          $path   Root path
   * @param OutputInterface $output Output
   *
   * @return array
   */
  protected function getIncludedAndExcluded($path, OutputInterface $output) {
    $file = "$path/cli/conf/rsyncupdate_exclude.xml";

    if (!is_readable($file)) {
      throw new InvalidArgumentException("'$file' is not readable");
    }

    $dom = new DOMDocument();
    $dom->load($file);

    $xpath = new DOMXPath($dom);

    $files = array(
      "excluded"          => array(),
      "included"          => array(),
      "included_logfiles" => array()
    );

    /** @var DOMNodeList $excluded */
    $excluded = $xpath->query("//exclude");
    foreach ($excluded as $_excluded) {
      $files["excluded"][] = "--exclude=" . escapeshellarg($_excluded->nodeValue);
    }

    /** @var DOMNodeList $included */
    $included = $xpath->query("//include");
    foreach ($included as $_included) {
      if ($_included->hasAttribute("logfile") && $_included->getAttribute("logfile") == "1") {
        // Files included afterwards
        $files["included_logfiles"][] = array(
          "file" => $_included->nodeValue,
          "dest" => $_included->getAttribute("dest")
        );
      }
      else {
        // Standard included files
        $files["included"][] = "--include=" . escapeshellarg($_included->nodeValue);
      }
    }

    $files["excluded"] = implode(" ", $files["excluded"]);
    $files["included"] = implode(" ", $files["included"]);

    return $files;
  }

  /**
   * Get remote release code
   *
   * @param string $host Hostname
   * @param string $path Path to the release file
   *
   * @return string|bool
   */
  protected function getRemoteRelease($host, $path, OutputInterface $output) {
    $cmd = "ssh " . escapeshellarg($host) . " cat " . escapeshellarg($path);

    $result = array();
    exec($cmd, $result, $state);

    if ($state !== 0) {
      $this->out($output, "<error>Error occurred during $cmd...</error>");

      return false;
    }

    return implode("\n", $result);
  }

  /**
   * External libraries installer
   *
   * @param OutputInterface $output Output
   */
  protected function installLibraries(OutputInterface $output) {
    require "install/cli/bootstrap.php";

    foreach (CLibrary::$all as $library) {
      if ($library->isInstalled() && $library->getUpdateState()) {
        continue;
      }

      $library->clearLibraries($library->name);

      $this->out($output, "Installation: <b>'$library->name'</b>...");

      if ($nbFiles = $library->install()) {
        $this->out($output, " > <comment>$nbFiles</comment> extracted files");
      }
      else {
        $this->out($output, "<error> > Error, $library->nbFiles found files</error>");
      }

      $output->write(strftime("[%Y-%m-%d %H:%M:%S]") . " -  > Moving: ");

      if ($library->apply()) {
        $output->writeln("<info>OK</info>");
      }
      else {
        $output->writeln("<error>Error!</error>");
      }

      if (count($library->patches)) {
        $this->out($output, " > Applying patches:");

        foreach ($library->patches as $patch) {
          $output->write(strftime("[%Y-%m-%d %H:%M:%S]") . " -  > Patch <comment>'$patch->sourceName'</comment> in <comment>'$patch->targetDir'</comment>: ");

          if ($patch->apply()) {
            $output->writeln("<info>Patch applied successfully</info>");
          }
          else {
            $output->writeln("<error>Error!</error>");
          }
        }
      }
    }
  }
}
