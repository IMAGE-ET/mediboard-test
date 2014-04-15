{{*
 * $Id$
 *  
 * @category SalleOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if "dPsalleOp COperation password_sortie"|conf:"CGroups-$g" && $app->_ref_user->isAnesth()}}
  {{mb_include template=inc_pref spec=bool var=autosigne_sortie}}
{{/if}}