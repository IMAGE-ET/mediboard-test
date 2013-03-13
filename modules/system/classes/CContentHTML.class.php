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

class CContentHTML extends CMbObject {
  // DB Table key
  public $content_id;

  // DB Fields
  public $content;

  // Form fields
  public $_list_classes;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'content_html';
    $spec->key   = 'content_id';
    return $spec;
  }

  function getProps() { 
    $props = parent::getProps();
    $specs["_list_classes"] = "enum list|".implode("|", array_keys(CCompteRendu::getTemplatedClasses()));
    $props["content"] = "html helped|_list_classes";
    return $props;
  }

  function check() {
    if ($this->fieldModified("content", "")) {
      return "CContentHTML-failed-emptytext";
    }

    return parent::check();
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["compte_rendus"] = "CCompteRendu content_id";
    $backProps["usermail_html"]     = "CUserMail text_html_id";
    return $backProps;
  }
}
