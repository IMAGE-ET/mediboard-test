{{*
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{mb_include module=system template=inc_pagination change_page=changePage}}

<table class="tbl">
  <tr>
    <th class="narrow" rowspan="2">{{mb_title object=$cim field=code}}</th>
    <th rowspan="2">{{mb_title object=$cim field=short_name}}</th>
    <th rowspan="2">{{mb_title object=$cim field=complete_name}}</th>
    <th colspan="4">{{mb_title object=$cim field=type}}</th>
    {{if $modal}}
      <th rowspan="2" class="narrow"></th>
    {{/if}}
  </tr>
  <tr>
    <th>{{tr}}CCIM10.DP{{/tr}}</th>
    <th>{{tr}}CCIM10.DR{{/tr}}</th>
    <th>{{tr}}CCIM10.DAS{{/tr}}</th>
    <th>{{tr}}CCIM10.ailleurs{{/tr}}</th>
  </tr>
  {{foreach from=$list_cim item=_cim}}
    <tr>
      <td class="text narrow">
        <span onmouseover="ObjectTooltip.createEx(this, 'CCIM10-{{$_cim->_id}}')"><strong>{{$_cim->_id}}</strong></span></td>
      <td>{{$_cim->short_name}}</td>
      <td>{{$_cim->complete_name}}</td>
      <td>
        {{if $_cim->type == 0}}
          <img title="{{tr}}CCIM10.type.{{$_cim->type}}{{/tr}}" src="images/icons/note_green.png">
        {{else}}
          <img title="{{tr}}CCIM10.type.{{$_cim->type}}{{/tr}}" src="images/icons/note_red.png">
        {{/if}}
      </td>
      <td>
        {{if $_cim->type == 0 || $_cim->type == 4}}
          <img title="{{tr}}CCIM10.type.{{$_cim->type}}{{/tr}}" src="images/icons/note_green.png">
        {{else}}
          <img title="{{tr}}CCIM10.type.{{$_cim->type}}{{/tr}}" src="images/icons/note_red.png">
        {{/if}}
      </td>
      <td>
        {{if $_cim->type != 3}}
          <img title="{{tr}}CCIM10.type.{{$_cim->type}}{{/tr}}" src="images/icons/note_green.png">
        {{else}}
          <img title="{{tr}}CCIM10.type.{{$_cim->type}}{{/tr}}" src="images/icons/note_red.png">
        {{/if}}
      </td>
      <td>
        <img title="{{tr}}CCIM10.type.{{$_cim->type}}{{/tr}}" src="images/icons/note_green.png">
      </td>
      {{if $modal}}
        <td>
          <button type="button" class="tick notext" onclick="DiagPMSI.selectDiag('{{$_cim->code}}'); Control.Modal.close()"></button>
        </td>
      {{/if}}
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="8" class="empty">{{tr}}CCIM.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>