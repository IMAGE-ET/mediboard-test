{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  {{if !$app->user_id}}
    User = {};
    AjaxResponse.onDisconnected();
  {{else}}
    {{assign var=user value=$app->_ref_user}}

    {{if $user->_id}} 
    User = {{"utf8_encode"|array_map_recursive:$user->_basic_info|@json}};
    {{else}}
    User = {};
    {{/if}}
    	
    AjaxResponse.onLoaded({{"utf8_encode"|array_map_recursive:$smarty.get|@json}}, {{$performance|@json}});
  {{/if}}
</script>

{{if !$app->user_id}}
<div class="error">{{tr}}Veuillez vous reconnecter{{/tr}}</div>
{{/if}}