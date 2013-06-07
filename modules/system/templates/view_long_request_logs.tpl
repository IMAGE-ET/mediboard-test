{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 *}}

<script type="text/javascript">
  function changePage(start) {
    $V(getForm("filterFrm").start, start);
  }
</script>

<form name="filterFrm" action="?m={{$m}}" method="get" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="start" value="{{$start|default:0}}" onchange="this.form.submit()" />

  <table class="form">
    <tr>
      <th>{{mb_label object=$filter field=user_id}}</th>
      <td>
        <select name="user_id" class="ref" onchange='this.form.start.value = 0'>
          <option value="">&mdash; Tous les utilisateurs</option>
          {{foreach from=$user_list item=curr_user}}
            <option value="{{$curr_user->user_id}}" {{if $curr_user->user_id == $filter->user_id}}selected="selected"{{/if}} >
              {{$curr_user}}
            </option>
          {{/foreach}}
        </select>
      </td>

      <th>{{mb_label object=$filter field="_date_min"}}</th>
      <td>{{mb_field object=$filter field="_date_min" form="filterFrm" register=true onchange='this.form.start.value = 0'}}</td>

      <th>{{mb_label object=$filter field="_date_max"}}</th>
      <td>{{mb_field object=$filter field="_date_max" form="filterFrm" register=true onchange='this.form.start.value = 0'}} </td>
    </tr>

    <tr>
      <td class="button" colspan="10">
        <button class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{mb_include module=system template=inc_pagination total=$list_count current=$start step=50 change_page='changePage' jumper=1}}


<table class="tbl">
  <tr>
    <th>{{mb_title class=CLongRequestLog field=datetime}}</th>
    <th>{{mb_title class=CLongRequestLog field=duration}}</th>
    <th>{{mb_title class=CLongRequestLog field=server_addr}}</th>
    <th>{{mb_title class=CLongRequestLog field=query_params_get}}</th>
    <th>{{mb_title class=CLongRequestLog field=query_params_post}}</th>
    <th>{{mb_title class=CLongRequestLog field=session_data}}</th>
    <th>{{mb_title class=CLongRequestLog field=user_id}}</th>
    <th>{{tr}}Hyperlink{{/tr}}</th>
  </tr>

  {{foreach from=$logs item=_log}}
    <tbody class="hoverable">
    <tr>
      <td>{{$_log->datetime}}</td>
      <td>{{$_log->duration}}</td>
      <td>{{$_log->server_addr}}</td>
      <td>
        <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-params-get-{{$_log->_id}}')">
          {{$_log->_query_params_get|@count}}
        </span>
        <div id="tooltip-params-get-{{$_log->_id}}" style="display: none;">
          {{$_log->_query_params_get|@mbTrace}}
        </div>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-params-post-{{$_log->_id}}')">
          {{$_log->_query_params_post|@count}}
        </span>
        <div id="tooltip-params-post-{{$_log->_id}}" style="display: none;">
          {{$_log->_query_params_post|@mbTrace}}
        </div>
      </td>
      <td>
        <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-params-session-{{$_log->_id}}')">
          {{$_log->_session_data|@count}}
        </span>
        <div id="tooltip-params-session-{{$_log->_id}}" style="display: none;">
          {{$_log->_session_data|@mbTrace}}
        </div>
      </td>
      <td>{{mb_value object=$_log field=user_id tooltip=true}}</td>
      <td><a href="{{$_log->_link}}" target="_blank">{{tr}}Hyperlink{{/tr}}</a></td>
    </tr>
    </tbody>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="8">{{tr}}CLongRequestLog.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>