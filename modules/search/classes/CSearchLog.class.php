<?php

/**
 * $Id$
 *
 * @category Search
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

  static $names_mapping = array("generique", "pharmacie", "pmsi", "prescription", "classique");

  static $mapping_log = array(
    "date"         => array(
      'type'           => 'date',
      'format'         => 'yyyy/MM/dd HH:mm:ss||yyyy/MM/dd',
      'include_in_all' => true
    ),

    "user_id"      => array(
      'type'           => 'string',
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
   * @return void
   */
  function createLogMapping() {
    $index = $this->createIndex($this->loadNameIndex(), null, false);
    foreach (self::$names_mapping as $names) {
      $type  = $this->createType($index, $names);
      $this->createMapping($type, self::$mapping_log);
    }

  }

  /**
   * Construct the datum for logging
   *
   * @param array   $names_types Types
   * @param string  $contexte    Contexte
   * @param integer $user_id     User who have make the search
   * @param string  $words       the words of the search
   * @param bool    $aggregation aggregation or not
   *
   * @return void
   */

  function log($names_types, $contexte, $user_id, $words, $aggregation) {
    if (!$names_types) {
      $names_types = array();
    }
    $document                 = array();
    $document['aggregation']  = $aggregation;
    $document['body']         = CMbString::normalizeUtf8($words);
    $document['user_id']      = $user_id;
    $document['types']        = implode(" ", $names_types);
    $document['date']         = CMbDT::format(null, "%Y/%m/%d");

    $this->createClient();
    $index = $this->loadIndex($this->loadNameIndex());
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

    $users_id = explode('|', $specific_user);
    $user_req = implode(' || ', $users_id);
    if ($user_req) {
      $words = $words . " user_id:(" . $user_req . ")";
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

    $words = CSearch::normalizeEncoding($words);
    // Define a Query. We want a string query.

    $queryString  = new Elastica\Query\QueryString($words);
    $queryString->setDefaultOperator("and");
    // Create the actual search object with some data.
    $query        = new Elastica\Query($queryString);

    //create aggregation
    if ($aggregation) {
      // on aggrège d'abord par class d'object référents
      // on effectue un sous aggrégation par id des objets référents.
      $agg_by_date = new CSearchAggregation("Terms", "date_log", "date", 10);
      $sub_agg_by_user = new CSearchAggregation("Terms", "user_id", "user_id", 10);
      $sub_agg_by_contexte = new CSearchAggregation("Terms", "contexte", "_type", 10);
      $sub_agg_by_user->_aggregation->addAggregation($sub_agg_by_contexte->_aggregation);
      $agg_by_date->_aggregation->addAggregation($sub_agg_by_user->_aggregation);

      $query->addAggregation($agg_by_date->_aggregation);
    }
    else {
      //  Pagination
      $query->setFrom($start);    // Where to start
      $query->setLimit($limit);
    }

    //Highlight
    $query->setHighlight(
      array(
        "fields" => array("body" => array(
          "pre_tags" => array(" <em> <strong> "),
          "post_tags" => array(" </strong> </em>"),
          "fragment_size" => 80,
          "number_of_fragments" => 10,
         )
        ),
      )
    );

    //Search on the index.
    $index = CAppUI::conf("db std dbname")."_log";
    $index = $this->loadIndex($index);
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($index);
    if ($names_types) {
      $search->addTypes($names_types);
    }

    return $search->search($query);
  }

  /**
   * method to search log details
   *
   * @param string $operator    the operator for the query
   * @param string $words       the words
   * @param string $names_types the types to search
   *
   * @return \Elastica\ResultSet
   */
  function searchQueryLogDetails ($operator, $words, $names_types = null) {
    $words = CmbString::normalizeUtf8(stripcslashes($words));
    // Define a Query. We want a string query.
    $elasticaQueryString  = new QueryString();

    //'And' or 'Or' default : 'Or'
    $elasticaQueryString->setDefaultOperator($operator);
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
    $elasticaQuery->setFrom(0);    // Where to start
    $elasticaQuery->setLimit(1000);

    return $search->search($elasticaQuery);

  }
  /**
   * Load the aggregation and format array to display in the search template
   *
   * @param array $aggregation the aggregation
   *
   * @return array
   */
  function loadAggregationLog ($aggregation) {

    $objects_refs = array ();
    $agg_user_date     = $aggregation['date_log']['buckets'];
    foreach ($agg_user_date as $_agg) {

      $date = ($_agg["key_as_string"] != "") ? CMbDT::format($_agg["key_as_string"], "%Y-%m-%d 00:00:00"): "";
      $agg_ref_user_id  = $_agg['user_id']['buckets'];

      foreach ($agg_ref_user_id as $__agg) {
        $id_object = $__agg['key']." ".$date;
        $object    = CMbObject::loadFromGuid("CMediusers-$id_object");
        $object->loadRefFunction();
        $objects_refs[$id_object]["date_log"] = $date;
        $objects_refs[$id_object]["object"] = $object;
        $agg_contexte                       = $__agg['contexte']['buckets'];
        foreach ($agg_contexte as $_key => $___agg) {
          $key = $___agg['key'];
          $count = $___agg['doc_count'];
          $objects_refs[$id_object]['contexte'][$_key]['key']   = $key;
          $objects_refs[$id_object]['contexte'][$_key]['count'] = $count;
        }
      }
    }

    return $objects_refs;
  }

  /**
   * method to load infos about search
   *
   * @return array
   */
  function loadCartoInfos() {
    $search_query = new CSearchQuery();
    $query_aggreg = $search_query->aggregCartoCountByType();
    //Search on the index.
    $index  = $this->loadIndex($this->loadNameIndex());
    $search = new \Elastica\Search($this->_client);
    $search->addIndex($index);
    $aggreg = $search->search($query_aggreg);

    // récupération du nombre de docs "indexés"
    $nbdocs_indexed      = $index->count();
    $result ["nbdocs_indexed"] = $nbdocs_indexed;

    // récupération des données de l'agregation
    $aggreg = $aggreg->getAggregation("ref_type");
    $result["aggregation"] = $aggreg["buckets"];

    return $result;
  }

  /**
   * method to load static contextes
   *
   * @return array
   */
  static function loadContextes () {
    return self::$names_mapping;
  }

  /**
   * method to load index name
   *
   * @return array
   */
  function loadNameIndex () {
    return CAppUI::conf("db std dbname") . "_log";
  }
}
