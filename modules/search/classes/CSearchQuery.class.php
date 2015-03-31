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

CAppUI::requireLibraryFile("Elastica/autoloader", false);

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
class CSearchQuery extends CSearch{

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

  /**
   * Method to delete temporary data
   *
   * @param array $array array of data
   *
   * @return resource
   */
  function deleteDataTemporaryTable($array) {
    $ds    = CSQLDataSource::get("std");
    $query = 'DELETE FROM `search_indexing` WHERE `search_indexing_id` ';
    $query .= $ds->prepareIn($array);

    return $ds->exec($query);
  }

  /**
   * Method to delete temporary data
   *
   * @param integer $id the id of the datum
   *
   * @return resource
   */
  function deleteDatumTemporaryTable($id) {
    $ds    = CSQLDataSource::get("std");
    $query = $ds->prepare("DELETE FROM `search_indexing` WHERE `object_id` = ?;", $id);

    return $ds->exec($query);
  }

  /**
   * simple search with an operator and words
   *
   * @param string  $words         data
   * @param integer $start         the begining of the paging
   * @param integer $limit         the interval of the paging
   * @param bool    $aggregation   parameter the search to be aggregated or not.
   * @param integer $sejour_id     the id of the sejour
   * @param string  $specific_user the ids of users selected
   * @param bool    $details       details of query
   * @param string  $date          date of query
   * @param bool    $fuzzy_search  fuzzy the query
   *
   * @return \Elastica\Query
   */
  function searchQueryString($words, $start = 0, $limit = 30, $aggregation = false, $sejour_id = null, $specific_user =null, $details=null, $date=null, $fuzzy_search =null) {

    // Initialisation des mots pour la recherche
    $prats = $this->constructWordsWithPrat($specific_user, $sejour_id);
    $sejour = $this->constructWordsWithSejour($sejour_id);
    $words = CmbString::normalizeUtf8(stripcslashes($words));
    $query_bool = new Elastica\Query\Bool();

    //query date
    if ($date) {
      $query_date = new Elastica\Query\QueryString();
      $query_date->setQuery($date);
      $query_date->setDefaultOperator("and");
      $query_bool->addMust($query_date);
    }

    //query mots
    if ($words) {
      if ($fuzzy_search) {
        $query_fuzzy = new Elastica\Query\FuzzyLikeThis();
        $query_fuzzy->addFields(array("body", "title"));
        $query_fuzzy->setLikeText($words);
        $query_fuzzy->setMinSimilarity(0.3);
        $query_fuzzy->setMaxQueryTerms(3);
        $query_bool->addMust($query_fuzzy);
      }
      else {
        $query_words = new Elastica\Query\QueryString($words);
        $query_words->setFields(array("body", "title"));
        $query_words->setDefaultOperator("and");
        $query_bool->addMust($query_words);
      }
    }

    //query détails
    if ($details) {
      $query_details = new Elastica\Query\QueryString();
      $query_details->setQuery($details);
      $query_details->setDefaultOperator("and");
      $query_bool->addMust($query_details);
    }
    else {
      // query prat_id
      $query_prat = new Elastica\Query\QueryString();
      $query_prat->setQuery("prat_id:($prats)");
      $query_prat->setDefaultField("prat_id");
      $query_bool->addMust($query_prat);

      //query sejour
      if ($sejour) {
        $query_sejour = new Elastica\Query\QueryString();
        $query_sejour->setQuery($sejour);
        $query_sejour->setDefaultOperator("and");
        $query_bool->addMust($query_sejour);
      }
    }
    $query = new Query($query_bool);
    //create aggregation
    if ($aggregation && $aggregation != "by_type" ) {
      // on aggrège d'abord par class d'object référents
      // on effectue un sous aggrégation par id des objets référents.
      $agg_by_class = new CSearchAggregation("Terms", "ref_class", "object_ref_class", 10);
      $sub_agg_by_id = new CSearchAggregation("Terms", "sub_ref_id", "object_ref_id", 100);
      $sub_agg_by_type = new CSearchAggregation("Terms", "sub_ref_type", "_type", 100);
      $sub_agg_by_id->_aggregation->addAggregation($sub_agg_by_type->_aggregation);
      $agg_by_class->_aggregation->addAggregation($sub_agg_by_id->_aggregation);
      $query->addAggregation($agg_by_class->_aggregation);
    }
    else if (!$aggregation) {
      //  Pagination
      $query->setFrom($start);    // Where to start
      $query->setLimit($limit);
    }
    else {
      $agg_by_type = new CSearchAggregation("Terms", "ref_type", "_type", 100);
      $query->addAggregation($agg_by_type->_aggregation);
    }

    //Highlight
    if ($words) {
      $query->setHighlight(
        array(
          "pre_tags" => array(" <em> <strong> "),
          "post_tags" => array(" </strong> </em>"),
          "fields" => array(
            "body" => array(
              "fragment_size" => 50,
              "number_of_fragments" => 3,
              "highlight_query" => array(
                "bool" => array(
                  "must" => array(
                    "match"=> array(
                      "body"=> array(
                        "query"=> $words
                      )
                    )
                  ),
                  "minimum_should_match" => 1
                )
              )
            )
          )
        )
      );
    }
    return $query;
  }

  /**
   * simple search
   *
   * @param string  $words     data
   * @param integer $start     the begining of the paging
   * @param integer $limit     the interval of the paging
   * @param integer $sejour_id the id of the sejour
   *
   * @return \Elastica\Query
   */
  function searchQueryStringManual($words, $start, $limit, $sejour_id) {
    $words = CmbString::normalizeUtf8(stripcslashes($words));
    $sejour = $this->constructWordsWithSejour($sejour_id);
    $query_bool = new Elastica\Query\Bool();

    // Query words
    $query_words = new Elastica\Query\QueryString();
    $query_words->setQuery($words);
    $query_words->setFields(array("body", "title"));
    $query_words->setDefaultOperator("and");
    $query_bool->addMust($query_words);

    // Query Séjour
    $query_sejour = new Elastica\Query\QueryString();
    $query_sejour->setQuery($sejour);
    $query_sejour->setDefaultOperator("and");
    $query_bool->addMust($query_sejour);

    $query = new Query($query_bool);

    //Pagination
    $query->setFrom($start);    // Where to start
    $query->setLimit($limit);

    //Highlight
    if ($words) {
      $query->setHighlight(
        array(
          "pre_tags" => array(" <em> <strong> "),
          "post_tags" => array(" </strong> </em>"),
          "fields" => array(
            "body" => array(
              "fragment_size" => 50,
              "number_of_fragments" => 3,
              "highlight_query" => array(
                "bool" => array(
                  "must" => array(
                    "match"=> array(
                      "body"=> array(
                        "query"=> $words
                      )
                    )
                  ),
                  "minimum_should_match" => 1
                )
              )
            )
          )
        )
      );
    }
    return $query;
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
    if ($_date) {
      $_date = CMbDT::format($_date, "%Y/%m/%d");
      return " date:[".$_date." TO ".$_date."]";
    }
    else {
      $_min_date = ($_min_date) ? CMbDT::format($_min_date, "%Y/%m/%d") : "*";
      $_max_date = ($_max_date) ? CMbDT::format($_max_date, "%Y/%m/%d") : "*";
      return " date:[".$_min_date." TO ".$_max_date."]";
    }
  }

  /**
   * * Construct query with prat informations
   *
   * @param string $specific_user the id of the specific user
   * @param string $sejour_id     the id of the sejour
   *
   * @return string
   */
  function constructWordsWithPrat($specific_user, $sejour_id) {
    $users_id = array();
    if (!$specific_user) {
      $user = new CMediusers();
      $users = ($sejour_id) ? $user->loadPraticiens(PERM_READ) : $user->loadPraticiens(PERM_EDIT);
      foreach ($users as $_user) {
        $users_id[] = $_user->_id;
      }
      $user_req = implode(' ', $users_id);
    }
    else {
      $users_id = explode('|', $specific_user);
      $user_req = implode(' ', $users_id);
    }

    $ids_conf = str_replace(";", " || ", CAppUI::conf("search ids_search"));
    if ($ids_conf) {
      $user_req .= " || $ids_conf";
    }

    return $user_req;
  }

  /**
   * Construct query with sejour informations (PMSI)
   *
   * @param string $sejour_id the id of the sejour
   *
   * @return string
   */
  function constructWordsWithSejour($sejour_id) {
    if ($sejour_id) {
      return " object_ref_class:(CSejour) object_ref_id:(".$sejour_id.")";
    }

    return null;
  }

  /**
   * Load the aggregation and format array to display in the search template
   *
   * @param array $aggregation The aggregation
   *
   * @return array
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
   * @param array $objects_refs the array of ref objects
   *
   * @return void
   */
  function loadObjectRef ($objects_refs) {

    foreach ($objects_refs as $_object_ref) {
      $_object = $_object_ref['object'];
      if ($_object instanceof CMbObject) {
        if ($_object instanceof CConsultAnesth) {
          $consult = $_object->loadRefConsultation();
          $consult->loadRefPraticien();
          $consult->loadRelPatient();
          $consult->loadRefPlageConsult();
          $_object->loadRefSejour();
          if ($_object->_ref_sejour->_id) {
            $_object->_ref_sejour->loadNDA();
          }
        }
        else {
          if ($_object instanceof CConsultation) {
            $_object->loadRefPraticien();
            $_object->loadRelPatient();
            $_object->loadRefPlageConsult();
            $_object->loadRefSejour();
            if ($_object->_ref_sejour->_id) {
              $_object->_ref_sejour->loadNDA();
            }

          }
          if ($_object instanceof CSejour) {
            $_object->loadRefPraticien();
            $_object->loadRelPatient();
            $_object->loadNDA();
          }
          else {
            $_object->loadRefPraticien();
            $_object->loadRelPatient();
            $_object->loadRefSejour();
            if ($_object->_ref_sejour->_id) {
              $_object->_ref_sejour->loadNDA();
            }
          }
        }
      }
    }
  }

  /**
   * Method to load cartos infos
   *
   * @param CSearch $c_search the csearch
   * @param array   $aggreg   the aggreg
   *
   * @return array
   */
  function loadCartoInfos($c_search, $aggreg) {
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
    $name = CAppUI::conf("search index_name");

    $result['stats']["search"]['total']        = $stats["indices"][$name]["primaries"]["search"]["query_total"];
    $result['stats']["search"]['average_time'] = $stats["indices"][$name]["primaries"]["search"]["query_time_in_millis"];

    // récupération du nombre de docs "indexés",  "à indexer" et récupération des types d'éléments restant à indexer.
    $result['nbDocs_indexed']          = $index->count();;
    $result['nbdocs_to_index']         = $search->countList();;

    $order = "`object_class`, COUNT(`object_class`) AS `total`";
    $result['nbdocs_to_index_by_type'] = $search->countMultipleList(null, null, "object_class", null, $order);

    // récupération du statut de la connexion et du cluster
    $result['status']    = $cluster->getHealth()->getStatus();
    $result['connexion'] = $c_search->_client->hasConnection();

    // récupération des données de l'agregation
    $aggreg = $aggreg->getAggregation("ref_type");
    $result["aggregation"] = $aggreg["buckets"];

    return $result;
  }

  /**
   * Method to count by type document in index
   *
   * @return array
   */
  function aggregCartoCountByType () {
    $query = new Query();

    $agg_by_type = new CSearchAggregation("Terms", "ref_type", "_type", 100);
    $query->addAggregation($agg_by_type->_aggregation);

    return $query;
  }

  /**
   * Query to search auto
   *
   * @param CSearchThesaurusEntry $favori The favori
   * @param CSejour               $sejour The sejour
   *
   * @return Query
   */
  function querySearchAuto ($favori, $sejour) {
    $query_bool = new Elastica\Query\Bool();

    // query des séjours
    $query_sejour = new Elastica\Query\QueryString();
    $query_sejour->setQuery($this->constructWordsWithSejour($sejour->_id));
    $query_sejour->setDefaultOperator("and");
    $query_bool->addMust($query_sejour);

    // query du favoris
    $query_words = new Elastica\Query\QueryString();
    $query_words->setQuery($this->normalizeEncoding($favori->entry));
    $query_words->setFields(array("body", "title"));
    $query_words->setDefaultOperator("and");
    $query_bool->addMust($query_words);


    $query = new Query($query_bool);

    //  Pagination
    $query->setFrom(0);    // Where to start
    $query->setLimit(30);

    //Highlight
    $query->setHighlight(
      array(
        "pre_tags" => array(" <em> <strong> "),
        "post_tags" => array(" </strong> </em>"),
        "fields" => array(
          "body" => array(
            "fragment_size" => 50,
            "number_of_fragments" => 3,
            "highlight_query" => array(
              "bool" => array(
                "must" => array(
                  "match"=> array(
                    "body"=> array(
                      "query"=> $this->normalizeEncoding($favori->entry)
                    )
                  )
                ),
                "minimum_should_match" => 1
              )
            )
          )
        )
      )
    );

    return $query;
  }
}
