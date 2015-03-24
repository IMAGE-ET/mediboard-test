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

use SVNClient\WorkingCopy;

/**
 * deploy:mep command
 */
abstract class DeployOperation extends MediboardCommand {
  protected $start_time;

  protected $patterns_to_check = array(
    ".*/setup\.php"
  );

  /** @var OutputInterface */
  protected $output;

  /** @var InputInterface */
  protected $input;

  /** @var DialogHelper */
  protected $dialog;

  protected $ignore_externals;
  protected $update;
  protected $path;

  protected $rsyncupdate_conf;

  protected $all_instances = array();
  protected $instance_ids;
  protected $instances_to_update;
  protected $instances_to_perform;

  protected $performed_instances = array();
  protected $skipped_instances = array();

  /** @var DOMDocument */
  protected $rsyncupdate_dom;

  /** @var DOMXpath */
  protected $rsyncupdate_xpath;

  /** @var WorkingCopy */
  protected $wc;

  protected $master_branch;
  protected $elapsed_time;

  /**
   * Display header information
   *
   * @param OutputInterface $output Output
   *
   * @return mixed
   */
  abstract protected function showHeader();

  /**
   * Test to apply in order to determine if update will be performed
   *
   * @param string $release_code Instance release
   *
   * @return mixed
   */
  abstract protected function testBranch($release_code);

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
  protected function getMasterBranch() {
    $this->wc = new WorkingCopy($this->path);
    $url      = $this->wc->getURL();

    // Find the current branch name
    $current_branch = "trunk";

    $matches = array();
    if (preg_match("@/branches/(.*)@", $url, $matches)) {
      $current_branch = $matches[1];
    }

    $this->master_branch = $current_branch;

    $this->out($this->output, "Current MASTER branch: '<b>$this->master_branch</b>'");

    if ($this->master_branch == "trunk") {
      $this->out($this->output, "<error>Cannot perform operation: MASTER branch is TRUNK.</error>");

      return false;
    }

    return $this->master_branch;
  }

  /**
   * Select instances
   *
   * @return array
   */
  protected function promptInstances() {
    $this->rsyncupdate_conf = "$this->path/cli/conf/deploy.xml";

    if (!is_readable($this->rsyncupdate_conf)) {
      throw new Exception("$this->rsyncupdate_conf is not readable.");
    }

    $dom = new DOMDocument();
    $dom->load($this->rsyncupdate_conf);
    $xpath = new DOMXPath($dom);

    /** @var DOMNodeList $groups */
    $groups = $xpath->query("//group");

    $all_instances = array();
    /** @var DOMElement $_group */
    foreach ($groups as $_group) {
      $group_name = $_group->getAttribute("name");

      if (!isset($all_instances[$group_name])) {
        $all_instances[$group_name] = array();
      }

      /** @var DOMNodeList $instance_nodes */
      $instance_nodes = $xpath->query("instance", $_group);

      /** @var DOMElement $_instance */
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

    $selected = $this->dialog->select(
      $this->output,
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

    $this->output->writeln('Selected: ' . implode(', ', $selected_values));

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
   * @param array $instances Selected instances
   *
   * @return array
   */
  protected function checkBranches($instances) {
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
          throw new Exception("$release_file is not readable.");
        }
      }
      else {
        // Remote file
        $result = $this->getRemoteRelease($release_file[0], $release_file[1]);

        if ($result) {
          if (!$dom->loadXML($result)) {
            throw new Exception('Cannot parse XML.');
          }
        }
        else {
          throw new Exception("$release_file[0]:$release_file[1] is empty.");
        }
      }

      $root = $dom->documentElement;

      $release_code = $root->getAttribute("code");
      $to_perform[] = array(
        "path"         => $_instance,
        "release_code" => $release_code,
        "perform"      => $this->testBranch($release_code)
      );
    }

    return $to_perform;
  }

  /**
   * Checks particular files
   *
   * @param string $result RSYNC command result
   */
  protected function checkFilePresence($result) {
    if (preg_match_all("#(" . implode("|", $this->patterns_to_check) . ")#m", $result, $matches)) {
      $this->out($this->output, "<comment>Particular files:</comment>");

      foreach ($matches[1] as $_file) {
        $this->output->writeln("- <fg=red;>$_file</fg=red;>");
      }
    }
  }

  /**
   * Performs RSYNC
   *
   * @param array   $files    Files to include and files to exclude from RSYNC
   * @param string  $instance Instance to RSYNC
   * @param boolean $dry_run  Dry run mode toggle
   *
   * @return array|bool
   */
  protected function rsync($files, $instance, $dry_run = false, &$merged_result = false) {
    $msg = "";
    if ($dry_run) {
      $dry_run = "-n ";
      $msg     = "(DRY RUN)";
    }

    $os = $this->getOSVersion();
    $os = explode(" ", $os[0]);
    $os = $os[0];

    $log_cmd = "--out-format";
    // Old CentOS 5.6 tweak
    if ($os == "CentOS") {
      $log_cmd = "--log-format";
    }

    $cmd = "rsync -apgzC $log_cmd='%n%L' $dry_run" . escapeshellarg($this->path . "/") . " --delete " . escapeshellarg($instance) . " " . $files["excluded"] . " " . $files["included"];

    // Executes RSYNC
    $result = array();
    exec($cmd, $result, $state);

    if ($state !== 0) {
      $this->out($this->output, "<error>Error occurred during $instance RSYNC... $msg</error>");

      return false;
    }
    else {
      if (!$dry_run) {
        $this->out($this->output, "<info>RSYNC-ED: $instance</info>");
      }
    }

    // Log files RSYNC
    foreach ($files["included_logfiles"] as $_file) {
      $cmd = "rsync -azp --out-format='%n%L' $dry_run" . escapeshellarg($this->path . "/" . $_file["file"]) . " " . escapeshellarg($instance . $_file["dest"]);

      $log = array();
      exec($cmd, $log, $log_state);

      if ($log_state !== 0) {
        $this->out($this->output, "<error>Error occurred during log files RSYNC...</error>");

        return false;
      }

      $result = array_merge($result, $log);
    }

    return $result;
  }

  /**
   * Ask and validate operation by typing MASTER release_code
   */
  protected function confirmOperation() {
    $that = $this;

    $bundle = $this->dialog->askAndValidate(
      $this->output,
      "\nConfirm operation by typing MASTER release code: ",
      function ($answer) use ($that) {
        if ($that->master_branch !== trim($answer)) {
          throw new \RunTimeException("Wrong release code: $answer");
        }

        return $answer;
      }
    );
  }

  /**
   * Get excluded and included files
   *
   * @return array
   * @throws Exception
   */
  protected function getIncludedAndExcluded() {
    $file = "$this->path/cli/conf/exclude.xml";

    if (!is_readable($file)) {
      throw new Exception("$file is not readable.");
    }

    $dom = new DOMDocument();
    if (!$dom->load($file)) {
      throw new Exception("Cannot parse $file DOMDocument.");
    }

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
    /** @var DOMElement $_included */
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
   * @return array
   */
  protected function getExcluded() {
    $file = "$this->path/cli/conf/exclude.xml";

    if (!is_readable($file)) {
      throw new Exception("$file is not readable.");
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
   * @param string $path Instance path
   *
   * @return string|bool
   */
  protected function getRemoteRelease($host, $path) {
    $cmd = "ssh " . escapeshellarg($host) . " cat " . escapeshellarg($path);

    $result = array();
    exec($cmd, $result, $state);

    if ($state !== 0) {
      $this->out($this->output, "<error>Error occurred during $cmd...</error>");

      return false;
    }

    return implode("\n", $result);
  }

  /**
   * External libraries installer
   */
  protected function installLibraries() {
    require "install/cli/bootstrap.php";

    foreach (CLibrary::$all as $library) {
      if ($library->isInstalled() && $library->getUpdateState()) {
        continue;
      }

      $library->clearLibraries($library->name);

      $this->out($this->output, "Installation: <b>'$library->name'</b>...");

      if ($nbFiles = $library->install()) {
        $this->out($this->output, " > <comment>$nbFiles</comment> extracted files");
      }
      else {
        $this->out($this->output, "<error> > Error, $library->nbFiles found files</error>");
      }

      $this->output->write(strftime("[%Y-%m-%d %H:%M:%S]") . " -  > Moving: ");

      if ($library->apply()) {
        $this->output->writeln("<info>OK</info>");
      }
      else {
        $this->output->writeln("<error>Error!</error>");
      }

      if (count($library->patches)) {
        $this->out($this->output, " > Applying patches:");

        foreach ($library->patches as $patch) {
          $this->output->write(strftime("[%Y-%m-%d %H:%M:%S]") . " -  > Patch <comment>'$patch->sourceName'</comment> in <comment>'$patch->targetDir'</comment>: ");

          if ($patch->apply()) {
            $this->output->writeln("<info>Patch applied successfully</info>");
          }
          else {
            $this->output->writeln("<error>Error!</error>");
          }
        }
      }
    }
  }

  /**
   * RSYNC file diff table output
   *
   * @param array $instances List of all treated instances, for headers initialisation
   * @param array $files     List of all treated files as keys, with all concerned instances as values ([file] => (instance, instance))
   */
  protected function showFileDiffTable($instances, $files) {
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
      $this->out($this->output, "<info>Added or modified files:</info>");
      $table = $this->getHelperSet()->get('table');
      $table
        ->setHeaders($headers)
        ->setRows($rows)
        ->setCellHeaderFormat('<b>%s</b>')
        ->setCellRowFormat('%s');
      $table->render($this->output);
    }
    else {
      $this->out($this->output, "<comment>No added or modified files</comment>");
    }

    if ($rows_deleted_files) {
      $this->out($this->output, "<fg=red;>Deleted files:</fg=red;>");

      $table = $this->getHelperSet()->get('table');
      $table
        ->setHeaders($headers)
        ->setRows($rows_deleted_files)
        ->setCellHeaderFormat('<b>%s</b>')
        ->setCellRowFormat('<fg=red;>%s</fg=red;>');
      $table->render($this->output);
    }
    else {
      $this->out($this->output, "<comment>No deleted files</comment>");
    }

    if ($particular_files) {
      $this->out($this->output, "<comment>Particular files:</comment>");

      $table = $this->getHelperSet()->get('table');
      $table
        ->setHeaders($headers)
        ->setRows($particular_files)
        ->setCellHeaderFormat('<b>%s</b>')
        ->setCellRowFormat('<fg=red;>%s</fg=red;>');
      $table->render($this->output);
    }
  }

  /**
   * Get locally modified files from SVN status XML output
   *
   * @param string $xml XML output
   *
   * @return array
   * @throws Exception
   */
  protected function getModifiedFilesByXML($xml) {
    $modified_files = array();

    $dom = new DOMDocument();
    if (!$dom->loadXML($xml)) {
      throw new Exception("Cannot parse XML.");
    }

    $files_to_exclude = $this->getExcluded();

    $xpath = new DOMXPath($dom);

    // Get all 'entry' nodes whom 'wc-status' child node has 'item' attribute different from 'normal' and from 'external'
    $nodes = $xpath->query("//entry[wc-status[@item != 'normal' and @item != 'external']]");

    if ($nodes) {
      /** @var DOMElement $_node */
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
   * @throws Exception
   */
  protected function update() {
    $wc = new WorkingCopy($this->path);

    $this->out($this->output, "Checking SVN status...");
    $out = $wc->status(".", $this->ignore_externals);

    if ($out) {
      $this->out($this->output, "<info>SVN status checked</info>");

      $modified_files = $this->getModifiedFilesByXML($out);

      if ($modified_files) {
        $this->out($this->output, "<comment>These files are modified locally:</comment>");

        foreach ($modified_files as $_file) {
          $this->output->writeln("- <fg=red;>$_file</fg=red;>");
        }

        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation(
          $this->output,
          '<question>In order to update, you need to revert these files, otherwise update will be skipped.
Revert? [Y/n]</question> ',
          true
        )
        ) {
          return false;
        }

        // Files have to be reverted
        $this->out($this->output, "Reverting files...");
        // /!\ Long list may handle an exception
        $wc->revert($modified_files);
        $this->out($this->output, "<info>Files reverted</info>");
      }
    }

    // SVN update
    $this->out($this->output, "SVN update in progress...");
    $wc->update(array(), "HEAD");
    $this->out($this->output, "<info>SVN update completed</info>\n");

    // SVN status file writing
    $this->writeSVNStatusFile();
  }

  /**
   * Write status files
   *
   * @param WorkingCopy     $wc     Working copy
   * @param OutputInterface $output Output
   *
   * @throws Exception
   */
  protected function writeSVNStatusFile() {
    $status = "$this->path/tmp/svnstatus.txt";
    $event  = "$this->path/tmp/monitevent.txt";

    $this->out($this->output, "Checking SVN info...");
    $out = $this->wc->info();

    if ($out) {
      $revision = $this->getRevisionByXML($out);

      if (!$revision) {
        $this->out($this->output, "<error>Unable to check revision!</error>");

        return;
      }

      if (!is_readable($status)) {
        $this->out($this->output, "<error>'$status' is not readable</error>");
      }
      else {
        $status_file = fopen($status, "w");
        fwrite($status_file, "R�vision : $revision\n");
        fwrite($status_file, "Date: " . strftime("%Y-%m-%dT%H:%M:%S") . "\n");

        if (fclose($status_file)) {
          $this->out($this->output, "<info>'$status' updated</info>");
        }
        else {
          $this->out($this->output, "<error>Unable to write '$status'</error>");
        }
      }

      if (!is_readable($event)) {
        $this->out($this->output, "<error>'$event' is not readable</error>");
      }
      else {
        $event_file = fopen($event, "a+");
        fwrite($event_file, "#" . strftime("%Y-%m-%dT%H:%M:%S") . "\n");
        fwrite($event_file, "Mise a jour. R�vision : $revision\n");


        if (fclose($event_file)) {
          $this->out($this->output, "<info>'$event' updated</info>");
        }
        else {
          $this->out($this->output, "<error>Unable to write '$event'</error>");
        }
      }
    }
  }

  /**
   * Get OS version
   *
   * @return array|bool
   */
  protected function getOSVersion() {
    $result = array();
    exec("cat /etc/issue", $result, $state);

    if ($state !== 0) {
      $this->out($this->output, "<error>Unable to check OS version</error>");

      return false;
    }

    return $result;
  }

  protected function acquire() {
    $this->out($this->output, "Lock acquisition...");
    $lock_key = 'auto-update';
    $this->initLockFile($this->path, $lock_key);

    return $this->acquireLockFile();
  }

  protected function release() {
    $this->out($this->output, "Lock releasing...");

    $this->elapsed_time = (microtime(true) - $this->start_time);

    return $this->releaseLockFile();
  }

  /**
   * Performs an SVN cleanup
   */
  protected function doSVNCleanup() {
    $this->out($this->output, "Performing SVN cleanup...");

    $this->wc->cleanup($this->path);

    $this->out($this->output, "SVN cleanup performed");
  }
}
