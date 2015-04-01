<?php

/**
 * $Id: DeployMaJAuto.class.php 21950 2014-02-06 15:42:21Z phenxdesign $
 *
 * @category CLI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 21950 $
 * @link     http://www.mediboard.org
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * deploy:maj command
 */
class DeployMaJAuto extends DeployMaj {
  protected $patterns_to_check = array(
    ".*/setup\.php|.*\.zip|.*\.gz"
  );

  protected $master_host;
  protected $username;
  protected $password;
  protected $master_name;
  protected $master_role;
  protected $server_id;

  protected $instances_not_allowed = array();

  protected $status;
  protected $report = '';

  const STATUS_ERROR = '0';
  const STATUS_OK = '1';
  const STATUS_WARNING = '2';

  /**
   * @see parent::configure()
   */
  protected function configure() {
    $this
      ->setName('deploy:autoupdate')
      ->setAliases(array('deploy:au'))
      ->setDescription('Synchronize MB with RSYNC')
      ->setHelp('Performs an RSYNC command from MB Master')
      ->addOption(
        'path',
        'p',
        InputOption::VALUE_OPTIONAL,
        'Working copy root',
        realpath(__DIR__ . "/../../")
      )
      ->addOption(
        'allow_trunk',
        't',
        InputOption::VALUE_NONE,
        'Allow TRUNK working copy for MASTER'
      );
  }

  /**
   * @see parent::showHeader()
   */
  protected function showHeader() {
    $this->start_time = microtime(true);
    $this->out($this->output, "Automatic update starting...");
  }

  /**
   * @see parent::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->output      = $output;
    $this->input       = $input;
    $this->path        = $input->getOption('path');
    $this->allow_trunk = $input->getOption('allow_trunk');

    $this->status = self::STATUS_OK;

    // Lock file acquisition
    if (!$this->acquire()) {
      $this->errorMsg('Instance is locked.', self::STATUS_ERROR, false);
    }

    $this->showHeader();

    if (!is_dir($this->path)) {
      $this->errorMsg("$this->path is not a valid directory", self::STATUS_ERROR, false);
    }

    if (!$this->getMasterBranch()) {
      $this->errorMsg('Exiting.', self::STATUS_ERROR, false);
    }

    $this->getConfig();
    $perm = json_decode($this->checkRemoteAuthorization(), true);
    $this->out($this->output, 'Response received.');

    /**
     * Return codes
     *
     * -1 = Invalid data
     *  0 = Not allowed to perform an update
     *  1 = Allowed to perform an update
     *  2 = Invalid instance
     *
     */
    if (!is_array($perm)) {
      $this->errorMsg('Invalid data.', self::STATUS_ERROR);
    }

    foreach ($perm as $_instance_id => $_perm) {
      switch ($_perm) {
        case 0:
          $this->out($this->output, "Instance #{$_instance_id} is not allowed to update. Skipping.");
          unset($perm[$_instance_id]);
          $this->instances_not_allowed[] = $_instance_id;
          break;

        case 1:
          $this->out($this->output, "Instance #{$_instance_id} is allowed to update.");
          break;

        case 2:
          $this->out($this->output, "Instance #{$_instance_id} is invalid. Skipping.");
          unset($perm[$_instance_id]);
          $this->status = self::STATUS_WARNING;
          break;

        default:
          $this->out($this->output, "Instance #{$_instance_id} - An error occured. Skipping.");
          unset($perm[$_instance_id]);
          $this->status = self::STATUS_WARNING;
      }
    }

    if (empty($perm)) {
      $this->errorMsg('No instance to udpdate.', self::STATUS_WARNING);
    }

    try {
      $this->doSVNCleanup();
      $this->doSVNRevert();
      $this->doSVNUpdate();
    }
    catch (Exception $e) {
      $this->errorMsg($e, self::STATUS_ERROR);
    }

    $instance_ids_to_update    = array_keys($perm);
    $this->instances_to_update = array();
    foreach ($this->all_instances as $_shortname => $_instance) {
      if (in_array($_instance['id'], $instance_ids_to_update)) {
        $this->instances_to_update[] = $_instance['path'];
      }
    }

    if (empty($this->instances_to_update)) {
      $this->errorMsg('No instance to update.', self::STATUS_WARNING);
    }

    $this->out($this->output, "Checking branches...");
    try {
      $instances_with_branch = $this->checkBranches($this->instances_to_update);
    }
    catch (Exception $e) {
      $this->errorMsg($e, self::STATUS_ERROR);
    }

    $this->instances_to_perform = array();
    $this->out($this->output, "Result:");
    foreach ($instances_with_branch as $_instance) {
      $perform = 'KO';
      if ($_instance['perform']) {
        $perform                      = 'OK';
        $this->instances_to_perform[] = $_instance['path'];
      }

      $this->out($this->output, "<$perform> " . $_instance["release_code"] . " - " . $_instance["path"]);
    }

    if (empty($this->instances_to_perform)) {
      $this->errorMsg('No instance to update.', self::STATUS_WARNING);
    }

    $this->out($this->output, 'Performing operation...');

    // External libraries installation
    $this->installLibraries();

    try {
      $files = $this->getIncludedAndExcluded();

      // Flag cache file creation
      $clear_cache_file = "{$this->path}/tmp/clear_cache.flag";
      touch($clear_cache_file);
      chmod($clear_cache_file, 0755);

      // Adding flag file to RSYNC in order to propage it
      $files[] = array(
        'file' => $clear_cache_file,
        'dest' => 'tmp'
      );

      $merged_result = array();
      foreach ($this->instances_to_perform as $_k => $_instance) {
        $this->out($this->output, "$_instance - Performing RSYNC in dry run...");

        // RSYNC with dry run
        $result = $this->rsync($files, $_instance, true, $merged_result);

        if ($this->checkFilePresence(implode("\n", $result))) {
          $this->out($this->output, "$_instance - Particular files detected. Skipping.");
          $this->status = self::STATUS_WARNING;
          unset($this->instances_to_perform[$_k]);
        }
        else {
          $this->out($this->output, "$_instance - Ready to RSYNC!");
        }
      }

      if (empty($this->instances_to_perform)) {
        $this->errorMsg('No instance to update.', self::STATUS_WARNING);
      }

      $ip_addresses = $this->getIPAddresses();
      foreach ($this->instances_to_perform as $_instance) {
        $this->out($this->output, "$_instance - Performing RSYNC...");

        parent::rsync($files, $_instance);

        $result = $this->clearCache($ip_addresses[$_instance] . '/' . basename($_instance));

        if ($result) {
          $msg = "$_instance - Cache cleared.";
        }
        else {
          $msg          = "$_instance - Unable to clear cache!";
          $this->status = self::STATUS_WARNING;
        }

        $this->out($this->output, $msg);
      }

      $this->release();
      $this->out($this->output, "Operation completed. Elapsed time: {$this->elapsed_time}");
      $this->sendReport();
    }
    catch (Exception $e) {
      $this->errorMsg($e, self::STATUS_ERROR);
    }
  }

  protected function out(OutputInterface $output, $text) {
    $this->report .= "\n" . strftime("[%Y-%m-%d %H:%M:%S]") . " - $text";
  }

  protected function errorMsg($msg, $status_code, $release_lock = true) {
    $this->status = $status_code;

    if ($release_lock) {
      $this->release($this->path, $this->output);
    }

    $this->out($this->output, $msg);
    $this->sendReport();
    exit(0);
  }

  protected function getConfig() {
    $this->rsyncupdate_conf = "$this->path/cli/conf/deploy.xml";

    if (!is_readable($this->rsyncupdate_conf)) {
      $this->errorMsg("$this->rsyncupdate_conf is not readable.", self::STATUS_ERROR);
    }

    $this->rsyncupdate_dom = new DOMDocument();
    if (!$this->rsyncupdate_dom->load($this->rsyncupdate_conf)) {
      $this->errorMsg("Failed to load $this->rsyncupdate_conf DOMDocument.", self::STATUS_ERROR);
    }

    $this->rsyncupdate_xpath = new DOMXPath($this->rsyncupdate_dom);

    $this->getRemoteConfig();
    $this->getMasterConfig();
    $this->getInstanceIDs();
  }

  protected function getRemoteConfig() {
    /** @var DOMElement $monitoring */
    $monitoring = $this->rsyncupdate_xpath->query("(/groups/monitoring)[1]")->item(0);

    if (!$monitoring) {
      $this->errorMsg('Cannot find server configuration.', self::STATUS_ERROR);
    }

    $this->master_host = rtrim($monitoring->getAttribute('url'), '/') . '/';
    $this->username    = $monitoring->getAttribute('username');
    $this->password    = $monitoring->getAttribute('password');

    if (!$this->master_host || !$this->username || !$this->password) {
      $this->errorMsg('Cannot find server configuration.', self::STATUS_ERROR);
    }
  }

  protected function getInstanceIDs() {
    /** @var DOMNodeList $instances */
    $instances = $this->rsyncupdate_xpath->query("//instance");

    if (!$instances) {
      $this->errorMsg('No configured instance.', self::STATUS_WARNING);
    }

    $this->all_instances = array();
    $this->instance_ids  = array();

    /** @var DOMElement $_instance */
    foreach ($instances as $_instance) {
      $_shortname   = $_instance->getAttribute("shortname");
      $_path        = $_instance->getAttribute("path");
      $_instance_id = $_instance->getAttribute("id");
      $_server_id   = $_instance->getAttribute("server_id");

      $this->all_instances[$_shortname] = array(
        'path'      => $_path,
        'id'        => $_instance_id,
        'server_id' => $_server_id
      );
      $this->instance_ids[]             = $_instance_id;
    }
    $this->instance_ids = array_unique($this->instance_ids);

    if (!$this->instance_ids) {
      $this->errorMsg('No configured instance.', self::STATUS_WARNING);
    }
  }

  protected function checkRemoteAuthorization() {
    $this->out($this->output, "Checking remote authorization...");
    $this->out($this->output, "Communicating with {$this->master_host}...");

    $url = $this->master_host . "?" . http_build_query(
        array(
          "m"               => "monitorServer",
          "a"               => "check_remote_authorization",
          "login"           => "1",
          "suppressHeaders" => "1",
          "username"        => $this->username,
          "password"        => $this->password
        )
        , "", "&"
      );

    $http_client = curl_init($url);

    curl_setopt($http_client, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($http_client, CURLOPT_TIMEOUT, 10);
    curl_setopt($http_client, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($http_client, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($http_client, CURLOPT_POST, true);
    curl_setopt($http_client, CURLOPT_POSTFIELDS, json_encode($this->instance_ids));

    return curl_exec($http_client);
  }

  protected function doSVNRevert() {
    $this->out($this->output, "Checking SVN status...");
    $out = $this->wc->status(".");

    if ($out) {
      $this->out($this->output, "SVN status checked.");

      try {
        $modified_files = $this->getModifiedFilesByXML($out);
      }
      catch (Exception $e) {
        $this->errorMsg('Cannot parse modified files by XML.', self::STATUS_ERROR);
      }

      if ($modified_files) {
        $this->out($this->output, "These files are modified locally:");

        foreach ($modified_files as $_file) {
          $this->out($this->output, $_file);
        }

        // Files have to be reverted
        $this->out($this->output, "Reverting files...");

        try {
          // /!\ Long list may handle an exception
          $this->wc->revert($modified_files);
        }
        catch (Exception $e) {
          $this->errorMsg($e, self::STATUS_ERROR);
        }

        $this->out($this->output, "Files reverted.");
      }
    }
  }

  protected function doSVNUpdate() {
    $this->out($this->output, "SVN update in progress...");

    $this->wc->update(array(), "HEAD");

    $this->out($this->output, "SVN update completed.");

    // SVN status file writing
    $this->writeSVNStatusFile();
  }

  /**
   * @see parent::checkFilePresence
   */
  protected function checkFilePresence($result) {
    return (preg_match_all("#(" . implode("|", $this->patterns_to_check) . ")#m", $result, $matches));
  }

  protected function clearCache($url) {
    $http_client = curl_init("http://{$url}/modules/system/public/clear_cache.php");

    curl_setopt($http_client, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($http_client, CURLOPT_TIMEOUT, 10);
    curl_setopt($http_client, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($http_client, CURLOPT_FOLLOWLOCATION, true);

    $res  = curl_exec($http_client);
    $info = curl_getinfo($http_client);

    return ($info['http_code'] == "200") ? $res : false;
  }

  protected function getIPAddresses() {
    $ip_addresses = array();
    foreach ($this->instances_to_perform as $_path) {
      $ip_addresses[$_path] = 'localhost';
      if (preg_match('/(?:[0-9]{1,3}\.){3}[0-9]{1,3}/', $_path, $match)) {
        $ip_addresses[$_path] = $match[0];
      }
    }

    return $ip_addresses;
  }

  protected function getMasterConfig() {
    /** @var DOMElement $master */
    $master = $this->rsyncupdate_xpath->query("(/groups/master)[1]")->item(0);

    if (!$master) {
      $this->errorMsg('Cannot find master configuration.', self::STATUS_ERROR);
    }

    $this->master_name = $master->getAttribute('name');
    $this->master_role = $master->getAttribute('role');
    $this->server_id   = $master->getAttribute('server_id');

    if (!$this->master_name || !$this->master_role || !$this->server_id) {
      $this->errorMsg('Cannot find master configuration.', self::STATUS_ERROR);
    }
  }

  protected function sendReport() {
    $url = $this->master_host . "?" . http_build_query(
        array(
          "m"               => "monitorServer",
          "a"               => "get_auto_update_report",
          "login"           => "1",
          "suppressHeaders" => "1",
          "username"        => $this->username,
          "password"        => $this->password
        )
        , "", "&"
      );

    $http_client = curl_init($url);

    curl_setopt($http_client, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($http_client, CURLOPT_TIMEOUT, 10);
    curl_setopt($http_client, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($http_client, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($http_client, CURLOPT_POST, true);
    curl_setopt($http_client, CURLOPT_POSTFIELDS, json_encode($this->makeReport()));

    return curl_exec($http_client);
  }

  protected function makeReport() {
    $instances = $this->formatInstanceIDs();

    $data = array(
      'id'                       => "{$this->server_id}-{$this->master_name}",
      'date'                     => date('Y/m/d H:i:s', $this->start_time),
      'server_id'                => $this->server_id,
      'instance_ids_all'         => $instances['all'],
      'instance_ids_not_allowed' => implode(' ', $this->instances_not_allowed),
      'instance_ids_ok'          => $instances['updated'],
      'instance_ids_ko'          => $instances['skipped'],
      'role'                     => $this->master_role,
      'status'                   => $this->status,
      'elapsed_time'             => $this->elapsed_time,
      'branch'                   => str_replace('_', '/', $this->master_branch),
      'body'                     => trim($this->report)
    );

    return $data;
  }

  protected function formatInstanceIDs() {
    $all_instances     = array();
    $updated_instances = array();

    foreach ($this->all_instances as $_instance) {
      $all_instances[] = $_instance['server_id'] . '-' . $_instance['id'];

      foreach ($this->instances_to_perform as $_instance_to_perform) {
        if ($_instance_to_perform == $_instance['path']) {
          $updated_instances[] = $_instance['server_id'] . '-' . $_instance['id'];
          break;
        }
      }
    }

    $skipped_instances = array_diff($all_instances, $updated_instances);

    $instances = array(
      'all'     => array(),
      'updated' => array(),
      'skipped' => array()
    );

    foreach ($all_instances as $_instance) {
      $value = explode('-', $_instance);

      $instances['all'][] = array(
        'server_id'   => $value[0],
        'instance_id' => $value[1]
      );
    }

    foreach ($updated_instances as $_instance) {
      $value = explode('-', $_instance);

      $instances['updated'][] = array(
        'server_id'   => $value[0],
        'instance_id' => $value[1]
      );
    }

    foreach ($skipped_instances as $_k => $_instance) {
      $value = explode('-', $_instance);

      if (!in_array($value[1], $this->instances_not_allowed)) {
        $instances['skipped'][] = array(
          'server_id'   => $value[0],
          'instance_id' => $value[1]
        );
      }
    }

    return $instances;
  }
}
