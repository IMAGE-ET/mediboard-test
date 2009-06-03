{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("selection").date, null, {noView: true});
});
</script>

<form action="?" name="selection" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="op" value="0" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{$date|date_format:$dPconfig.longdate}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
    </th>
  </tr>
  
  <tr>
    <th><label for="salle" title="Salle d'opération">Salle</label></th>
    <td>
      <select name="salle" onchange="this.form.submit()">
        <option value="">&mdash; {{tr}}CSalle.none{{/tr}}</option>
        {{foreach from=$listBlocs item=curr_bloc}}
        <optgroup label="{{$curr_bloc->nom}}">
          {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
          <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $salle->_id}}selected="selected"{{/if}}>
            {{$curr_salle->nom}}
          </option>
          {{foreachelse}}
          <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
          {{/foreach}}
        </optgroup>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>

</form>
      
{{include file="inc_details_plages.tpl"}}