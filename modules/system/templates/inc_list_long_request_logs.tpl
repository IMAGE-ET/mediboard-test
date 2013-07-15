{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 *}}

{{mb_include module=system template=inc_pagination total=$list_count current=$start step=50 change_page='changePage' jumper=1}}

<table class="tbl">
  <tr>
    <th>{{mb_title class=CLongRequestLog field=datetime}}</th>
    <th>{{mb_title class=CLongRequestLog field=duration}}</th>
    <th>{{mb_title class=CLongRequestLog field=server_addr}}</th>
    <th>{{mb_title class=CLongRequestLog field=_module}}</th>
    <th>{{mb_title class=CLongRequestLog field=_action}}</th>
    <th>{{mb_title class=CLongRequestLog field=user_id}}</th>
    <th class="narrow"></th>
  </tr>

  {{foreach from=$logs item=_log}}
  <tr>
    <td>{{mb_value object=$_log field=datetime}}</td>
    <td>{{mb_value object=$_log field=duration}}s</td>
    <td>{{mb_value object=$_log field=server_addr}}</td>
    <td>{{mb_value object=$_log field=_module}}</td>
    <td>{{mb_value object=$_log field=_action}}</td>
    <td>{{mb_value object=$_log field=user_id tooltip=true}}</td>
    <td><button class="search notext" onclick="LongRequestLog.edit('{{$_log->_id}}')"></button></td>
  </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="8">{{tr}}CLongRequestLog.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>