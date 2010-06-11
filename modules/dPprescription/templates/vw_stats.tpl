{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var series = {{$series|@json}};
var options = {{$options|@json}};

series[0].markers.labelFormatter = function(obj) {
  return Math.round(obj.data[obj.index][2] * 100) + "%";
}


Main.add(function(){
  Flotr.draw($('graph'), series, options);
	
	var oFormStat = getForm("formStat");
	Calendar.regField(oFormStat.date_min);
	Calendar.regField(oFormStat.date_max);
});
</script>

<form name="formStat" method="get" action="?">
	<input type="hidden" name="m" value="{{$m}}" />
	<input type="hidden" name="tab" value="{{$tab}}" />
  
	
	<table class="form">
		<tr>
		  <th class="title" colspan="4">Statistiques d'utilisation de la prescription</th>
		</tr>
		<tr>
			<th class="category">Début</th>
			<th class="category">Fin</th>
			<th class="category">Praticien</th>
			<th class="category">Service</th>
		</tr>
	  <tr>
	  	<td>	
	      <input type="hidden" name="date_min" value="{{$date_min}}" onchange="this.form.submit();" />
			</td>
			<td>
	      <input type="hidden" name="date_max" value="{{$date_max}}" onchange="this.form.submit();" />
      </td>
			<td>
			  <select name="praticien_id" onchange="this.form.submit()">
			  	  <option value="">&mdash; Choix d'un praticien</option>
			  	{{foreach from=$praticiens item=_praticien}}
				    <option value="{{$_praticien->_id}}" {{if $praticien_id == $_praticien->_id}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_praticien->_ref_function->color}};">{{$_praticien->_view}}</option>
			  	{{/foreach}}
				</select>
	    </td>
			<td>
				<select name="service_id" onchange="this.form.submit();">
					<option value="">&mdash; Choix d'un service</option>
					{{foreach from=$services item=_service}}
					<option value="{{$_service->_id}}" {{if $service_id == $_service->_id}}selected="selected"{{/if}}>{{$_service->_view}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
	</table>
</form>

<div style="height: 400px; margin: 1em;" id="graph"></div>
