<table class="tbl">
	<tr>
		<th class="title" colspan="10">Statistiques</th>
	</tr>
	<tr>
    <th style="width: 1%;">{{mb_title class=CPlageOp field=chir_id}}</th>
    <th colspan="2" style="width: 30%;">{{tr}}dPcabinet-stats-consultations{{/tr}}</th>
    <th colspan="2" style="width: 30%;">{{tr}}dPcabinet-stats-patients{{/tr}}</th>
    <th colspan="2" style="width: 30%;">{{tr}}dPcabinet-stats-sejours{{/tr}}</th>
		{{if $filter->_other_function_id}} 
    <th colspan="2" style="width: 30%;">{{tr}}dPcabinet-stats-others{{/tr}}</th>
		{{/if}}
	</tr>
	
	{{foreach from=$praticiens key=praticien_id item=_praticien}}
  <tr style="text-align: right;">
  	<td style="text-align: left;">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_praticien}}</td>
    {{assign var=counts   value=$stats.$praticien_id.counts}}
    {{assign var=percents value=$stats.$praticien_id.percents}}
    <td>{{$counts.consultations}}</td>
    <td style="width: 1%;"><span style="opacity: 0.7">{{$percents.consultations|percent}}</span></td>
    <td>{{$counts.patients}}</td>
    <td style="width: 1%;"><span style="opacity: 0.7">{{$percents.patients|percent}}</span></td>
    <td>{{$counts.sejours}}</td>
    <td style="width: 1%;"><span style="opacity: 0.7">{{$percents.sejours|percent}}</span></td>
    {{if $filter->_other_function_id}} 
    <td>{{$counts.others}}</td>
    <td style="width: 1%;"><span style="opacity: 0.7">{{$percents.others|percent}}</span></td>
    {{/if}}
  </tr>	   
	{{foreachelse}}
	<tr>
		<td><em>{{tr}}None{{/tr}}</em></td>
	</tr>
	{{/foreach}}
</table>