<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CConfiguration::register(
  array(
    'CGroups' => array(
      'dPccam' => array(
        'codage' => array(
          'rights' => 'enum list|user_rights|self default|user_rights'
        ),
        'associations' => array(
          'mode'  => 'enum list|auto|manual localize default|auto',
          'rules' => array(
            'M'    => 'bool default|1',
            'G'    => 'bool default|1',
            'EA'   => 'bool default|1',
            'EB'   => 'bool default|1',
            'EC'   => 'bool default|1',
            'ED'   => 'bool default|1',
            'EE'   => 'bool default|1',
            'EF'   => 'bool default|1',
            'EG1'  => 'bool default|1',
            'EG2'  => 'bool default|1',
            'EG3'  => 'bool default|1',
            'EG4'  => 'bool default|1',
            'EG5'  => 'bool default|1',
            'EG6'  => 'bool default|1',
            'EG7'  => 'bool default|1',
            'EH'   => 'bool default|1',
            'EI'   => 'bool default|1'
          )
        )
      )
    )
  )
);