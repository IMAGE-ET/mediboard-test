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

{{if is_array($result)}}
  <table class="tbl">
    <tr>
      <th class="title">
        {{tr}}CHTTPTunnelObject-Information-general{{/tr}}
      </th>
    </tr>
    <tr>
      <td>
        {{tr}}CHTTPTunnelObject-Start_date{{/tr}} : {{$result.start_date}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}CHTTPTunnelObject-Timer{{/tr}} : {{$result.timer}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}CHTTPTunnelObject-Memory{{/tr}} : {{$result.memory}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}CHTTPTunnelObject-Memory_peak{{/tr}} : {{$result.memory_peak}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}CHTTPTunnelObject-Hits{{/tr}} : {{$result.hits}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}CHTTPTunnelObject-Data-sent{{/tr}} : {{$result.data_sent}}
      </td>
    </tr>
    <tr>
      <td>
        {{tr}}CHTTPTunnelObject-Data-received{{/tr}} : {{$result.data_received}}
      </td>
    </tr>
    <tr>
      <th class="section">
        {{tr}}CHTTPTunnelObject-Information-client{{/tr}}
      </th>
    </tr>
    <tr>
      <td {{if !$result.clients}}class="empty"{{/if}}>
  {{foreach from=$result.clients key=_key item=_client}}
      <table class="tbl">
        <tr>
          <th>{{tr}}CHTTPTunnelObject-Client{{/tr}} {{$_key}}</th>
        </tr>
        <tr>
          <td>
            {{tr}}CHTTPTunnelObject-Hits{{/tr}} : {{$_client.hits}}
          </td>
        </tr>
        <tr>
          <td>
            {{tr}}CHTTPTunnelObject-Data-sent{{/tr}} : {{$_client.data_sent}}
          </td>
        </tr>
        <tr>
          <td>
            {{tr}}CHTTPTunnelObject-Data-received{{/tr}} : {{$_client.data_received}}
          </td>
        </tr>
      </table>
  {{foreachelse}}
    {{tr}}CHTTPTunnelObject-Client-none{{/tr}}
  {{/foreach}}
      </td>
    </tr>
  </table>
{{else}}
  {{tr}}Action-success{{/tr}}
{{/if}}