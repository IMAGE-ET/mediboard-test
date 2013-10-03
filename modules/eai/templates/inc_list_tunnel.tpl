{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th colspan="3" class="title">
      {{tr}}CHTTPTunnelObject-List-tunnels{{/tr}}
    </th>
  </tr>
  <tr>
    <th>{{tr}}CHTTPTunnelObject-address{{/tr}}</th>
    <th>{{tr}}CHTTPTunnelObject-status{{/tr}}</th>
    <th>{{tr}}Action{{/tr}}</th>
  </tr>
  {{foreach from=$tunnels item=_tunnel}}
    {{if $_tunnel->status == "0"}}
      {{assign var=img value="images/icons/status_red.png"}}
    {{else}}
      {{assign var=img value="images/icons/status_green.png"}}
    {{/if}}
    <tr>
      <td>
        <button class="edit notext" onclick="CTunnel.editTunnel('{{$_tunnel->_id}}')">
          {{tr}}Edit{{/tr}}
        </button> {{$_tunnel->address}}
      </td>
      <td>
        <img class="status" src="{{$img}}" title="{{$_tunnel->_message_status}}"/>
      </td>
      <td>
        <button type="button" class="change notext" onclick="CTunnel.proxyAction('restart', '{{$_tunnel->_id}}')">
          {{tr}}Restart{{/tr}}
        </button>
        <button type="button" class="stop notext" onclick="CTunnel.proxyAction('stop', '{{$_tunnel->_id}}')">
          {{tr}}Stop{{/tr}}
        </button>
        <button type="button" class="stats notext" onclick="CTunnel.proxyAction('stat', '{{$_tunnel->_id}}')">
          {{tr}}Statistics{{/tr}}
        </button>
      </td>
    </tr>
    {{foreachelse}}
    <tr><td colspan="3" class="empty">{{tr}}CHTTPTunnelObject.none{{/tr}}</td></tr>
  {{/foreach}}
</table>