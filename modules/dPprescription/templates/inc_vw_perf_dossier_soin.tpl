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
 
  {{if $smarty.foreach.foreach_perfusion.first}}
  <th rowspan="{{$prescription->_ref_perfusions_for_plan|@count}}" onmouseover="timeOutBefore = setTimeout(showBefore, 1000);" onmouseout="clearTimeout(timeOutBefore);">
   <a href="#1" onclick="showBefore();">
     <img src="images/icons/a_left.png" title="" alt="" />
   </a>
  </th>
  {{/if}}
  
  <!-- Affichage des heures de prises des medicaments -->			    
  {{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date item=_hours}}
        {{foreach from=$_hours key=_heure_reelle item=_hour}}
		      {{assign var=_date_hour value="$_date $_heure_reelle"}}	
			    <td class="{{$_view_date}}-{{$moment_journee}}"
			        style='{{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}
			    {{if ($_date_hour >= $_perfusion->_debut) && ($_date_hour < $_perfusion->_fin)}}
			      background-image: url(images/pictures/perf_line.png);
			      background-repeat: repeat-x;		    
			      background-position: center;
			    {{else}}
			      background-color: #aaa;
			    {{/if}}'>
			    </td>    
		    {{/foreach}}
     {{/foreach}}		   
   {{/foreach}}
 {{/foreach}}		

 {{if $smarty.foreach.foreach_perfusion.first}}
 <th rowspan="{{$prescription->_ref_perfusions_for_plan|@count}}" onmouseover="timeOutAfter = setTimeout(showAfter, 1000);"  onmouseout="clearTimeout(timeOutAfter);">
   <a href="#1" onclick="showAfter();">
     <img src="images/icons/a_right.png" title="" alt="" />
   </a>
 </th>
 {{/if}}
 
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