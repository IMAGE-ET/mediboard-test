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
    <th class="title" colspan="{{math equation=x+1 x=$dates|@count}}">
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
      </form>
    </th>
  </tr>

  <tr>
    <th>Patient</th>
    {{foreach from=$dates item=_date}}
      <th>{{$_date|date_format:$conf.date}}</th>
    {{/foreach}}
  </tr>
		
  {{foreach from=$charge key=_sejour_id item=_indices_by_date}}

	  {{assign var=sejour value=$sejours.$_sejour_id}}
	  <tr>
	  	<th>
        {{$sejour->_ref_patient}}  
      </th>
		{{foreach from=$_indices_by_date key=date item=_ressources}}
		  <td>
			{{foreach from=$_ressources key=ressource_id item=nb}}
			  {{assign var=ressource value=$ressources.$ressource_id}}
				{{$ressource}} ({{$nb}})
			  <br />  
      {{/foreach}}
			</td>
	  {{/foreach}}
		</tr>
	{{foreachelse}}
	<tr>
		<td class="empty" colspan="{{math equation=x+1 x=$dates|@count}}">
			{{tr}}CSejour.none{{/tr}}
		</td>
	</tr>
	{{/foreach}}
</table>  