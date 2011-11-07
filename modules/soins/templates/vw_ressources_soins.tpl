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
  Calendar.regField(getForm("updateChargeSoins").datetime);
}); 

</script>

<table class="main tbl">
  <tr>
    <th class="title" colspan="{{math equation=x+2 x=$datetimes|@count}}">
      <form name="updateChargeSoins" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="{{$actionType}}" value="{{$action}}" />

        <span style="float: right;">
          <label>
            <input type="checkbox" name="show_cost_view" {{if $show_cost}}checked="checked"{{/if}}/ onchange="$V(this.form.show_cost, this.checked ? 1 : 0); this.form.submit()"> Coût
            <input type="hidden" name="show_cost" value="{{$show_cost}}" />
          </label> 
        </span>
				        
        Charge en soins du service
        <select name="service_id" onchange="this.form.submit();">
          <option value="">&mdash; Service</option>
          {{foreach from=$services item=_service}}
            <option value="{{$_service->_id}}" {{if $_service->_id == $service_id}}selected="selected"{{/if}}>{{$_service}}</option>
          {{/foreach}}
        </select>
        à partir de 
        <input type="hidden" name="datetime" class="dateTime" value="{{$datetime}}" onchange="this.form.submit()" />
        pendant 
        <input type="text" name="nb_periods" class="num" size="2" value="{{$nb_periods}}" onchange="this.form.submit()" />
        
        <select name="period" onchange="this.form.submit()" >
          <option value="hour" {{if $period == "hour"}} selected="true" {{/if}}>{{tr}}Hour{{/tr}}s</option>
          <option value="day"  {{if $period == "day" }} selected="true" {{/if}}>{{tr}}Day{{/tr}}s</option>
          <option value="week" {{if $period == "week"}} selected="true" {{/if}}>{{tr}}Week{{/tr}}s</option>
        </select>

      </form>
    </th>
  </tr>

  <tr>
    <th>Patient</th>

    {{foreach from=$datetimes item=_datetime}}
    {{mb_include template=inc_period_table_cell}}
    {{/foreach}}

    <th>Total</th>
  </tr>
		
  {{foreach from=$charge key=_sejour_id item=_indices_by_datetime}}

	  {{assign var=sejour value=$sejours.$_sejour_id}}
	  <tr>
	  	<td class="text">
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}');">
          {{$sejour->_ref_patient}}  
        </strong>
      </td>
  		{{foreach from=$_indices_by_datetime key=_datetime item=_ressources}}
  		  <td {{if $sejour->entree > $_datetime || $_datetime > $sejour->sortie}} class="arretee" {{/if}} >
  			  {{mb_include template=inc_detail_ressources list_ressources=$_ressources total=0}}
  			</td>
  	  {{/foreach}}
      <th style="text-align: right;">
        {{mb_include template=inc_detail_ressources list_ressources=$total_sejour.$_sejour_id total=1}}
      </th>
		</tr>
	{{foreachelse}}
	<tr>
		<td class="empty" colspan="{{math equation=x+1 x=$datetimes|@count}}">
			{{tr}}CSejour.none{{/tr}}
		</td>
	</tr>
	{{/foreach}}
  
  <tr>
    <th>Patient</th>

    {{foreach from=$datetimes item=_datetime}}
    {{mb_include template=inc_period_table_cell}}
    {{/foreach}}

    <th>Total</th>
  </tr>
      
  <tr>
    <th>Total</th>
  {{foreach from=$total_datetime item=_total}}
    <th>
      {{mb_include module=soins template=inc_detail_ressources list_ressources=$_total total=0}}
    </th>
  {{/foreach}}
  <th class="title">
    {{mb_include module=soins template=inc_detail_ressources list_ressources=$total total=1}}
  </th>
</table>  