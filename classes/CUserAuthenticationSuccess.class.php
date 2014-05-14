<?php

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * User authentication success exception
 */
class CUserAuthenticationSuccess extends CMbException {
  /** @var int User ID to authenticate */
  public $user_id;

  /** @var string Authentication method */
  public $auth_method;

  /** @var bool Restrict to only one script */
  public $restricted = true;
}
