<?php
/**
 * CIM page representation
 *
 * @package    Tests
 * @subpackage Pages
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    SVN: $Id: CimPage.php $
 * @link       http://www.mediboard.org
 */

require_once "HomePage.php";

class CimPage extends HomePage {

  function __construct($driver) {
    parent::__construct($driver);
    $this->driver->url("/?login=selenium:test");
    $this->driver->url("/index.php?m=dPcim10");
  }
}