<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupsoins extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'soins';

    $this->makeRevision('all');

    $this->mod_version = '0.1';
  }
}

?>