<?php /** $Id$ **/

/**
 * @package    Mediboard
 * @subpackage messagerie
 * @version    $Revision$
 * @author     SARL OpenXtrem
 */

// Open a contentHTML from an id

CCanDo::checkRead();

$html_id = CValue::get("html_id", 0);
if (!$html_id) {
  $content_html= new CContentHTML();
  $content_html->_id = $html_id;
  $content_html->loadMatchingObject();

  echo $content_html->content;
}