{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(function() {
    getForm('Edit-Log').delete.focus();
  })
</script>

<form name="Edit-Log" action="?m={{$m}}" method="post">

  {{mb_class object=$log}}
  {{mb_key   object=$log}}

  <table class="form">

  <tr>
    <th>{{mb_label object=$log field=datetime}}</th>
    <td>{{mb_value object=$log field=datetime}}</td>
    <th>{{mb_label object=$log field=_module}}</th>
    <td>{{mb_value object=$log field=_module}}</td>
    <th>{{mb_label object=$log field=user_id}}</th>
    <td>{{mb_value object=$log field=user_id}}</td>
  </tr>

  <tr>
    <th>{{mb_label object=$log field=duration}}</th>
    <td>{{mb_value object=$log field=duration}}</td>
    <th>{{mb_label object=$log field=_action}}</th>
    <td>{{mb_value object=$log field=_action}}</td>
    <th>{{mb_label object=$log field=server_addr}}</th>
    <td>{{mb_value object=$log field=server_addr}}</td>
  </tr>

  <tr>
    <td colspan="2" style="width: 33%;">{{mb_label object=$log field=query_params_get}}</td>
    <td colspan="2" style="width: 33%;">{{mb_label object=$log field=query_params_post}}</td>
    <td colspan="2" style="width: 33%;">{{mb_label object=$log field=session_data}}</td>
  </tr>
  <tr>
    <td colspan="2">
      <div style="height: 400px; overflow-y: auto">
        {{mb_value object=$log field=_query_params_get export=true}}
      </div>
    </td>

    <td colspan="2">
      <div style="height: 400px; overflow-y: auto">
        {{mb_value object=$log field=_query_params_post export=true}}
      </div>
    </td>

    <td colspan="2">
      <div style="height: 400px; overflow-y: auto">
        {{mb_value object=$log field=_session_data export=true}}
      </div>
    </td>

  </tr>

  <tr>
    <td class="button" colspan="6">
      <button name="delete" type="button" class="trash" onclick="LongRequestLog.confirmDeletion(this.form);">
        {{tr}}Delete{{/tr}}
      </button>
      <a class="button search" href="{{$log->_link}}" target="_blank">{{tr}}Hyperlink{{/tr}}</a>
    </td>
  </tr>
</table>

</form>