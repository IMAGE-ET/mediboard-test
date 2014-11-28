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

use Symfony\Component\Process\Process;

/**
 * deploy:mep command
 */
abstract class DeployOperation extends MediboardCommand {
  protected $all_instances = array();

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

    $rsyncupdate_conf = "$path/cli/conf/deploy.xml";

    if (!is_readable($rsyncupdate_conf)) {
      throw new Exception("'$rsyncupdate_conf' is not readable");
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

        $_shortname                  = $_instance->getAttribute("shortname");
        $this->all_instances[$_path] = $_shortname;
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
          throw new Exception("'$release_file' is not readable");
        }
      }
      else {
        // Remote file
        $result = $this->getRemoteRelease($release_file[0], $release_file[1], $output);

        if ($result) {
          $dom->loadXML($result);
        }
        else {
          throw new Exception("$release_file[0]:$release_file[1] is empty");
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
        $output->writeln("- <fg=red;>$_file</fg=red;>");
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
  protected function rsync($path, $files, $instance, OutputInterface $output, $dry_run = false, &$merged_result = false) {
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
      $this->out($output, "<error>Error occurred during $instance RSYNC... $msg</error>");

      return false;
    }
    else {
      if (!$dry_run) {
        $this->out($output, "<info>RSYNC-ED: $instance</info>");
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
   * @param string $path Root path
   *
   * @return array
   * @throws Exception
   */
  protected function getIncludedAndExcluded($path) {
    $file = "$path/cli/conf/exclude.xml";

    if (!is_readable($file)) {
      throw new Exception("'$file' is not readable");
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
   * Get excluded files
   *
   * @param string          $path   Root path
   * @param OutputInterface $output Output
   *
   * @return array
   */
  protected function getExcluded($path, OutputInterface $output) {
    $file = "$path/cli/conf/exclude.xml";

    if (!is_readable($file)) {
      throw new Exception("'$file' is not readable");
    }

    $dom = new DOMDocument();
    $dom->load($file);

    $xpath = new DOMXPath($dom);

    $files = array();

    /** @var DOMNodeList $excluded */
    $excluded = $xpath->query("//exclude");
    foreach ($excluded as $_excluded) {
      $files[] = $_excluded->nodeValue;
    }

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

  /**
   * RSYNC file diff table output
   *
   * @param array           $instances List of all treated instances, for headers initialisation
   * @param array           $files     List of all treated files as keys, with all concerned instances as values ([file] => (instance, instance))
   * @param OutputInterface $output    Output
   */
  protected function showFileDiffTable($instances, $files, OutputInterface $output) {
    // Headers initialisation
    $headers = array(str_pad("File", 50));
    foreach ($instances as $_instance) {
      $headers[] = $this->all_instances[$_instance];
    }

    /**
     * Rows initialisation
     *
     * For each file, init "file" column
     * Then, for each header, except file, init cell value
     * Finally, for each instance associated with file, set cell value, with array index
     **/
    $rows               = array();
    $rows_deleted_files = array();
    $particular_files   = array();
    foreach ($files as $_file => $_instances) {
      $_row = array("file" => $_file);

      foreach ($instances as $_header) {
        $_row[$_header] = "";

        foreach ($_instances as $_k => $_instance) {
          if ($_header == $_instance) {
            $_row[$_instance] = "X";
          }
        }
      }

      if (substr($_file, 0, 9) == "deleting ") {
        $_row["file"]               = substr($_row["file"], 9);
        $rows_deleted_files[$_file] = $_row;
      }
      else {
        $rows[$_file] = $_row;
      }

      if (preg_match("#(" . implode("|", $this->patterns_to_check) . ")#", $_file, $matches)) {
        $particular_files[$_file] = $_row;
      }
    }

    if ($rows) {
      $this->out($output, "<info>Added or modified files:</info>");
      $table = $this->getHelperSet()->get('table');
      $table
        ->setHeaders($headers)
        ->setRows($rows)
        ->setCellHeaderFormat('<b>%s</b>')
        ->setCellRowFormat('%s');
      $table->render($output);
    }
    else {
      $this->out($output, "<comment>No added or modified files</comment>");
    }

    if ($rows_deleted_files) {
      $this->out($output, "<fg=red;>Deleted files:</fg=red;>");

      $table = $this->getHelperSet()->get('table');
      $table
        ->setHeaders($headers)
        ->setRows($rows_deleted_files)
        ->setCellHeaderFormat('<b>%s</b>')
        ->setCellRowFormat('<fg=red;>%s</fg=red;>');
      $table->render($output);
    }
    else {
      $this->out($output, "<comment>No deleted files</comment>");
    }

    if ($particular_files) {
      $this->out($output, "<comment>Particular files:</comment>");

      $table = $this->getHelperSet()->get('table');
      $table
        ->setHeaders($headers)
        ->setRows($particular_files)
        ->setCellHeaderFormat('<b>%s</b>')
        ->setCellRowFormat('<fg=red;>%s</fg=red;>');
      $table->render($output);
    }
  }

  /**
   * Get locally modified files from SVN status XML output
   *
   * @param string          $xml    XML output
   * @param string          $path   Root
   * @param OutputInterface $output Output
   *
   * @return array
   * @throws Exception
   */
  protected function getModifiedFilesByXML($xml, $path, OutputInterface $output) {
    $modified_files = array();

    $dom = new DOMDocument();
    if (!$dom->loadXML($xml)) {
      throw new Exception("Cannot parse XML.");
    }

    $files_to_exclude = $this->getExcluded($path, $output);

    $xpath = new DOMXPath($dom);

    // Get all 'entry' nodes whom 'wc-status' child node has 'item' attribute different from 'normal' and from 'external'
    $nodes = $xpath->query("//entry[wc-status[@item != 'normal' and @item != 'external']]");

    if ($nodes) {
      foreach ($nodes as $_node) {
        $_path                  = $_node->getAttribute("path");
        $modified_files[$_path] = $_path;
      }

      // Unset specific configuration files which MUST NOT be reverted
      if (preg_match_all("#(" . implode("|", $files_to_exclude) . ")#m", implode("\n", $modified_files), $matches)) {
        foreach ($matches[1] as $_file) {
          unset($modified_files[$_file]);
        }
      }
    }

    return $modified_files;
  }

  /**
   * Get current revision from SVN info XML output
   *
   * @param string $xml XML output
   *
   * @return null
   * @throws Exception
   */
  protected function getRevisionByXML($xml) {
    $revision = null;

    $dom = new DOMDocument();
    if (!$dom->loadXML($xml)) {
      throw new Exception("Cannot parse XML.");
    }

    $xpath    = new DOMXPath($dom);
    $revision = $xpath->query("/info/entry/@revision");

    // Cause query returns a list
    foreach ($revision as $_revision) {
      if ($_revision->value) {
        return $revision = $_revision->value;
      }
    }

    return null;
  }

  /**
   * Performs an SVN update
   *
   * @param string          $path             Root
   * @param OutputInterface $output           Output
   * @param bool            $ignore_externals Does the externals have to been ignored?
   *
   * @throws Exception
   */
  protected function update($path, OutputInterface $output, $ignore_externals = false) {
    $wc = new WorkingCopy($path);

    $this->out($output, "Checking SVN status...");
    $out = $wc->status(".", $ignore_externals);

    if ($out) {
      $this->out($output, "<info>SVN status checked</info>");

      $modified_files = $this->getModifiedFilesByXML($out, $path, $output);

      if ($modified_files) {
        $this->out($output, "<comment>These files are modified locally:</comment>");

        foreach ($modified_files as $_file) {
          $output->writeln("- <fg=red;>$_file</fg=red;>");
        }

        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation(
          $output,
          '<question>Revert? [Y/n]</question> ',
          true
        )
        ) {
          return false;
        }

        // Files have to be reverted
        $this->out($output, "Reverting files...");
        // /!\ Long list may handle an exception
        $wc->revert($modified_files);
        $this->out($output, "<info>Files reverted</info>");
      }
    }

    // SVN update
    $this->out($output, "SVN update in progress...");
    $wc->update(array(), "HEAD", $ignore_externals);
    $this->out($output, "<info>SVN update completed</info>\n");

    // SVN status file writing
    $this->writeSVNStatusFile($wc, $output);
  }

  /**
   * Write status files
   *
   * @param WorkingCopy     $wc     Working copy
   * @param OutputInterface $output Output
   *
   * @throws Exception
   */
  protected function writeSVNStatusFile(WorkingCopy $wc, OutputInterface $output) {
    $path   = $wc->getPath();
    $status = "$path/tmp/svnstatus.txt";
    $event  = "$path/tmp/monitevent.txt";

    $this->out($output, "Checking SVN info...");
    $out = $wc->info();

    if ($out) {
      $revision = $this->getRevisionByXML($out);

      if (!$revision) {
        $this->out($output, "<error>Unable to check revision!</error>");

        return;
      }

      if (!is_readable($status)) {
        $this->out($output, "<error>'$status' is not readable</error>");
      }
      else {
        $status_file = fopen($status, "w");
        fwrite($status_file, "Révision : $revision\n");
        fwrite($status_file, "Date: " . strftime("%Y-%m-%dT%H:%M:%S") . "\n");

        if (fclose($status_file)) {
          $this->out($output, "<info>'$status' updated</info>");
        }
        else {
          $this->out($output, "<error>Unable to write '$status'</error>");
        }
      }

      if (!is_readable($event)) {
        $this->out($output, "<error>'$event' is not readable</error>");
      }
      else {
        $event_file = fopen($event, "a+");
        fwrite($event_file, "#" . strftime("%Y-%m-%dT%H:%M:%S") . "\n");
        fwrite($event_file, "Mise a jour. Révision : $revision\n");


        if (fclose($event_file)) {
          $this->out($output, "<info>'$event' updated</info>");
        }
        else {
          $this->out($output, "<error>Unable to write '$event'</error>");
        }
      }
    }
  }
}
