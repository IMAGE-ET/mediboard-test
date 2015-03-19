<?php

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */


CAppUI::requireLibraryFile("Elastica/autoloader", false);

use Elastica\Type;
use Elastica\Client;
use Elastica\Document;
use Elastica\Type\Mapping;
use Elastica\Query;
use Elastica\Request;

/**
 * Class CSearch
 * Manage Elastica Library in order to index documents
 */
class CSearch {

  /** @var  Elastica\Client _client */
  public $_client;
  /** @var  Elastica\Index _index */
  public $_index;
  /** @var  Elastica\Type\Mapping _mapping */
  public $_mapping;

  /**
   * Create client for indexing
   *
   * @param Array $hosts [optional] needs an array like
   *               array(
   *                       'host' => 'mydomain.org',
   *                       'port' => 12345
   *                     )
   *
   * @return void
   */
  function createClient ($hosts = null) {
    if (!$hosts) {
      $connections["connections"] = $this->getServerAddresses();
    }
    else {
      $connections["connections"] = $hosts;
    }

    $this->_client = new Client($connections);
  }

  /**
   * Get the list of server addresses
   *
   * @return array
   */
  private function getServerAddresses(){

    $conf_host = trim(CAppUI::conf("search client_host"));
    $conf_port = trim(CAppUI::conf("search client_port"));

    $servers = preg_split("/\s*,\s*/", $conf_host);
    $list = array();
    foreach ($servers as $_server) {
      $list[] =  array (
        'host' => $_server,
        'port' => $conf_port,
      );
    }
    return $list;
  }

  /**
   * Creates a new index object
   *
   * @param string $name   index name
   * @param array  $params The params of the index (nb shards, replicas, etc...)
   * @param bool   $bool   Deletes index first if already exists (default = false).
   *
   * @return Elastica\Index
   */
  function createIndex ($name, $params, $bool=false) {
    if (!$name) {
      $conf_name = CAppUI::conf("search index_name");
      $name = ($conf_name) ? $conf_name : CAppUI::conf("db std name");
    }
    if (!$params) {
      $params = self::$settings_default;
      $params["number_of_replicas"] = CAppUI::conf("search nb_replicas");
    }
    $this->_index = $this->_client->getIndex($name);
    $this->_index->create($params, $bool);
    return $this->_index;
  }

  /**
   * Update settings of the index
   *
   * @param Elastica/Index $index the index
   *
   * @return void
   */
  function updateIndex ($index) {
    if (!$index) {
      $this->_index = $this->createIndex(null, null, false);
    }
    $index = $this->loadIndex();
    $this->updateIndexSettings($index);
  }

  /** Update an index settings
   *
   * @param Elastica\Index $index    the index
   * @param array          $settings the settings you want to apply
   *
   * @return void
   */
  function updateIndexSettings ($index, $settings = null) {
    if (!$settings) {
      $settings = self::$settings_default;
    }
    $index->close();
    $index->setSettings($settings);
    $index->open();
  }

  /**
   * Get an index object
   *
   * @param string $name index name
   *
   * @return Elastica\Index
   */
  function getIndex ($name) {
    if ($this->_index) {
      return  $this->_index;
    }
    $this->_index = $this->_client->getIndex($name);
    return  $this->_index;
  }

  /**
   * Load the index for uses.
   *
   * @param string $name The name of the index [OPTIONNAL]
   *
   * @return \Elastica\Index
   */
  function loadIndex ($name = null) {
    if (!$name) {
      $conf_name = CAppUI::conf("search index_name");
      $name = ($conf_name) ? $conf_name : CAppUI::conf("db std dbname");
    }
    return $this->getIndex($name);
  }
  /**
   * Creates a new Type object
   *
   * @param Elastica\Index $index the Index where you want to create your Type
   * @param string         $name  Type name
   *
   * @return Elastica\Type
   */
  function createType ($index, $name) {
    $type = $index->getType($name);
    return $type;
  }

  /**
   * Creates a new mapping object
   *
   * @param Elastica\Type $type  the type where you want to create your mapping
   * @param Array         $array the mapping which you want to create
   *
   * @return Elastica\Type
   */
  function createMapping ($type, $array) {
    // Define mapping
    $mapping = new Mapping();
    $mapping->setType($type);
    // Set mapping
    $mapping->setProperties($array);
    // Send mapping to type
    $mapping->send();
  }

  /**
   * First indexing create mapping
   *
   * @param array          $names_types the name of types we want to create
   * @param Elastica\Index $index       the index where we want to create those types
   *
   * @return void
   */
  function firstIndexingMapping ($names_types , $index) {
    if (!$index) {
      $this->_index = $this->createIndex(null, null, false);
    }
    /** @var Elastica\Type $elasticaType */
    foreach ($names_types as $name_type) {
      $type  = $this->createType($this->_index, $name_type);
      $this->createMapping($type, self::$mapping_default);
    }
  }

  /**
   * Get data from the temporary table.
   *
   * @param string $limit        the number of data you want to get
   * @param string $object_class the class of data you want to get [OPTIONNAL]
   *
   * @return array  The Result of the query
   */
  function getDataTemporaryTable ($limit, $object_class = null) {
    $query = new CSearchQuery();
    return $query->getDataTemporaryTable($limit, $object_class);
  }

  /**
   * Delete data from the temporary table.
   *
   * @param array $array the array of the id of the data you want to delete
   *
   * @return bool  The Result of the query
   */
  function deleteDataTemporaryTable ($array) {
    $query = new CSearchQuery();
    return $query->deleteDataTemporaryTable($array);
  }

  /**
   * Delete datum from the temporary table.
   *
   * @param integer $id the id of datum you want to delete
   *
   * @return bool  The Result of the query
   */
  function deleteDatumTemporaryTable ($id) {
    $query = new CSearchQuery();
    return $query->deleteDatumTemporaryTable($id);
  }

  /**
   * Construit les données afin que celles-ci soient indexées (avec les fields corrects)
   *
   * @param CMbObject $datum The datum you want to construct
   *
   * @return array
   */
  function constructDatum ($datum) {
    if ($datum['type'] != 'delete') {
      /** @var IIndexableObject|CStoredObject $object */
      $object = CModelObject::getInstance($datum["object_class"]);
      if (!$object->load($datum['object_id'])) {
        $datum_to_index["id"]  = $datum['object_id'];
        $datum_to_index["date"] = CMbDT::format(CMbDT::dateTime(), "%Y/%m/%d");
          return $datum_to_index;
      }
      //On récupère les champs à indexer.
      $datum_to_index = $object->getIndexableData();

      if (!$datum_to_index["date"]) {
        $datum_to_index["id"]          = $datum['object_id'];
        $datum_to_index["date"] = CMbDT::format(CMbDT::dateTime(), "%Y/%m/%d");
      }
      $datum_to_index['body'] = $this->normalizeEncoding($datum_to_index['body']);
      $datum_to_index['title'] = $this->normalizeEncoding($datum_to_index['title']);
    }
    else {
      $datum_to_index["id"]          = $datum['object_id'];
      $datum_to_index["date"]        = CMbDT::format(CMbDT::dateTime(), "%Y/%m/%d");
    }

    return $datum_to_index;
  }

  /**
   * Construit les tableaux de données afin que celles-ci soient bulk indexées (avec les fields corrects)
   *
   * @param array $data data you want to construct
   *
   * @return array
   */
  function constructBulkData ($data) {
    $data_to_index = array();
    foreach ($data as $key => $_datum) {
      $data_to_index[$_datum['object_class']][$_datum['type']][$key] = $this->constructDatum($_datum);;
    }
    return $data_to_index;
  }

  /**
   * Construit les données afin que celles-ci soient indexées (avec les fields corrects)
   *
   * @param CMbObject     $datum the datum you want to construct
   * @param Elastica\Type $type  the type where you want to index the data
   *
   * @return array
   */
  function indexingDatum ($datum, $type) {
    $datum_to_index = $this->constructDatum($datum);
    $document = $type->createDocument($datum['object_id'], $datum_to_index);
    switch ($datum['type']) {
      case 'create':
        $type->addDocument($document);
        break;
      case 'store':
            $type->updateDocument($document);
        break;
      case 'delete':
        $type->deleteDocument($document);
        break;
      case 'merge':
        //nothing to do
        /* supprimer un des deux et faire un update de l'autre.*/
        break;
      default:
        return false;
    }
    $type->getIndex()->refresh();
    $this->deleteDatumTemporaryTable($datum['search_indexing_id']);
    return true;
  }

  /**
   * indexation en bulk avec les données contstruites (avec les fields corrects)
   *
   * @param array $data les datas que vous voulez indexer
   *
   * @return bool
   */
  function bulkIndexing ($data) {
    $data_to_index = $this->constructBulkData($data);
    foreach ($data_to_index as $type_name => $_type) {
      $typeES = (strpos($type_name, 'CExObject') === 0 ) ? $this->_index->getType("CExObject") : $this->_index->getType($type_name);
      foreach ($_type as $action => $_data) {
        $documents = array();
        foreach ($_data as $_datum) {
          $documents[] = new Document($_datum['id'], $_datum);
        }
        switch ($action) {
          case 'create':
            $typeES->addDocuments($documents);
            break;

          case 'store':
            try {
              $typeES->updateDocuments($documents);
            } catch (Exception $e) {
              try {
                $typeES->addDocuments($documents);
              } catch (Exception $e) {
                mbLog($e->getMessage());
              }
            }
            break;

          case 'delete':
            $typeES->deleteDocuments($documents);
            break;

          case 'merge':
            /* supprimer un des deux et faire un update de l'autre.*/
            break;

          default:
            return false;
        }
      }
      $typeES->getIndex()->refresh();
    }
    $ids_to_delete = CMbArray::pluck($data, "search_indexing_id");
    $this->deleteDataTemporaryTable($ids_to_delete);

    return true;
  }

  /**
   *  search with words and options
   *
   * @param string  $words         data
   * @param integer $start         the begining of the paging
   * @param integer $limit         the interval of the paging
   * @param array   $names_types   the restrictive type(s) where the search take place.
   * @param bool    $aggregation   parameter the search to be aggregated or not.
   * @param integer $sejour_id     the id of the sejour
   * @param string  $specific_user the ids of users selected
   * @param bool    $details       details of query
   * @param string  $date          date of query
   * @param bool    $fuzzy_search  fuzzy the query
   *
   * @return \Elastica\ResultSet
   */
  function searchQueryString($words, $start = 0, $limit = 30, $names_types = null, $aggregation = false, $sejour_id = null, $specific_user=null, $details=null, $date=null, $fuzzy_search = null) {
    $query = new CSearchQuery();
    $query_string =  $query->searchQueryString($words, $start, $limit, $aggregation, $sejour_id, $specific_user, $details, $date, $fuzzy_search);

    //Search on the index.
    $this->_index = $this->loadIndex();
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($this->_index);
    if ($names_types) {
      $search->addTypes($names_types);
    }
    return $search->search($query_string);
  }

  /**
   * simple search
   *
   * @param string  $words     data
   * @param integer $start     the begining of the paging
   * @param integer $limit     the interval of the paging
   * @param integer $sejour_id the id of the sejour
   *
   * @return \Elastica\ResultSet
   */
  function searchQueryStringManual($words, $start, $limit, $sejour_id) {
    $query = new CSearchQuery();
    $query_string =  $query->searchQueryStringManual($words, $start, $limit, $sejour_id);

    //Search on the index.
    $this->_index = $this->loadIndex();
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($this->_index);

    return $search->search($query_string);
  }

  /**
   * The auto search from favoris
   *
   * @param array   $favoris the favoris
   * @param CSejour $sejour  the sejour
   *
   * @return array
   */
  function searchAuto ($favoris, $sejour) {
    $query = new CSearchQuery();
    $tab_search = array();

    //Search on the index.
    $this->createClient();
    $this->_index = $this->loadIndex();
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($this->_index);

    foreach ($favoris as $_favori) {
      if ($_favori->types) {
        $search->addTypes(explode("|", $_favori->types));
      }
      $sub_query = $query->querySearchAuto($_favori, $sejour);
      $results_query = $search->search($sub_query);
      $tab_search[$_favori->_id]["titre"] = $_favori->titre;
      $tab_search[$_favori->_id]["entry"] =$_favori->entry;
      $tab_search[$_favori->_id]["time"] = $results_query->getTotalTime();
      $tab_search[$_favori->_id]["nb_results"] = $results_query->getTotalHits();
      foreach ($results_query->getResults() as $_result) {
        $item = $_result->getHit();
        if (isset($item["highlight"]["body"][0])) {
          $item["highlight"]["body"][0] = mb_convert_encoding($item["highlight"]["body"][0], "WINDOWS-1252",  "UTF-8");
        }
        $tab_search[$_favori->_id]["results"][] = $item;
      }
    }

    return $tab_search;
  }

  function queryByType ($words, $name, $types) {

    $query_words = new Elastica\Query\QueryString();
    $query_words->setQuery($words);
    $query_words->setFields(array("body", "title"));
    $query_words->setDefaultOperator("and");

    $agg_by_type = new CSearchAggregation("Terms", "ref_type", "_type", 100);
    $query = new Query($query_words);
    $query->addAggregation($agg_by_type->_aggregation);

    //Search on the index.
    $this->_index = $this->loadIndex($name);
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($this->_index);
    $search->addTypes($types);

    return $search->search($query);
  }
  /**
   * Construct query with date informations
   *
   * @param string $_date     type of interval date
   * @param string $_min_date begining date
   * @param string $_max_date final date
   *
   * @return string
   */
  function constructWordsWithDate($_date, $_min_date, $_max_date) {
    $query = new CSearchQuery();
    return $query->constructWordsWithDate($_date, $_min_date, $_max_date);
  }

  /**
   * * Construct query with prat informations
   *
   * @param string $words         the words query
   * @param string $specific_user the id of the specific user
   * @param string $sejour_id     the id of the sejour
   *
   * @return string
   */
  function constructWordsWithPrat($words, $specific_user, $sejour_id) {
    $query = new CSearchQuery();
    return $query->constructWordsWithPrat($words, $specific_user, $sejour_id);
  }

  /**
   * Construct query with sejour informations (PMSI)
   *
   * @param string $words     the words query
   * @param string $sejour_id the id of the sejour
   *
   * @return string
   */
  function constructWordsWithSejour($words, $sejour_id) {
    $query = new CSearchQuery();
    return $query->constructWordsWithSejour($words, $sejour_id);
  }

  /**
   * Load the aggregation and format array to display in the search template
   *
   * @param array $aggregation the aggregation
   *
   * @return array
   */
  function loadAggregationObject ($aggregation) {
    $query = new CSearchQuery();
    return $query->loadAggregationObject($aggregation);
  }

  /**
   * Method to load infos about serveur ES
   *
   * @return array
   */
  function loadCartoInfos() {
    $query = new CSearchQuery();
    $query_aggreg = $query->aggregCartoCountByType();
    //Search on the index.
    $this->_index = $this->loadIndex();
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($this->_index);

    return $query->loadCartoInfos($this, $search->search($query_aggreg));
  }

  /**
   * cleaning text before indexing method
   *
   * @param string $content the content which have to be cleaned
   *
   * @return string The content cleaned
   */
  static function getRawText($content) {
    $content = strtr(
      $content,
      array(
        "<"      => " <",
        "&nbsp;" => " ",
      )
    );
    $content = self::purifyHTML($content);
    $content = preg_replace("/\s+/", ' ', $content);
    $content = html_entity_decode($content);

    return trim($content);
  }

  /**
   * HTML cleaning method
   *
   * @param string $html HTML to purify
   *
   * @return string
   */
  static function purifyHTML($html) {
    if (trim($html) == "") {
      return $html;
    }

    static $cache = array();
    static $purifier;

    if (isset($cache[$html])) {
      return $cache[$html];
    }

    // Only Unicode alphanum characters and whitespaces
    /*
    if (!preg_match("/[^\p{L}\p{N}\s]/u", $html)) {
      // No need to purify
      return $html;
    }
    */

    if (!$purifier) {
      $root = CAppUI::conf("root_dir");

      if (!class_exists("HTMLPurifier", false) || !class_exists("HTMLPurifier_Config", false)) {
        $file = "$root/lib/htmlpurifier/library/HTMLPurifier.auto.php";
        if (is_readable($file)) {
          include_once $file;
        }
      }

      $config = HTMLPurifier_Config::createDefault();
      // App encoding (in order to prevent from removing diacritics)
      $config->set('Core.Encoding', "UTF-8");
      $config->set('Cache.SerializerPath', "$root/tmp");
      $config->set('HTML.Allowed', "");

      $purifier = new HTMLPurifier($config);
    }

    $purified = $purifier->purify(mb_convert_encoding($html, "UTF-8", "Windows-1252"));

    if ($purified) {
      $purified = mb_convert_encoding($purified, "Windows-1252", "UTF-8");
    }

    if (isset($purified[5])) {
      $cache[$html] = $purified;
    }

    return $purified;
  }

  /**
   * Method to get the setting of the index
   *
   * @param bool $default the setting by default
   *
   * @return array
   */
  function getSettings($default = true) {
    if (!$default) {
      return self::$settings;
    }
    return self::$settings_default;
  }

  /**
   * Method to normalize text
   *
   * @param String $text The text to normalize
   *
   * @return String
   */
  function normalizeEncoding($text) {
    $text = mb_convert_encoding($text, "UTF-8", "Windows-1252");
    $text = CMbString::normalizeUtf8($text);
    return $text;
  }

  /**
   * Method testing if we able to connect to server ES
   *
   * @param CGRoups $group The group
   *
   * @return void
   */
  function testConnection ($group) {
    try{
      $this->createClient();
      $index = $this->loadIndex();
      $search = new \Elastica\Search($this->_client);
      $search->addIndex($index);
      $this->_client->getCluster();
    } catch (Exception $e) {
      if (CAppUI::conf("search active_handler active_handler_search", $group)) {
        CAppUI::displayAjaxMsg("Le serveur de recherche n'est pas connecté", UI_MSG_ERROR);
      }
      else {
        CAppUI::displayAjaxMsg("Le serveur de recherche n'est pas configuré, veuillez prendre contact avec un administrateur", UI_MSG_ERROR);
      }
    }
  }

  // static settings
  static $settings = array(
    "analysis" => array(
      "analyzer" => array(
        "custom_analyzer" => array(
          "type" => "custom",
          'tokenizer' => 'nGram',
          'filter' => array("stopwords", "asciifolding", "lowercase", "snowball", "worddelimiter", "elision")
        ),
        "custom_search_analyzer" => array(
          "type" => "custom",
          "tokenizer" => "standard",
          "filter" => array("stopwords", "asciifolding", "lowercase", "snowball", "worddelimiter", "elision")
        )
      ),
      "tokenizer" => array (
        "nGram" => array(
          "type" => "nGram",
          "min_gram" => "3",
          "max_gram" => "20"
        )
      ),
      "filter" => array(
        "snowball" => array (
          "type"=> "snowball",
          "language"=> "French"
        ),
        "stopwords" => array (
          "type" => "stop",
          "stopwords"=> array ("_french_"),
          "ignore_case" => "true"
        ),
        "elision" => array (
          "type" => "elision",
          "articles" => array("l", "m", "t", "qu", "n", "s", "j", "d")
        ),
        "worddelimiter" => array(
          "type" => "word_delimiter"
        )
      )
    )
  );

  static $settings_default = array(
    'number_of_shards'   => 5,
    'number_of_replicas' => 1,
    "analysis"           => array(
      "analyzer" => array(
        "default" => array(
          "type"      => "custom",
          'tokenizer' => 'standard',
          'filter'    => array('standard', 'lowercase', 'mySnowball',  'asciifolding')
        )
      ),
      'filter'   => array(
        'mySnowball' => array(
          'type'     => 'snowball',
          'language' => 'French'
        )
      )
    )
  );

  static $mapping_default =  array(
    "id" => array(
      'type' => 'integer',
      'include_in_all' => true
    ),

    "author_id" => array(
      'type' => 'integer',
      'include_in_all' => true
    ),

    "prat_id" => array(
      'type' => 'integer',
      'include_in_all' => true
    ),

    "title" => array(
      'type' => 'string',
      'include_in_all' => false,
    ),

    "body" => array(
      'type' => 'string',
      'include_in_all' => true,
    ),

    "date" => array(
      'type' => 'date',
      'format' => 'yyyy/MM/dd HH:mm:ss||yyyy/MM/dd',
      'include_in_all' => true
    ),

    "patient_id"  => array(
      'type' => 'integer',
      'include_in_all' => true
    ),

    "function_id" => array(
      'type' => 'integer',
      'include_in_all' => true
    ),

    "group_id" => array(
      'type' => 'integer',
      'include_in_all' => true
    ),

    "object_ref_id" => array(
      'type' => 'integer',
      'include_in_all' => true
    ),

    "object_ref_class"=> array(
      'type' => 'string',
      'include_in_all' => true
    )
  );

}