
  <!-- Affichage des allergies -->
{{if array_key_exists('alle', $antecedents)}}
  {{assign var=allergies value=$antecedents.alle}}
  {{if $allergies|@count}}
   <img src="images/icons/warning.png" title="Allergies" alt="Allergies" 
        onmouseover="$('allergies{{$sejour_id}}').show();"
        onmouseout="$('allergies{{$sejour_id}}').hide();" />
 
   <div id="allergies{{$sejour_id}}" class="tooltip" style="text-align:left; display: none; background-color: #ddd; border-style: ridge; padding:3px;">
     <strong>Allergies</strong>
     <ul>
      {{foreach from=$allergies item=allergie}}
      <li>
      {{if $allergie->date}}
 	      {{$allergie->date|date_format:"%d/%m/%Y"}}:
	    {{/if}} 
		  	{{$allergie->rques}}
	    </li>
	    {{/foreach}}
   </ul>   
   </div>   
  {{/if}}
{{/if}}

<!-- Affichage des autres antecedents -->
 {{if $dossier_medical->_count_antecedents}}
  <img src="images/icons/antecedents.gif" title="Antécédents" alt="Antécédents" 
        onmouseover="$('antecedents{{$sejour_id}}').show();"
        onmouseout="$('antecedents{{$sejour_id}}').hide();" />
  
   <div id="antecedents{{$sejour_id}}" class="tooltip" style="text-align:left;  display: none; background-color: #ddd; border-style: ridge; padding:3px;">
     <ul>
      {{foreach from=$antecedents key=name item=cat}}
      {{if $name != "alle" && $cat|@count}}
      <li>
      <strong>{{tr}}CAntecedent.type.{{$name}}{{/tr}}</strong>
      <ul>
      {{foreach from=$cat item=ant}}
      <li>
      {{if $ant->date}}
 	      {{$ant->date|date_format:"%d/%m/%Y"}}:
	    {{/if}} 
		  	{{$ant->rques}}
	    </li>
	    {{/foreach}}
	    </ul>
	    </li>
	    {{/if}}
	    {{/foreach}}
   </ul>   
   </div>  
 {{/if}}
 
