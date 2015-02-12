{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 *}}

<style>
  td.user_human {
    background-color: rgba(178, 34, 34, 0.20) !important;
  }

  td.user_bot {
    background-color: rgba(70, 130, 180, 0.20) !important;
  }
</style>

{{mb_include module=system template=inc_pagination total=$list_count current=$start step=50 change_page='changePage' jumper=1}}

<table class="tbl">
  <tr>
    <th class="narrow"></th>
    <th class="narrow">{{mb_title class=CLongRequestLog field=datetime}}</th>
    <th style="width: 75px;">{{mb_title class=CLongRequestLog field=duration}} (s)</th>
    <th style="width: 125px;">{{mb_title class=CLongRequestLog field=server_addr}}</th>
    <th class="narrow">{{mb_title class=CLongRequestLog field=_module}}</th>
    <th class="narrow">{{mb_title class=CLongRequestLog field=_action}}</th>
    <th>{{mb_title class=CLongRequestLog field=user_id}}</th>
  </tr>

  {{foreach from=$logs item=_log}}
    {{mb_ternary var=bot_css test=$_log->_ref_user->_ref_user->dont_log_connection value='user_bot' other='user_human'}}
    <tr>
      <td class="{{$bot_css}}">
        <button class="search notext compact" onclick="LongRequestLog.edit('{{$_log->_id}}')"></button>
      </td>

      <td class="{{$bot_css}}">{{mb_value object=$_log field=datetime}}</td>

      <td class="{{$bot_css}}" style="text-align: right;">{{mb_value object=$_log field=duration}}</td>

      <td class="{{$bot_css}}" style="text-align: center;">{{mb_value object=$_log field=server_addr}}</td>

      <td class="{{$bot_css}}">{{mb_value object=$_log field=_module}}</td>

      <td class="{{$bot_css}}">{{mb_value object=$_log field=_action}}</td>

      <td class="{{$bot_css}}">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_log->_ref_user}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="7">{{tr}}CLongRequestLog.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>