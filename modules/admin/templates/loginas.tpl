{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if (!$conf.admin.LDAP.ldap_connection || $conf.admin.LDAP.allow_login_as_admin) && $app->user_type == 1 && ($app->user_id != $loginas_user->_id) && !$loginas_user->template}}
<form name="loginas-{{$loginas_user->_id}}" action="?" method="post">
  <input type="hidden" name="redirect" value="?"/>
  <input type="hidden" name="login" value="ok" />
  <input type="hidden" name="loginas" value="{{$loginas_user->user_username}}" />
  <button type="submit" class="tick compact">
    {{tr}}Substitute{{/tr}}
  </button>
</form>
{{/if}}
