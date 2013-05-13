<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CActeSSR extends CMbObject {
  // DB Fields
  public $evenement_ssr_id;
  public $administration_id;
  public $sejour_id;
  public $code;
  
  /** @var CAdministration */
  public $_ref_administration;

  /** @var CEvenementSSR */
  public $_ref_evenement_ssr;

  /** @var CSejour */
  public $_ref_sejour;

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["evenement_ssr_id"]  = "ref class|CEvenementSSR cascade";
    $props["administration_id"] = "ref class|CAdministration cascade";
    $props["sejour_id"]         = "ref class|CSejour";
    $props["code"]              = "str notNull show|0";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->code;
  }

  /**
   * Chargement l'évenement associé
   *
   * @return CEvenementSSR
   */
  function loadRefEvenementSSR(){
    return $this->_ref_evenement_ssr = $this->loadFwdRef("evenement_ssr_id", true);
  }

  /**
   * Chargement de l'administration associée
   *
   * @return CAdministration
   */
  function loadRefAdministration(){
    return $this->_ref_administration = $this->loadFwdRef("administration_id", true);
  }

  /**
   * Chargement du séjour associé
   *
   * @return CSejour
   */
  function loadRefSejour(){
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
}
