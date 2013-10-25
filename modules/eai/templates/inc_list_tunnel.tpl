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
      <button type="button" class="change notext" onclick="CTunnel.refreshList()">
        {{tr}}Refresh{{/tr}}
      </button>
    </th>
  </tr>
  <tr>
    <th>{{tr}}CHTTPTunnelObject-address{{/tr}}</th>
    <th>{{tr}}CHTTPTunnelObject-status{{/tr}}</th>
    <th>{{tr}}Action{{/tr}}</th>
  </tr>
  {{foreach from=$tunnels item=_tunnel}}
    <tr>
      <td>
        <button class="edit notext" onclick="CTunnel.editTunnel('{{$_tunnel->_id}}')">
          {{tr}}Edit{{/tr}}
        </button> {{$_tunnel->address}}
      </td>
      <td>
        {{unique_id var=uid}}
        {{main}}
          CTunnel.verifyAvaibility($('{{$uid}}'));
        {{/main}}
        <img class="status" id="{{$uid}}" data-id="{{$_tunnel->_id}}"
             data-guid="{{$_tunnel->_guid}}" src="images/icons/status_grey.png"
             title="{{$_tunnel->address}}"/>
      </td>
      <td>
        <button type="button" class="reboot compact notext" onclick="CTunnel.proxyAction('restart', '{{$_tunnel->_id}}')">
          {{tr}}Restart{{/tr}}
        </button>
        <button type="button" class="stop compact notext" onclick="CTunnel.proxyAction('stop', '{{$_tunnel->_id}}')">
          {{tr}}Stop{{/tr}}
        </button>
        <button type="button" class="stats compact notext" onclick="CTunnel.proxyAction('stat', '{{$_tunnel->_id}}')">
          {{tr}}Statistics{{/tr}}
        </button>
      </td>
    </tr>
    {{foreachelse}}
    <tr><td colspan="3" class="empty">{{tr}}CHTTPTunnelObject.none{{/tr}}</td></tr>
  {{/foreach}}
</table>