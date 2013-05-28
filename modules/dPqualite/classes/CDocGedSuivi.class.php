<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Qualite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Suivi des documents qualité
 * Class CDocGedSuivi
 */
class CDocGedSuivi extends CMbObject {
  // DB Table key
  public $doc_ged_suivi_id;
    
  // DB Fields
  public $user_id;
  public $doc_ged_id;
  public $file_id;
  public $remarques;
  public $date;
  public $actif;
  public $etat;
  
  // Object References
  public $_ref_proc;
  public $_ref_user;
  /** @var  CFile */
  public $_ref_file;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'doc_ged_suivi';
    $spec->key   = 'doc_ged_suivi_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $specs = parent::getProps();
    $specs["user_id"]    = "ref notNull class|CMediusers";
    $specs["doc_ged_id"] = "ref notNull class|CDocGed";
    $specs["file_id"]    = "ref class|CFile";
    $specs["remarques"]  = "text notNull";
    $specs["etat"]       = "enum notNull list|0|16|32|48|64";
    $specs["date"]       = "dateTime";
    $specs["actif"]      = "bool";
    return $specs;
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    // Forward references
    $this->_ref_proc = new CDocGed;
    $this->_ref_proc->load($this->doc_ged_id);
    $this->_ref_user = new CMediusers;
    $this->_ref_user->load($this->user_id);
  }
  
  function loadProcComplete(){
    // Chargement des procédures Terminées
  }
  
  function loadHistory($doc_ged_id = null){
    // Chargement de l'historique complet pour une procédure
    if (!$doc_ged_id) {
      $doc_ged_id = $this->doc_ged_suivi_id;
    }  
  }
  
  function loadFile(){
    $this->_ref_file = new CFile();
    if ($this->file_id) {
      $this->_ref_file->load($this->file_id);
    }
  }
  
  function delete_suivi($doc_ged_id,$lastactif_id){
    $supprSuivi = new CDocGedSuivi;
    $where = array();
    $where["doc_ged_id"] = "= '$doc_ged_id'";
    if ($lastactif_id) {
      $where["doc_ged_suivi_id"] = "> '$lastactif_id'";
    }
    $supprSuivi = $supprSuivi->loadList($where);
    // Supression de chacun des enregistrement
    foreach ($supprSuivi as $keySuppr=>$currSuppr) {
      $supprSuivi[$keySuppr]->delete();
    }
  }

  /**
   * @see parent::delete()
   */
  function delete() {
    // Suppression du fichier correspondant
    if ($this->file_id) {
      $this->loadFile();
      if ($this->_ref_file->file_id) {
        $this->_ref_file->delete();
      }
    }

    //suppression de la doc
    return parent::delete();
  }
}
