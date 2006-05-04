<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

require_once( $AppUI->getSystemClass ('mbobject' ) );

/**
 * The dPcim10 Class
 */
class CFavoricim10 extends CMbObject {
	var $favoris_id = NULL;
	var $favoris_code = NULL;
	var $favoris_user = NULL;

	function CFavoricim10() {
		$this->CMbObject( 'cim10favoris', 'favoris_id' );

    $this->_props["favoris_code"] = "str|notNull";
    $this->_props["favoris_user"] = "ref|notNull";
	}

	function delete() {
		$sql = "DELETE FROM cim10favoris" .
        "\nWHERE favoris_id = '$this->favoris_id'";
		if (!db_exec( $sql )) {
			return db_error();
		} else {
			return null;
		}
	}
	
	function store() {
		$sql = "SELECT *" .
        "\nFROM cim10favoris" .
        "\nWHERE favoris_code = '$this->favoris_code'" .
        "\nAND favoris_user = '$this->favoris_user'";
		$issingle = db_loadList( $sql );
		if(sizeof($issingle) == 0) {
			$sql = "INSERT" .
          "\nINTO cim10favoris(favoris_code, favoris_user)" .
          "\nVALUES('$this->favoris_code', '$this->favoris_user')";
			if (!db_exec( $sql )) {
				return db_error();
			} else {
				return null;
			}
		}
		else {
			return "Favoris déja existant";
		}
		
	}
}
?>