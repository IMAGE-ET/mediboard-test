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

{{mb_script module=system script=exchange_source}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
{{foreach from=$all_sources key=name item=_sources}}
  <li><a href="#tab{{$name}}">{{tr}}{{$name}}{{/tr}}</a></li>
{{/foreach}}
</ul>

{{foreach from=$all_sources key=name item=_sources}}
  <div id="tab{{$name}}" style="display: none;">
    <table class="tbl">
      <tr>
        <th>
          {{tr}}Name{{/tr}}
        </th>
        <th>
          {{tr}}Reachable{{/tr}}
        </th>
        <th>
          {{tr}}Message{{/tr}}
        </th>
        <th>
          {{tr}}Time-response{{/tr}}
        </th>
      </tr>
      {{foreach from=$_sources item=_source}}
        <tr>
          <td>
            <a href="#" onclick="ExchangeSource.editSource('{{$_source->_guid}}');" title="Modifier la source">
              {{$_source->name}}
            </a>
          </td>
          <td class="narrow">
            {{unique_id var=uid}}
            {{main}}
              ExchangeSource.dispoSource($('{{$uid}}'));
            {{/main}}
            <img class="status" id="{{$uid}}" data-id="{{$_source->_id}}"
                 data-guid="{{$_source->_guid}}" src="images/icons/status_grey.png"
                 title="{{$_source->name}}"/>
          </td>
          <td class="text compact">
            {{$_source->_message|smarty:nodefaults}}
          </td>
          <td>
            {{$_source->_response_time}}
          </td>
        </tr>
      {{foreachelse}}
        <tr>
          <td colspan="4" class="empty">
            {{tr}}{{$name}}.none{{/tr}}
          </td>
        </tr>
    {{/foreach}}
    </table>
  </div>
{{/foreach}}
