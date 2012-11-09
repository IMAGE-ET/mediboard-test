<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Interface CHMessageXML
 * Message XML
 */
interface CHMessageXML {
  function getContentNodes();
  
  function handle($ack, CMbObject $object, $data);
}
