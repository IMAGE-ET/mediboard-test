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
use Elastica\Snapshot;
use Elastica\Index;

/**
 * Gestion de la création des snapshots des Index ElasticSearch.
 */
class CSearchSnapshot extends CSearch {

  /**
   * @var Snapshot
   */
  public $_snapshot;
  /**
   * @var Index
   */
  public $_index_snapshot;

  /**
   * Création du snapshot.
   *
   */
  public function createSnapshot() {
    $this->createClient();
    $this->_snapshot = new Snapshot($this->_client);
  }

  /**
   * Méthode accesseur du répertoire pour le snapshot
   *
   * @param string $name_repository Le nom du répertoire
   *
   * @return string Le chemin du répertoire
   */
  public function getRepository($name_repository) {
    $response = $this->_snapshot->getRepository($name_repository);

    return $response["settings"]["location"];
  }

  /**
   * Méthode permettant la création du snapshot
   *
   * @param string $repositoryName Le nom du répertoire
   * @param string $location       Le chemin du répertoire
   * @param string $snapshotName   Le nom du snapshot
   *
   * @return bool Création effectuée ou non
   */
  public function snapshot($repositoryName, $location, $snapshotName) {
    // register the repository
    $this->registerRepository($repositoryName, $location);
    if ($this->getSnapshot($repositoryName, $snapshotName)) {
      $this->deleteSnapshot($repositoryName, $snapshotName);
    }
    $response = $this->_snapshot->createSnapshot($repositoryName, $snapshotName, array("indices" => $this->_index->getName()), true);

    return $response->isOk();
  }

  /**
   * Methode permettant la création du répertoire pour le snapshot
   *
   * @param string $name_repository Le nom du répertoire
   * @param string $location        Le chemin du répertoire
   *
   * @return bool Si la création est ok
   */
  public function registerRepository($name_repository, $location) {
    $response = $this->_snapshot->registerRepository($name_repository, "fs", array("location" => $location));

    return $response->isOk();
  }

  /**
   * Accesseur du snapshot
   *
   * @param string $repositoryName Le nom du répertoire du snapshot
   * @param string $snapshotName   Le nom du snapshot
   *
   * @return array
   */
  public function getSnapshot($repositoryName, $snapshotName) {

    return $this->_snapshot->getSnapshot($repositoryName, $snapshotName);
  }

  /**
   * @param string $repositoryName Le nom du répertoire du snapshot
   * @param string $snapshotName   Le nom du snapshot
   *
   * @return \Elastica\Response
   */
  public function deleteSnapshot($repositoryName, $snapshotName) {
    return $this->_snapshot->deleteSnapshot($repositoryName, $snapshotName);
  }

  /**
   * ATTENTION cette méthode supprime l'index ElasticSearch
   * Par prudence il faut mettre les booléens à oui.
   *
   * @param string $repositoryName Le nom du répertoire du snapshot
   * @param string $snapshotName   Le nom du snapshot
   * @param bool   $delete_index   Suppression de l'index [DEFAULT = FALSE]
   * @param bool   $restore        Restoration de l'index à partir du snapshot [DEFAULT = FALSE]
   *
   * @return void
   */
  public function deleteIndexAndRestore($repositoryName, $snapshotName, $delete_index = false, $restore = false) {
    $this->loadIndex();

    if ($delete_index) {
      // delete our index
      $this->deleteIndexSnapshot();
    }

    if ($restore) {
      // restore the index from our snapshot
      $this->_snapshot->restoreSnapshot($repositoryName, $snapshotName, array(), true);
      // mbtrace if it's ok
      $this->_index->refresh();
      $this->_index->optimize();
    }
  }

  /**
   * Supprime l'index
   */
  public function deleteIndexSnapshot() {
    $this->_index->delete();
  }
}
