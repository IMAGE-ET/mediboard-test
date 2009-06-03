{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Calendar.regField(getForm("selection").date, null, {noView: true});
});
</script>

<form action="?" name="selection" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="op" value="0" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{$date|date_format:date_format:$dPconfig.longdate}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
    </th>
  </tr>
  
  <tr>
    <th><label for="praticien_id" title="Praticien">Praticien</label></th>
    <td>
      <select name="praticien_id" onchange="this.form.submit()">
        <option value="">&mdash; Aucun praticien</option>
        {{foreach from=$listPrats key=prat_id item=prat_view}}
        <option value="{{$prat_id}}" {{if $prat_id == $praticien->_id}} selected="selected" {{/if}}>
          {{$prat_view}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>

</form>
      
{{include file="inc_details_op_prat.tpl"}}