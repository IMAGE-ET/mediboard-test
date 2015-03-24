<?php

/**
 * $Id: ESReindex.class.php 21950 2014-02-06 15:42:21Z phenxdesign $
 *
 * @category CLI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 21950 $
 * @link     http://www.mediboard.org
 */

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * es:reindex command
 */
class ESReindex extends MediboardCommand {
  /** @var OutputInterface */
  protected $output;

  /** @var InputInterface */
  protected $input;

  /** @var DialogHelper */
  protected $dialog;

  /** @var string */
  protected $scroll_time;

  /** @var integer */
  protected $scroll_size;

  /** @var string */
  protected $to_index;

  /** @var string */
  protected $from_index;

  /** @var array */
  protected $types_to_index;

  /** @var string */
  protected $scroll_id;

  /** @var float */
  protected $start_time;

  /** @var float */
  protected $end_time;

  /**
   * @see parent::configure()
   */
  protected function configure() {
    $this
      ->setName('es:reindex')
      ->setDescription('Reindexing your ElasticSearch data with zero downtime')
      ->setHelp('Performs a scroll search and a bulk indexing')
      ->addArgument(
        'types_to_index',
        InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
        'Which mappings do you want to index?'
      );
  }

  /**
   * Display header information
   *
   * @return mixed
   */
  protected function showHeader() {
    $this->out($this->output, '<fg=red;bg=black>Reindexing your ElasticSearch data with zero downtime</fg=red;bg=black>');
  }

  /**
   * @see parent::execute()
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input  = $input;
    $this->output = $output;
    $this->dialog = $this->getHelperSet()->get('dialog');

    $this->showHeader();
    $this->setParams();

    foreach ($this->types_to_index as $_mapping) {
      $this->reindex($_mapping);
    }
  }

  protected function reindex($type_to_index) {
    $this->start_time = microtime(true);

    do {
      $this->out($this->output, "Indexing $type_to_index...");

      $ch = curl_init();

      $url = "http://localhost:9200/$this->from_index/$type_to_index/_search?scroll=$this->scroll_time&size=$this->scroll_size";
      if ($this->scroll_id) {
        $url = "http://localhost:9200/_search/scroll?scroll=$this->scroll_time&scroll_id=$this->scroll_id";
      }
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $this->out($this->output, "Requesting $url...");
      $output = curl_exec($ch);
      $this->out($this->output, "Request completed.");
      $result = json_decode($output, true);

      $this->scroll_id = $result['_scroll_id'];
      $hits      = $result['hits']['hits'];

      if (!$hits || empty($hits)) {
        $this->end_time = microtime(true);
        $this->out($this->output, "No data to send.");
        $this->out($this->output, "$type_to_index reindexing completed.");
        $this->out($this->output, "Elapsed time: " . ($this->end_time - $this->start_time));

        $next_type_to_index = next($this->types_to_index);
        $question = '<question>Reindex another mapping? [y/N] </question>';
        if ($next_type_to_index) {
          $question = "<question>Reindex another mapping? (Next mapping: $next_type_to_index) [y/N] </question>";
        }

        if ($this->dialog->askConfirmation($this->output, $question, false)) {
          if ($next_type_to_index) {
            continue;
          }
          else {
            $this->askForReindexing();
          }
        }
        else {
          $this->out($this->output, 'Exiting...');
          exit();
        }
      }

      $formatted_hits = array();
      foreach ($hits as $_k => $_hit) {
        $_hit['_index'] = $this->to_index;

        $formatted_hits[] = json_encode(array('index' => array('_id' => $_hit['_id'])));
        $formatted_hits[] = json_encode($_hit['_source']);
      }
      $formatted_hits = implode("\n", $formatted_hits) . "\n";

      // Save data into temporary memfile
      $fp = fopen('php://temp', 'r+');
      fwrite($fp, $formatted_hits);
      rewind($fp);

      $url = "http://localhost:9200/$this->to_index/$type_to_index/_bulk";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_PUT, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
      curl_setopt($ch, CURLOPT_INFILE, $fp);
      curl_setopt($ch, CURLOPT_INFILESIZE, strlen($formatted_hits));

      $this->out($this->output, "Sending data...");
      curl_exec($ch);
      $this->out($this->output, "Data sent.");
      fclose($fp);

    }
    while ($hits && $this->scroll_id);
  }

  protected function askForReindexing() {
    $type_to_index = $this->dialog->askAndValidate(
      $this->output,
      "Mapping to index: ",
      function ($answer) {
        if (!trim($answer)) {
          throw new \RunTimeException("You have to select a mapping");
        }

        return $answer;
      }
    );

    $this->types_to_index = array();
    $this->reindex($type_to_index);
  }

  protected function setParams() {
    $this->scroll_time = $this->dialog->askAndValidate(
      $this->output,
      "Select scroll time, ie 10m: ",
      function ($answer) {
        if (!preg_match('/\d+(s|m|h|d|w|M|y)/', trim($answer))) {
          throw new \RunTimeException("Wrong scroll time format, ie '10m': $answer");
        }

        return $answer;
      }
    );

    $this->scroll_size = $this->dialog->askAndValidate(
      $this->output,
      "Select scroll size, ie 100: ",
      function ($answer) {
        if (!preg_match('/\d+/', trim($answer))) {
          throw new \RunTimeException("Wrong scroll size format, ie '100': $answer");
        }

        return $answer;
      }
    );

    $this->from_index = $this->dialog->askAndValidate(
      $this->output,
      "Index to index from: ",
      function ($answer) {
        if (!trim($answer)) {
          throw new \RunTimeException("You have to select an index name.");
        }

        return $answer;
      }
    );

    $this->to_index = $this->dialog->askAndValidate(
      $this->output,
      "Index to index to: ",
      function ($answer) {
        if (!trim($answer)) {
          throw new \RunTimeException("You have to select an index name.");
        }

        return $answer;
      }
    );

    $this->types_to_index = $this->input->getArgument('types_to_index');
    if (!$this->types_to_index) {
      $this->out($this->output, '<fg=red;bg=black>No mapping selected, please select a mapping to index.</fg=red;bg=black>');

      $this->types_to_index[] = $this->dialog->askAndValidate(
        $this->output,
        "Mapping to index: ",
        function ($answer) {
          if (!trim($answer)) {
            throw new \RunTimeException("You have to select a mapping");
          }

          return $answer;
        }
      );
    }
  }
}
