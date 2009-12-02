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
    User = {{"utf8_encode"|array_map_recursive:$app->_ref_user->_basic_info|@json}};
    AjaxResponse.onLoaded({{$smarty.get|@json}}, {{$performance|@json}});
  {{/if}}
</script>

{{if !$app->user_id}}
<div class="error">{{tr}}Veuillez vous reconnecter{{/tr}}</div>
{{/if}}