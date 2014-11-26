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

CAppUI::requireLibraryFile("elastica/autoloader", false);

use Elastica\Query;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Filter\BoolOr;
use Elastica\Query\QueryString;
use Elastica\Request;

/**
 * Class CSearchQuery
 * Manage Elastica Library in order to index documents
 */
class CSearchQuery {

  /**
   * Get data from the temporary table.
   *
   * @param string $limit        the number of data you want to get
   * @param string $object_class the class of data you want to get [OPTIONNAL]
   *
   * @return array  The Result of the query
   */
  function getDataTemporaryTable($limit, $object_class = null) {
    $ds    = CSQLDataSource::get("std");
    $query = ($object_class) ?
      "SELECT * FROM `search_indexing` WHERE `object_class` = '$object_class' ORDER BY `type`, `search_indexing_id` LIMIT $limit"
      :
      "SELECT * FROM `search_indexing` ORDER BY `object_class` ,
                                                CASE `type`
                                                  WHEN 'create' THEN '1_create'
                                                  WHEN 'store'  THEN '2_store'
                                                  WHEN 'delete' THEN '3_delete'
                                                  END,
                                                `search_indexing_id`
                                                LIMIT $limit";

    return $ds->loadList($query);
  }

  function deleteDataTemporaryTable($array) {
    $ds    = CSQLDataSource::get("std");
    $query = 'DELETE FROM `search_indexing` WHERE `search_indexing_id` ';
    $query .= $ds->prepareIn($array);

    return $ds->exec($query);
  }

  function deleteDatumTemporaryTable($id) {
    $ds    = CSQLDataSource::get("std");
    $query = "DELETE FROM `search_indexing` WHERE `object_id` = \"$id\";";

    return $ds->exec($query);
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
   * @return \Elastica\Query
   */
  function searchQueryString($operator, $words, $start = 0, $limit = 30, $names_types = null, $aggregation = false) {

    $words = CmbString::normalizeUtf8($words);
    // Define a Query. We want a string query.
    $elasticaQueryString  = new QueryString();

    //'And' or 'Or' default : 'Or'
    $elasticaQueryString->setDefaultOperator($operator);
    //$elasticaQueryString->setAnalyzer("custom_search_analyzer");
    $elasticaQueryString->setQuery($words);

    // Create the actual search object with some data.
    $elasticaQuery        = new Query();
    $elasticaQuery->setQuery($elasticaQueryString);

    //create aggregation
    if ($aggregation) {
      // on aggrège d'abord par class d'object référents
      // on effectue un sous aggrégation par id des objets référents.
      $agg_by_class = new CSearchAggregation("Terms", "ref_class", "object_ref_class", 10);
      $sub_agg_by_id = new CSearchAggregation("Terms", "sub_ref_id", "object_ref_id", 100);
      $sub_agg_by_type = new CSearchAggregation("Terms", "sub_ref_type", "_type", 10);
      $sub_agg_by_id->_aggregation->addAggregation($sub_agg_by_type->_aggregation);
      $agg_by_class->_aggregation->addAggregation($sub_agg_by_id->_aggregation);
      $elasticaQuery->addAggregation($agg_by_class->_aggregation);

      // Nuage de mots clés pour recherche automatique.
//      $agg_cloud = new CSearchAggregation("Terms", "cloud", "body", 400);
//      $agg_cloud->_aggregation->setMinimumDocumentCount(10);
//      $agg_cloud->_aggregation->setExclude("(\\b\\w{1,4}\\b|\\d*)", "CANON_EQ|CASE_INSENSITIVE");
//      $elasticaQuery->addAggregation($agg_cloud->_aggregation);
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
          "number_of_fragments" => 3,
        )),
      ));

    return $elasticaQuery;
  }

  /**
   * Construct query with date informations
   *
   * @param string $words     the words query
   * @param string $_date     type of interval date
   * @param string $_min_date begining date
   * @param string $_max_date final date
   *
   * @return string
   */
  function constructWordsWithDate($words, $_date, $_min_date, $_max_date) {
    if ($_date) {
      $_date = CMbDT::format($_date, "%Y/%m/%d");
      $words .= " date:[".$_date." TO ".$_date."]";
    }
    else {
      $_min_date = ($_min_date) ? CMbDT::format($_min_date, "%Y/%m/%d") : "*";
      $_max_date = ($_max_date) ? CMbDT::format($_max_date, "%Y/%m/%d") : "*";

      $words .= " date:[".$_min_date." TO ".$_max_date."]";
    }
    return $words;
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
    $users_id = array();
    if (!$specific_user) {
      $user = new CMediusers();
      if ($sejour_id) {
        $users = $user->loadPraticiens(PERM_READ);
      }
      else {
        $users = $user->loadPraticiens(PERM_EDIT);
      }

      foreach ($users as $_user) {
        $users_id[] = $_user->_id;
      }
      $user_req = implode(' || ', $users_id);
      $words    = $words . " prat_id:(" . $user_req . ")";
    }
    else {
      $users_id = explode('|', $specific_user);
      $user_req = implode(' || ', $users_id);
      $words    = $words . " prat_id:(" .$user_req . ")";
    }

    return $words;
  }

  /**
   * Construct query with sejour informations (PMSI)
   *
   * @param string $words  the words query
   * @param string $sejour_id the id of the sejour
   *
   * @return string
   */
  function constructWordsWithSejour($words, $sejour_id) {

    if ($sejour_id) {
      $words = $words." object_ref_class:(CSejour) object_ref_id:(".$sejour_id.")";
    }

    return $words;
  }

  /**
   * Load the aggregation and format array to display in the search template
   *
   * @param $aggregation
   */
  function loadAggregationObject ($aggregation) {
    $objects_refs = array ();
    $agg_ref_class     = $aggregation['ref_class']['buckets'];
    foreach ($agg_ref_class as $_agg) {
      if ($_agg['key'] == "cconsult" || $_agg['key'] == "cconsultation") {
        $_agg['key'] = "CConsultation";
      }
      if ($_agg['key'] == "coper" || $_agg['key'] == "coperation") {
        $_agg['key'] = "COperation";
      }
      if ($_agg['key'] == "cconsultanesth") {
        $_agg['key'] = "CConsultAnesth";
      }
      $name_object = $_agg['key'];
      $agg_ref_id  = $_agg['sub_ref_id']['buckets'];

      foreach ($agg_ref_id as $__agg) {
        $id_object                          = $__agg['key'];
        $objects_refs[$id_object]["object"] = CMbObject::loadFromGuid("$name_object-$id_object");
        $agg_ref_type                       = $__agg['sub_ref_type']['buckets'];

        foreach ($agg_ref_type as $_key => $___agg) {
          $key                                              = $___agg['key'];
          $count                                            = $___agg['doc_count'];
          $objects_refs[$id_object]['type'][$_key]['key']   = $key;
          $objects_refs[$id_object]['type'][$_key]['count'] = $count;
        }
      }
    }
    // chargement des contextes référents
    $this->loadObjectRef($objects_refs);

    return $objects_refs;
  }

  /**
   * Load objects for the aggregation view.
   *
   * @param array $objects_refs
   *
   * @return void
   */
  function loadObjectRef ($objects_refs) {

    foreach ($objects_refs as $_object_ref) {
      if ($_object_ref['object'] instanceof CMbObject) {
        if ($_object_ref['object'] instanceof CConsultAnesth) {
          $_object_ref['object']->loadRefConsultation()->loadRefPraticien();
          $_object_ref['object']->loadRefConsultation()->loadRelPatient();
          $_object_ref['object']->loadRefConsultation()->loadRefPlageConsult();
          $_object_ref['object']->loadRefSejour();
          if ($_object_ref['object']->_ref_sejour->_id) {
            $_object_ref['object']->_ref_sejour->loadNDA();
          }
        }
        else {
          if ($_object_ref['object'] instanceof CConsultation) {
            $_object_ref['object']->loadRefPraticien();
            $_object_ref['object']->loadRelPatient();
            $_object_ref['object']->loadRefPlageConsult();
            $_object_ref['object']->loadRefSejour();
            if ($_object_ref['object']->_ref_sejour->_id) {
              $_object_ref['object']->_ref_sejour->loadNDA();
            }

          }
          if ($_object_ref['object'] instanceof CSejour) {
            $_object_ref['object']->loadRefPraticien();
            $_object_ref['object']->loadRelPatient();
            $_object_ref['object']->loadNDA();
          }
          else {
            $_object_ref['object']->loadRefPraticien();
            $_object_ref['object']->loadRelPatient();
            $_object_ref['object']->loadRefSejour();
            if ($_object_ref['object']->_ref_sejour->_id) {
              $_object_ref['object']->_ref_sejour->loadNDA();
            }
          }
        }
      }
    }
  }

  /**
   *
   * @param CSearch $c_search
   * @return array
   */
  function loadCartoInfos($c_search) {
    $result = array();
    $search = new CSearchIndexing();
    // récupération de l'index, cluster
    $index      = $c_search->loadIndex();
    $cluster    = $c_search->_client->getCluster();

    // statistiques du cluster
    $path = '_cluster/stats';
    $response = $c_search->_client->request($path, Request::GET);
    $data = $response->getData();
    $result['stats']['cluster']["nbIndex"] = $data["indices"]["count"];
    $result['stats']['cluster']["nbDocsTotal"] = $data["indices"]["docs"]["count"];

    // récupération du mapping et du name_index
    $result['mapping']      = $index->getMapping();
    $result['mappingjson']  = json_encode($result['mapping'] );
    $result['name_index']   = $index->getName();

    // récupération de la taille totale des indexes et de statistiques
    $stats = $index->getStats()->getData();
    $result['size'] = CMbString::toDecaBinary($stats ["_all"]["primaries"]["store"]["size_in_bytes"]);

    // récupération de l'état des shards
    $result['stats']['shards']["total"]      = $stats["_shards"]['total'];
    $result['stats']['shards']["successful"] = $stats["_shards"]['successful'];
    $result['stats']['shards']["failed"]     = $stats["_shards"]['failed'];

    // récupération de statistiques
    $name = CAppUI::conf("db std dbname");
    $result['stats']["search"]['total']        = $stats["indices"][$name]["primaries"]["search"]["query_total"];
    $result['stats']["search"]['average_time'] = $stats["indices"][$name]["primaries"]["search"]["query_time_in_millis"];

    // récupération du nombre de docs "indexés",  "à indexer" et récupération des types d'éléments restant à indexer.
    $result['nbDocs_indexed']          = $index->count();;
    $result['nbdocs_to_index']         = $search->countList();;
    $result['nbdocs_to_index_by_type'] = $search->countMultipleList(null, null, "object_class", null, "`object_class`, COUNT(`object_class`) AS `total`");

    // récupération du statut de la connexion et du cluster
    $result['status']    = $cluster->getHealth()->getStatus();
    $result['connexion'] = $c_search->_client->hasConnection();

    return $result;
  }

}
