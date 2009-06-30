{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $app->user_type == 1 && $app->user_id != $_user->_id && $_user->_login_locked}}
<form name="unlock-{{$_user->_id}}" action="?m={{$m}}&amp;tab={{$tab}}" method="post">
<input type="hidden" name="dosql" value="do_user_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$_user->_id}}" />
<input type="hidden" name="user_login_errors" value="0" />

<button type="submit" class="tick">
  {{tr}}Débloquer{{/tr}}
</button>
  
</form>
{{/if}}
