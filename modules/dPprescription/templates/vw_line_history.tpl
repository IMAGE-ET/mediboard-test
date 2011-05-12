<table class="main tbl">
	<tr>
	  <th colspan="5" class="title text">
	  	{{$line->_view}}
			
			{{if $line instanceof CPrescriptionLineMix}}
			<br />
			  {{foreach from=$line->_ref_lines item=_perf_line name=foreach_perf_line}}
          {{$_perf_line->_view}}
          {{if !$smarty.foreach.foreach_perf_line.last}}, {{/if}} 
        {{/foreach}}
			{{/if}}
		</th>
	</tr>	
	<tr>
		<th>Posologie</th>
		<th>Début</th>
		<th>Durée</th>
    <th>Arrêt</th>
		<th>Praticien</th>
	</tr>	
	{{foreach from=$lines key=type item=_lines}}
	  {{foreach from=$_lines item=_line name="foreach_lines"}}
	  <tr {{if $_line->_guid == $line->_guid}}class="selected"{{/if}}>
			<td class="text" {{if $smarty.foreach.foreach_lines.first}}style="font-weight: bold;"{{/if}}>
				<span onmouseover="ObjectTooltip.createEx(this, '{{$_line->_guid}}')">
				{{if $_line instanceof CPrescriptionLineMix}}
				  <strong>{{$_line->_view}}<br />
		      {{foreach from=$_line->_ref_lines item=_perf_line name=foreach_perf_line}}
		        {{$_perf_line->_view}}
		        {{if !$smarty.foreach.foreach_perf_line.last}},{{/if}} 
		      {{/foreach}}
				{{else}}
					{{foreach from=$_line->_ref_prises item=prise name=foreach_prise}}
		        {{if $prise->quantite}}
		          {{$prise->_view}}
		          {{if !$smarty.foreach.foreach_prise.last}},{{/if}} 
		        {{/if}}
		      {{foreachelse}}
					  <em>Aucune posologie</em>
					{{/foreach}}
				{{/if}}
				</span>
	    </td>
			<td>
				{{if $_line instanceof CPrescriptionLineMix}}
				  {{mb_value object=$_line field=date_debut}} {{mb_value object=$_line field=time_debut}}
				{{else}}
				  {{mb_value object=$_line field=debut}} {{mb_value object=$_line field=time_debut}}
			  {{/if}}
			</td>
			<td>
				{{if $_line instanceof CPrescriptionLineMix}}
				  {{if $_line->duree && $_line->unite_duree}}
					  {{mb_value object=$_line field=duree}}  
            {{mb_value object=$_line field=unite_duree}}
					{{/if}}
				{{else}}
					{{if $_line->duree && $_line->unite_duree}}
	          {{mb_value object=$_line field=duree}}  
	          {{mb_value object=$_line field=unite_duree}}
	        {{elseif $_line->_ref_prescription->type == "sejour"}}
					  {{if $_line instanceof CPrescriptionLineMedicament}}
	          <span class="opacity-70">{{mb_value object=$_line field=_duree}} Jour(s) <br />(Fin du séjour)</span>
						{{elseif $_line instanceof CPrescriptionLineElement}}
						1 Jour
						{{/if}}
	        {{/if}}
				{{/if}}
			</td>
			<td>
				<strong>
					{{mb_value object=$_line field=date_arret}} {{mb_value object=$_line field=time_arret}}
        </strong>					
			</td>
			<td>
				{{mb_value object=$_line field=praticien_id}}
		  </td>
		</tr>	
    {{/foreach}}
	{{/foreach}}
</table>