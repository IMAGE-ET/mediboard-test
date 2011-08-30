<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2FieldItem {
	var $field = null;
	var $data = null;
	var $components = array();
	
	function __construct(CHL7v2Field $field, $data) {
		$this->field = $field;
		$this->data = $data;
    $this->components = ($data !== "" ? explode($field->getMessage()->componentSeparator, $data) : array());
	}
}