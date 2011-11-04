{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function(){
  Calendar.regField(getForm("updateChargeSoins").date);
	Calendar.regField(getForm("updateChargeSoins").date_max);
}); 

</script>


<table class="main tbl">
  <tr>
    <th class="title" colspan="{{math equation=x+2 x=$dates|@count}}">
      <form name="updateChargeSoins" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
        
        Charge en soins du service
        <select name="service_id" onchange="this.form.submit();">
          <option value="">&mdash; Service</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
          {{/foreach}}
        </select>
        du
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
				au
				<input type="hidden" name="date_max" class="date" value="{{$date_max}}" onchange="this.form.submit()" />
        <span style="float: right;">
          <label>
            <input type="checkbox" name="cout_euro_view" {{if $cout_euro == 1}}checked="checked"{{/if}}/ onchange="$V(this.form.cout_euro, this.checked ? 1 : 0); this.form.submit()"> Coût
            <input type="hidden" name="cout_euro" value="{{$cout_euro}}" />
          </label> 
        </span>
      </form>
    </th>
  </tr>

  <tr>
    <th>Patient</th>
    {{foreach from=$dates item=_date}}
      <th>{{$_date|date_format:$conf.date}}</th>
    {{/foreach}}
    <th class="title">Total</th>
  </tr>
		
  {{foreach from=$charge key=_sejour_id item=_indices_by_date}}

	  {{assign var=sejour value=$sejours.$_sejour_id}}
	  <tr>
	  	<th>
        {{$sejour->_ref_patient}}  
      </th>
  		{{foreach from=$_indices_by_date key=date item=_ressources}}
  		  <td>
  			  {{mb_include module=soins template=inc_detail_ressources list_ressources=$_ressources}}
  			</td>
  	  {{/foreach}}
      <td>
        {{mb_include module=soins template=inc_detail_ressources list_ressources=$total_sejour.$_sejour_id}}
      </td>
		</tr>
	{{foreachelse}}
	<tr>
		<td class="empty" colspan="{{math equation=x+1 x=$dates|@count}}">
			{{tr}}CSejour.none{{/tr}}
		</td>
	</tr>
	{{/foreach}}
  <tr>
    <th class="title">Total</th>
  {{foreach from=$total_date item=_total}}
    <th class="title">
      {{mb_include module=soins template=inc_detail_ressources list_ressources=$_total}}
    </th>
  {{/foreach}}
  <th class="title">
    {{mb_include module=soins template=inc_detail_ressources list_ressources=$total}}
  </th>
</table>  