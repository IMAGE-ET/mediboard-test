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


CAppUI::requireLibraryFile("elastica/autoloader", false);

// The autoloader must be triggered, so the second argument must be true
if (!class_exists("CSearch", true)) {
  return;
}
use Elastica\Type;
use Elastica\Client;
use Elastica\Document;
use Elastica\Type\Mapping;
use Elastica\Query;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Filter\BoolOr;
use Elastica\Query\QueryString;

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
      $hosts = array (
        'host' => CAppUI::conf("search client_host"),
        'port' => CAppUI::conf("search client_port"),
      );
    }
    $this->_client = new Client($hosts);
  }

  /**
   * Creates a new index object
   *
   * @param string $name   index name
   * @param array  $params The params of the index (nb shards, replicas, etc...)
   * @param bool   $bool   true to verify if index already exists
   *
   * @return Elastica\Index
   */
  function createIndex ($name, $params, $bool=false) {
    if (!$name) {
      $name = CAppUI::conf("db std dbname");
    }
    if (!$params) {
      $params = array(
                      'number_of_shards' => 5,
                      'number_of_replicas' =>1
                    );
    }
    $this->_index = $this->_client->getIndex($name);
    $this->_index->create($params, $bool);
    return $this->_index;
  }

  /**
   * Get an index object
   *
   * @param string $name index name
   *
   * @return Elastica\Index
   */
  function getIndex ($name) {
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
      $name = CAppUI::conf("db std dbname");

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
   * Get data from the temporary table.
   *
   * @param string $limit        the number of data you want to get
   * @param string $object_class the class of data you want to get [OPTIONNAL]
   *
   * @return array  The Result of the query
   */
  function getDataTemporaryTable ($limit, $object_class = null) {
    $ds = CSQLDataSource::get("std");
    $query = ($object_class) ?
      "SELECT * FROM `search_indexing` WHERE `object_class` = '$object_class' ORDER BY `type`, `search_indexing_id` LIMIT $limit"
      :
      "SELECT * FROM `search_indexing` ORDER BY `object_class` ,`type`, `search_indexing_id` LIMIT $limit";
    return $ds->loadList($query);
  }

  /**
   * Delete data from the temporary table.
   *
   * @param array $array the array of the id of the data you want to delete
   *
   * @return bool  The Result of the query
   */
  function deleteDataTemporaryTable ($array) {
    $ds = CSQLDataSource::get("std");
    $query = "DELETE FROM `search_indexing` WHERE `search_indexing_id` ". $ds->prepareIn($array);
    return $ds->exec($query);
  }

  /**
   * Delete datum from the temporary table.
   *
   * @param integer $id the id of datum you want to delete
   *
   * @return bool  The Result of the query
   */
  function deleteDatumTemporaryTable ($id) {
    $ds = CSQLDataSource::get("std");
    $query = "DELETE FROM `search_indexing` WHERE `object_id` = \"$id\";";
    return $ds->exec($query);
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
      $object = new $datum['object_class']();
      $object->load($datum['object_id']);
      //On récupère les champs à indexer.
      $datum_to_index = $object->getFieldsSearch();
    }
    else {
      $datum_to_index["id"]          = $datum['object_id'];
      $datum_to_index["author_id"]   = '';
      $datum_to_index["title"]       = '';
      $datum_to_index["body"]        = '';
      $datum_to_index["date"]        = '';
      $datum_to_index["patient_id"]  = '';
      $datum_to_index["function_id"] = '';
      $datum_to_index["group_id"]    = $datum['group_id'];
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
      case 'store' :
        $type->updateDocument($document);
        break;
      case 'delete':
        $type->deleteDocument($document);
        break;
      case 'merge' :
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
      $typeES = $this->_index->getType($type_name);
      foreach ($_type as $action =>$_data) {
        $documents = array();
        foreach ($_data as $_datum) {
          $documents[] = new Document($_datum['id'], $_datum);
        }
        switch ($action) {
          case 'create':
            $typeES->addDocuments($documents);
            break;
          case 'store' :
            $typeES->updateDocuments($documents);
            break;
          case 'delete':
            $typeES->deleteDocuments($documents);
            break;
          case 'merge' :
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
   * simple search with an operator and words
   *
   * @param string  $operator    'And' or 'Or' default : 'Or'
   * @param string  $words       data
   * @param array   $arrayFilter the array of users where PERM_READ
   * @param integer $start       the begining of the paging
   * @param integer $limit       the interval of the paging
   *
   * @return \Elastica\ResultSet
   */
  function searchQueryString($operator, $words , $arrayFilter, $start = 0, $limit = 30){
    // Define a Query. We want a string query.
    $elasticaQueryString  = new QueryString();

    //'And' or 'Or' default : 'Or'
    $elasticaQueryString->setDefaultOperator($operator);
    $elasticaQueryString->setQuery($words);

    // Create the actual search object with some data.
    $elasticaQuery        = new Query();
    $elasticaQuery->setQuery($elasticaQueryString);

    //add Filter to the query
    $elasticaFilterUser = new Terms();
    $elasticaFilterUser->setTerms('author_id', $arrayFilter);
    $elasticaFilterOr     = new BoolOr();
    $elasticaFilterOr->addFilter($elasticaFilterUser);
    $elasticaQuery->setFilter($elasticaFilterOr);

    //  Pagination
    $elasticaQuery->setFrom($start);    // Where to start?
    $elasticaQuery->setLimit($limit);

    //Search on the index.
    $this->_index = $this->loadIndex();
    return $this->_index->search($elasticaQuery);
  }

  /**
   * Construct query with date informations
   *
   * @param string $words         the words query
   * @param string $date_interval type of interval date
   * @param string $date_deb      begining date
   * @param string $date_fin      final date
   *
   * @return string
   */
  function constructWordsWithDate($words, $date_interval, $date_deb, $date_fin) {
    switch ($date_interval) {
      case 'uniqueDay' :
        if ($date_deb) {
          return $words." date:[".$date_deb." TO ".$date_deb."]";
        }
        else {
          return $words;
        }
        break;
      case 'between' :
        if ($date_deb && $date_fin) {
          return $words." date:[".$date_deb." TO ".$date_fin."]";
        }
        else {
          return $words;
        }
        break;
      case 'since' :
        return $words." date:{".$date_deb." TO *}";
        break;
      default :
        return $words;
    }
  }
}