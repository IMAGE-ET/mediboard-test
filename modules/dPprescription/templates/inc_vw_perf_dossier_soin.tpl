{{assign var=perfusion_id value=$_perfusion->_id}}

<tr id="line_{{$_perfusion->_guid}}">
 	<td style="text-align: center;">-</td>
 	<td style="text-align: center;">-</td>
 	<td class="text">
 	<div onclick='addCibleTransmission("CPerfusion","{{$_perfusion->_id}}","{{$_perfusion->_view}}");' 
	       class="{{if @$transmissions.CPerfusion.$perfusion_id|@count}}transmission{{else}}transmission_possible{{/if}}">
	    <a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CPerfusion', object_id: {{$_perfusion->_id}} } })">
	      {{$_perfusion->_view}}
	    </a>
	  </div>
	</td>  
 	<td class="text" style="font-size: 1em;">
 	  <ul>
 	   {{foreach from=$_perfusion->_ref_lines item=_line}}
 	     <li><small>{{$_line->_view}}<small></li>
 	   {{/foreach}}
 	  </ul>
 	</td>	      
 
  <!-- Affichage des heures de prises des medicaments -->			    
  {{foreach from=$tabHours item=_hours_by_date key=_date}}
	  {{foreach from=$_hours_by_date item=_hour}}  
	    {{assign var=_date_hour value="$_date $_hour:00:00"}}
		    <td class="{{$_date_hour}}" 
		        style='{{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}
		    {{if ($_date_hour > $_perfusion->_debut) && ($_date_hour < $_perfusion->_fin)}}
		      background-image: url(images/pictures/perf_line.png);
		      background-repeat: repeat-x;		    
		      background-position: center;
		    {{else}}
		      background-color: #aaa;
		    {{/if}}'>
		    </td>    
		{{/foreach}}
 {{/foreach}}		   

 <!-- Signature du praticien -->
 <td style="text-align: center">
   {{if $_perfusion->signature_prat}}
   <img src="images/icons/tick.png" alt="Signée par le praticien" title="Signée par le praticien" />
   {{else}}
   <img src="images/icons/cross.png" alt="Non signée par le praticien" title="Non signée par le praticien" />
   {{/if}}
 </td>
 <!-- Signature du pharmacien -->
 <td style="text-align: center">
   {{if $_perfusion->signature_pharma}}
   <img src="images/icons/tick.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
   {{else}}
   <img src="images/icons/cross.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
   {{/if}}
  </td>
</tr>	 