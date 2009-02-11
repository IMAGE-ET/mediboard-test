<script type="text/javascript">

Main.add( function(){
  prepareForm("editPerf{{$perfusion->_id}}");
} );

</script>
{{assign var=perfusion_id value=$perfusion->_id}}
<form name="editPerf{{$perfusion->_id}}" method="post" action="?">
  <input type="hidden" name="dosql" value="do_perfusion_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="perfusion_id" value="{{$perfusion_id}}" />
	<table class="form">
	  <tr>
	    <th class="category">{{$perfusion->_view}}</th>
	  </tr>
	  <tr>
	    <td class="date">
			  {{if $perfusion->date_debut_adm}}
			    {{mb_label object=$perfusion field=date_debut_adm}}
			    {{mb_field object=$perfusion field=date_debut_adm form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    {{mb_field object=$perfusion field=time_debut_adm form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    <button type="button" class="notext cancel" onclick="this.form.date_debut_adm.value = ''; this.form.time_debut_adm.value = ''; submitTiming();"></button>
			  {{else}}
			    <input type="hidden" name="date_debut_adm" value="" />
			    <input type="hidden" name="time_debut_adm" value="" />
			    <button type="button" class="submit" onclick="this.form.date_debut_adm.value = 'current'; this.form.time_debut_adm.value = 'current'; submitTiming();">{{tr}}CPerfusion-date_debut_adm{{/tr}} de la perfusion</button>
			  {{/if}}
			</td>
	  </tr>
	  <tr>
	    <td class="date">
			  {{if $perfusion->date_fin_adm}}
			    {{mb_label object=$perfusion field=date_fin_adm}}
			    {{mb_field object=$perfusion field=date_fin_adm form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    {{mb_field object=$perfusion field=time_fin_adm form="editPerf$perfusion_id" onchange="submitTiming();" register=true canNull=false}}
			    <button type="button" class="notext cancel" onclick="this.form.date_fin_adm.value = ''; this.form.time_fin_adm.value = ''; submitTiming();"></button>
			  {{else}}
			    <input type="hidden" name="date_fin_adm" value="" />
			    <input type="hidden" name="time_fin_adm" value="" />
			    <button type="button" class="submit" onclick="this.form.date_fin_adm.value = 'current'; this.form.time_fin_adm.value = 'current'; submitTiming();">{{tr}}CPerfusion-date_fin_adm{{/tr}} de la perfusion</button>
			  {{/if}}
			</td>
	  </tr>
	</table>
</form>