<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Contenu HTML
 */
class CContentHTML extends CMbObject {
  // DB Table key
  public $content_id;

  // DB Fields
  public $content;
  public $last_modified;

  // Form fields
  public $_list_classes;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_html';
    $spec->key   = 'content_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() { 
    $props = parent::getProps();
    $props["_list_classes"] = "enum list|".implode("|", array_keys(CCompteRendu::getTemplatedClasses()));
    $props["content"] = "html helped|_list_classes";
    $props["last_modified"] = "dateTime";
    return $props;
  }

  /**
   * @see parent::check()
   */
  function check() {
    if ($this->fieldModified("content", "")) {
      return "CContentHTML-failed-emptytext";
    }

    return parent::check();
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["compte_rendus"] = "CCompteRendu content_id";
    $backProps["usermail_html"]     = "CUserMail text_html_id";
    return $backProps;
  }

  /**
   * @see parent::store()
   */
  function store() {
    if ($this->fieldModified("content") || !$this->last_modified) {
      $this->last_modified = CMbDT::dateTime();
    }

    return parent::store();
  }
}