{{assign var=transmissions value=$prescription->_transmissions}}
{{assign var=perfusion_id value=$_perfusion->_id}}
 	<td style="text-align: center;">
 		{{if $move_dossier_soin}}
		<script type="text/javascript">
			Main.add(function () {
			  //moveDossierSoin();
		    viewDossierSoin('{{$mode_dossier}}');
		    $("line_{{$_perfusion->_guid}}").show();
		  });
		</script>
		{{/if}}
		-
	</td>
 	<td style="text-align: center;">-</td>
 	<td class="text">
 	  <div class="mediuser" style="border-color: #{{$_perfusion->_ref_praticien->_ref_function->color}}">
 		<div onclick='addCibleTransmission("CPerfusion","{{$_perfusion->_id}}","{{$_perfusion->_view}}");' 
	       class="mediuser {{if @$transmissions.CPerfusion.$perfusion_id|@count}}transmission{{else}}transmission_possible{{/if}}"
	       >
	    <a href="#{{$_perfusion->_guid}}" onmouseover="ObjectTooltip.createEx(this, '{{$_perfusion->_guid}}')">
	      {{$_perfusion}} 
	    </a>
	  </div>
	  </div>
	</td>  
 	<td class="text" style="font-size: 1em;">
 	  <ul>
 	   {{foreach from=$_perfusion->_ref_lines item=_line}}
 	     <li><small>{{$_line->_view}}</small></li>
 	   {{/foreach}}
 	  </ul>
 	</td>	      
  <th></th>
  {{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date item=_hours}}
        {{foreach from=$_hours key=_heure_reelle item=_hour}}
		      {{assign var=_date_hour value="$_date $_heure_reelle"}}	
			    <td {{if ($_date_hour >= $_perfusion->_debut) && ($_date_hour < $_perfusion->_fin)}}
			    			onclick='editPerf("{{$_perfusion->_id}}","{{$date}}",document.mode_dossier_soin.mode_dossier.value, "{{$sejour->_id}}")' 
			    	  {{/if}}
			        class="{{$_view_date}}-{{$moment_journee}}"
			        style='cursor: pointer; {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}
					    {{if ($_date_hour >= $_perfusion->_debut) && ($_date_hour < $_perfusion->_fin)}}
					      {{if $_date_hour < $now}}
					        background-image: url(images/pictures/perf_orange.png);
					      {{else}}
					        background-image: url(images/pictures/perf_bleu.png);
					      {{/if}}
					      {{if ($_perfusion->_debut_adm && $_perfusion->_debut_adm <= $_date_hour)}}
					        {{if ($_perfusion->_fin_adm && $_perfusion->_fin_adm >= $_date_hour) || (!$_perfusion->_fin_adm && $_date_hour < $now)}}
					          background-image: url(images/pictures/perf_vert.png);
					        {{/if}}
					      {{/if}}
								{{if $_perfusion->_fin_adm && ($_perfusion->_fin_adm < $_date_hour)}}
								   background-image: url(images/pictures/perf_rouge.png);
								{{/if}}			    
					    {{else}}
					      background-color: #aaa;
					       {{if ($_perfusion->_debut_adm && $_perfusion->_debut_adm <= $_date_hour)}}
					        {{if ($_perfusion->_fin_adm && $_perfusion->_fin_adm >= $_date_hour) || (!$_perfusion->_fin_adm && $_date_hour < $now)}}
					          background-image: url(images/pictures/perf_vert.png);
					        {{/if}}
					      {{/if}}
					    {{/if}}
					    background-repeat: repeat-x;
					    background-position: center;'>
			    </td>    
		    {{/foreach}}
     {{/foreach}}		   
   {{/foreach}}
 {{/foreach}}		
 <th></th>
 <td style="text-align: center">
   {{if $_perfusion->signature_prat}}
   <img src="images/icons/tick.png" alt="" title="Signée le {{$_perfusion->_ref_log_signature_prat->date|date_format:$dPconfig.datetime}} par {{$_perfusion->_ref_praticien->_view}}" />
   {{else}}
   <img src="images/icons/cross.png" alt="" title="Non signée par le praticien" />
   {{/if}}
 </td>
 <td style="text-align: center">
   {{if $_perfusion->signature_pharma}}
   <img src="images/icons/tick.png" alt="" title="Signée par le pharmacien" />
   {{else}}
   <img src="images/icons/cross.png" alt="" title="Non signée par le pharmacien" />
   {{/if}}
 </td>