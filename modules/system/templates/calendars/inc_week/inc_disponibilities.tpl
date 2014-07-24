{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=objectType value=CPlageConsult}}
{{mb_default var=mode value=calendar}}

{{if $object->_disponibilities|@count}}
  {{assign var=count_dispo value=$object->_disponibilities|@array_count_values}}
  <div class="progressBar_dispo" onmouseover="ObjectTooltip.createDOM(this, 'disponibility_{{if $mode == "calendar"}}{{$object->guid}}{{else}}{{$object->_guid}}{{/if}}')">
    {{foreach from=$object->_disponibilities key=time item=_dispo}}
      <div class="disponibility_bar disponibility_planning_{{$_dispo}}" data-time="{{$time}}"></div>
    {{/foreach}}
  </div>
  <table id="disponibility_{{if $mode == "calendar"}}{{$object->guid}}{{else}}{{$object->_guid}}{{/if}}" style="display: none;" class="tbl">
    {{foreach from=$count_dispo key=type item=_dispo name=loop}}
      <tr>
        <th>{{tr}}{{$objectType}}_planning_disponibility_{{$type}}{{/tr}}</th>
        <td>{{$_dispo}}</td>
      </tr>
    {{/foreach}}
    <tr>
    <th>Total</th>
    <td>{{$object->_disponibilities|@count}}</td>
  </table>
{{/if}}