{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  window.userId = parseInt({{$app->user_id|@json}});
  {{if !$app->user_id}}
    AjaxResponse.onDisconnected();
  {{else}}
    AjaxResponse.onPerformances({{$performance|@json}});
  {{/if}}
</script>

{{if !$app->user_id}}
<div class="error">{{tr}}Veuillez vous reconnecter{{/tr}}</div>
{{/if}}