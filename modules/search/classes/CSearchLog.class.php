<?php

/**
 * $Id$
 *
 * @category ${Module}
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

use Elastica\Query\QueryString;
use Elastica\Query;

/**
 * Description
 */
class CSearchLog extends CSearch {

  static $settings = array(
    "analysis" => array(
      "analyzer"  => array(
        "custom_analyzer"        => array(
          "type"      => "custom",
          'tokenizer' => 'nGram',
          'filter'    => array("stopwords", "asciifolding", "lowercase", "snowball", "worddelimiter", "elision")
        ),
        "custom_search_analyzer" => array(
          "type"      => "custom",
          "tokenizer" => "standard",
          "filter"    => array("stopwords", "asciifolding", "lowercase", "snowball", "worddelimiter", "elision")
        )
      ),
      "tokenizer" => array(
        "nGram" => array(
          "type"     => "nGram",
          "min_gram" => "3",
          "max_gram" => "20"
        )
      ),
      "filter"    => array(
        "snowball"      => array(
          "type"     => "snowball",
          "language" => "French"
        ),
        "stopwords"     => array(
          "type"        => "stop",
          "stopwords"   => array("_french_"),
          "ignore_case" => "true"
        ),
        "elision"       => array(
          "type"     => "elision",
          "articles" => array("l", "m", "t", "qu", "n", "s", "j", "d")
        ),
        "worddelimiter" => array(
          "type" => "word_delimiter"
        )
      )
    )
  );

  static $names_mapping = array("generique", "pharmacie", "pmsi", "bloc");

  static $mapping_log = array(
    "date"         => array(
      'type'           => 'date',
      'format'         => 'yyyy/MM/dd HH:mm:ss||yyyy/MM/dd',
      'include_in_all' => true
    ),

    "user_id"      => array(
      'type'           => 'integer',
      'include_in_all' => true
    ),

    "body"         => array(
      'type'           => 'string',
      'include_in_all' => true,
    ),

    "types"        => array(
      'type'           => 'string',
      'include_in_all' => true
    ),

    "aggregation"  => array(
      'type'           => 'boolean',
      'include_in_all' => true
    )
  );

  /**
   * Create the mapping for logging
   *
   * @param Elastica /Index $index
   *
   * @return void
   */
  function createLogMapping($index) {
    $index = $this->createIndex(CAppUI::conf("db std dbname") . "_log", null, false);
    foreach (self::$names_mapping as $names) {
      $type  = $this->createType($index, $names);
      $this->createMapping($type, self::$mapping_log);
    }

  }

  /**
   * Construct the datum for logging
   *
   * @param array   $names_types  Types
   * @param string  $contexte     Contexte
   * @param integer $user_id      User who have make the search
   * @param string  $words        the words of the search
   * @param bool    $aggregation  aggregation or not
   *
   * @return void
   */

  function log($names_types, $contexte, $user_id, $words, $aggregation) {

    $document                 = array();
    $document['aggregation']  = $aggregation;
    $document['body']         = $words;
    $document['user_id']      = $user_id;
    $document['types']        = implode(" ", $names_types);
    $document['date']         = CMbDT::format(null, "%Y/%m/%d");

    $this->createClient();
    $index = $this->loadIndex(CAppUI::conf("db std dbname") . "_log");
    $type  = $index->getType($contexte);
    $log   = $type->createDocument('', $document);

    $type->addDocument($log);
    $type->getIndex()->refresh();
  }

  /**
   * * Construct query with types informations
   *
   * @param string $words the words query
   * @param array  $types types
   *
   * @return string
   */
  function constructWordsWithType($words, $types) {

    if (count($types) == 0) {
      return $words;
    }
    $types = implode(' || ', $types);
    $words = $words . " types:(".$types.")";

    return $words;
  }

  /**
   * * Construct query with prat informations
   *
   * @param string $words         the words query
   * @param string $specific_user the id's of the specifics users
   *
   * @return string
   */
  function constructWordsWithUser($words, $specific_user) {
    if (!$specific_user) {
      return $words;
    }
    else {
      $users_id = explode('|', $specific_user);
      $user_req = implode(' || ', $users_id);
      $words    = $words . " user_id:(" .$user_req . ")";
    }

    return $words;
  }

  /**
   * simple search with an operator and words
   *
   * @param string  $operator    'And' or 'Or' default : 'Or'
   * @param string  $words       data
   * @param integer $start       the begining of the paging
   * @param integer $limit       the interval of the paging
   * @param array   $names_types the restrictive type(s) where the search take place.
   * @param bool    $aggregation parameter the search to be aggregated or not.
   *
   * @return \Elastica\ResultSet
   */
  function searchQueryString($operator, $words, $start = 0, $limit = 30, $names_types = null, $aggregation = false) {

    $words = CmbString::normalizeUtf8($words);
    // Define a Query. We want a string query.
    $elasticaQueryString  = new QueryString();

    //'And' or 'Or' default : 'Or'
    $elasticaQueryString->setDefaultOperator($operator);
    $elasticaQueryString->setAnalyzer("custom_search_analyzer");
    $elasticaQueryString->setQuery($words);

    // Create the actual search object with some data.
    $elasticaQuery        = new Query();
    $elasticaQuery->setQuery($elasticaQueryString);

    //create aggregation
    if ($aggregation) {
      // on aggrège d'abord par class d'object référents
      // on effectue un sous aggrégation par id des objets référents.
      $agg_by_date = new CSearchAggregation("Terms", "date_log", "date", 10);
      $sub_agg_by_user = new CSearchAggregation("Terms", "user_id", "user_id", 10);
      $sub_agg_by_contexte = new CSearchAggregation("Terms", "contexte", "_type", 10);
      $sub_agg_by_user->_aggregation->addAggregation($sub_agg_by_contexte->_aggregation);
      $agg_by_date->_aggregation->addAggregation($sub_agg_by_user->_aggregation);

      $elasticaQuery->addAggregation($agg_by_date->_aggregation);
    }
    else {
      //  Pagination
      $elasticaQuery->setFrom($start);    // Where to start
      $elasticaQuery->setLimit($limit);
    }

    //Highlight
    $elasticaQuery->setHighlight(
      array(
        "fields" => array("body" => array(
          "pre_tags" => array(" <em> <strong> "),
          "post_tags" => array(" </strong> </em>"),
          "fragment_size" => 80,
          "number_of_fragments" => 10,
        )),
      ));

    //Search on the index.
    $index = CAppUI::conf("db std dbname")."_log";
    $this->_index = $this->loadIndex($index);
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($this->_index);
    if ($names_types) {
      $search->addTypes($names_types);
    }

    return $search->search($elasticaQuery);
  }

  function searchQueryLogDetails ($operator, $words, $names_types = null) {
    $words = CmbString::normalizeUtf8($words);
    // Define a Query. We want a string query.
    $elasticaQueryString  = new QueryString();

    //'And' or 'Or' default : 'Or'
    $elasticaQueryString->setDefaultOperator($operator);
    $elasticaQueryString->setAnalyzer("custom_search_analyzer");
    $elasticaQueryString->setQuery($words);

    // Create the actual search object with some data.
    $elasticaQuery        = new Query();
    $elasticaQuery->setQuery($elasticaQueryString);

    //Search on the index.
    $index = CAppUI::conf("db std dbname")."_log";
    $this->_index = $this->loadIndex($index);
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($this->_index);
    if ($names_types) {
      $search->addTypes($names_types);
    }

    return $search->search($elasticaQuery);

  }
  /**
   * Load the aggregation and format array to display in the search template
   *
   * @param $aggregation
   */
  function loadAggregationLog ($aggregation) {

    $objects_refs = array ();
    $agg_user_date     = $aggregation['date_log']['buckets'];
    foreach ($agg_user_date as $_agg) {
      $agg_ref_user_id  = $_agg['user_id']['buckets'];

      foreach ($agg_ref_user_id as $__agg) {
        $id_object = $__agg['key'];
        $object    = CMbObject::loadFromGuid("CMediusers-$id_object");
        $object->loadRefFunction();
        $objects_refs[$id_object]["date_log"] = CMbDT::format($_agg["key_as_string"], "%Y-%m-%d");
        $objects_refs[$id_object]["object"] = $object;
        $agg_contexte                       = $__agg['contexte']['buckets'];
        foreach ($agg_contexte as $_key => $___agg) {
          $key                                              = $___agg['key'];
          $count                                            = $___agg['doc_count'];
          $objects_refs[$id_object]['contexte'][$_key]['key']   = $key;
          $objects_refs[$id_object]['contexte'][$_key]['count'] = $count;
        }
      }
    }

    return $objects_refs;
  }

  /**
   * @return array
   */
  function loadCartoInfos() {
    $index      = $this->loadIndex(CAppUI::conf("db std dbname") . "_log");

    // récupération du nombre de docs "indexés"
    $nbdocs_indexed      = $index->count();
    $result ["nbdocs_indexed"] = $nbdocs_indexed;

    return $result;
  }

  /**
   * @return array
   */
  function loadContextes () {
    return self::$names_mapping;
  }
}
