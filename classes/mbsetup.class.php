<?php /* $Id: mbobject.class.php 31 2006-05-05 09:55:35Z MyttO $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
 */

class CMbSetup {
  function getVersionOf($moduleName) {
    $sql = "SELECT mod_version" .
        "\nFROM modules" .
        "\nWHERE mod_name = '$moduleName'";
    return db_loadResult($sql);
  }
}

?>