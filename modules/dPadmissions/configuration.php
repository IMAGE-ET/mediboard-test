<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage admission
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CConfiguration::register(
  array(
    'CGroups' => array(
      'dPadmissions' => array(
        'CSejour' => array(
          'entree_pre_op_ambu' => 'bool default|0'
        )
      )
    )
  )
);