<?php

/**
 * $Id: DeployMaJ.class.php 21950 2014-02-06 15:42:21Z phenxdesign $
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
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * deploy:maj command
 */
class DeployMaJ extends DeployOperation {
  /**
   * @see parent::configure()
   */
  protected function configure() {
    $this
      ->setName('deploy:maj')
      ->setDescription('Synchronize MB with RSYNC')
      ->setHelp('Performs an RSYNC command from MB Master')
      ->addOption(
        'update',
        'u',
        InputOption::VALUE_NONE,
        'Performs an SVN update'
      )
      ->addOption(
        'update-ignore-externals',
        'i',
        InputOption::VALUE_NONE,
        'Performs an SVN update ignoring externals'
      )
      ->addOption(
        'path',
        'p',
        InputOption::VALUE_OPTIONAL,
        'Working copy root',
        realpath(__DIR__ . "/../../")
      );
  }

  /**
   * @see parent::showHeader()
   */
  protected function showHeader() {
    $this->output->writeln(
      <<<EOT
<fg=blue;bg=black>
       __  __     _        _
      |  \/  |   / \      | |
      | |\/| |  / _ \  _  | |
      | |  | | / ___ \| |_| |
      |_|  |_|/_/   \_\\\___/

</fg=blue;bg=black>
EOT
    );
  }

  /**
   * @see parent::testBranch()
   */
  protected function testBranch($release_code) {
    return ($this->master_branch == $release_code);
  }

  /**
   * @see parent::rsync()
   */
  protected function rsync($files, $instance, $dry_run = false, &$merged_result = false) {
    $result = parent::rsync($files, $instance, $dry_run, $merged_result);

    if ($result) {
      foreach ($result as $_line) {
        if (is_array($merged_result)) {
          if (!isset($merged_result[$_line])) {
            $merged_result[$_line] = array();
          }

          $merged_result[$_line][] = $instance;
        }
      }
    }

    return $result;
  }

  /**
   * @see parent::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->output           = $output;
    $this->input            = $input;
    $this->dialog           = $this->getHelperSet()->get('dialog');
    $this->path             = $input->getOption('path');
    $this->ignore_externals = $input->getOption("update-ignore-externals");
    $this->update           = $input->getOption("update");

    $this->showHeader();

    if (!is_dir($this->path)) {
      throw new InvalidArgumentException("'$this->path' is not a valid directory.");
    }

    $this->getMasterBranch();
    if (!$this->master_branch) {
      return;
    }

    if ($this->ignore_externals || $this->update) {
      $this->update();
    }

    $dialog    = $this->getHelperSet()->get('dialog');
    $instances = $this->promptInstances();

    $this->out($this->output, "Checking branches...");
    $instances_with_branch = $this->checkBranches($instances);

    $this->out($this->output, "Result (<error>[branch]</error> will not be performed):\n");

    $to_perform = array();
    foreach ($instances_with_branch as $_instance) {
      $perform = "error";

      if ($_instance["perform"]) {
        $perform      = "info";
        $to_perform[] = $_instance["path"];
      }

      $this->output->writeln("- <$perform>" . $_instance["release_code"] . "</$perform> " . $_instance["path"]);
    }

    if (!$to_perform) {
      $this->out($this->output, "<error>No instance to update</error>");

      return;
    }

    // Ask confirmation by typing MASTER release code
    $this->confirmOperation();

    $this->out($this->output, "<info>Confirmation OK</info>");
    $this->out($this->output, "Performing operation...");

    // External libraries installation
    $this->installLibraries();

    $files = $this->getIncludedAndExcluded();

    $merged_result = array();
    foreach ($to_perform as $_instance) {
      // RSYNC with dry run
      $this->rsync($files, $_instance, true, $merged_result);
    }

    // RSYNC file diff table output
    $this->showFileDiffTable($to_perform, $merged_result);

    if (!$dialog->askConfirmation(
      $this->output,
      '<question>Confirm? [Y/n]</question> ',
      true
    )
    ) {
      return false;
    }

    // Progress bar
    $progress = $this->getHelperSet()->get('progress');
    $progress->start($this->output, count($to_perform));

    foreach ($to_perform as $_instance) {
      // RSYNC
      $result = parent::rsync($files, $_instance);
      //$this->checkFilePresence(implode("\n", $result), $output);

      // Next progress bar step
      $progress->advance();
      $this->output->writeln("");
    }

    $progress->finish();

    // Re-check remote release
    $this->out($this->output, "Current instances release:");
    $instances = $this->checkBranches($instances);

    foreach ($instances as $_instance) {
      $perform = "error";

      if ($_instance["perform"]) {
        $perform = "info";
      }

      $this->output->writeln("- <$perform>" . $_instance["release_code"] . "</$perform> " . $_instance["path"]);
    }
  }
}
