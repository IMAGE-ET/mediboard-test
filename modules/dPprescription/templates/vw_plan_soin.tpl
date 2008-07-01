<!-- Fermeture des tableaux -->
    </td>
  </tr>
</table>
  
<style type="text/css">

ul {
  padding-left: 11px;
}

</style>

<div class="plan_soin">

<!-- Header -->
<table style="width: 100%">
  <tr>
    <td>
      IPP: {{$patient->_IPP}}<br />
      <strong>{{$patient->_view}}</strong>
    </td>
    <td>
      Age {{$patient->_age}}{{if $patient->_age != "??"}} ans{{/if}}<br />
      Poids {{$poids}}{{if $poids}} kg{{/if}}
    </td>
    <td>
      Début du séjour: {{$sejour->_entree|date_format:"%d/%m/%Y à %Hh%M"}}<br />
      {{if $sejour->_ref_curr_affectation->_id}}
        Chambre {{$sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}
      {{/if}}
    </td>
    <td>
      Feuille de soin du {{$date|date_format:"%d/%m/%Y"}}
      </td>
  </tr>
</table>

<table style="border-collapse: collapse; border: 1px solid #ccc" class="tbl">
  <tr>
    <th colspan="2" class="title" style="width: 7cm">Prescription</th>
    <th rowspan="2" class="title" style="width: 1cm">Prescripteur</th>
    <th rowspan="2" style="width: 5px"></th>
    {{foreach from=$dates item=date}}
    <th colspan="8" class="title" style="width: 5cm">{{$date|date_format:"%d/%m/%Y"}}</th>
    {{/foreach}}
  </tr>
  <tr>
    <th class="title" style="width: 4cm">Produit</th>
    <th class="title" style="width: 3cm">Posologie</th>
    {{foreach from=$dates item=date}}
      {{foreach from=$tabHours item=_hour}}
        <th>{{$_hour}}h</th>
      {{/foreach}}
    {{/foreach}}
  </tr>
  <!-- Affichage des medicaments -->
  {{foreach from=$all_lines_med item=_all_lines_unite_prise}}
    {{foreach from=$_all_lines_unite_prise key=unite_prise item=_line}}
      {{assign var=line_id value=$_line->_id}}
      <tr>
        <td class="text"  style="border: 1px solid #ccc;">{{$_line->_view}}</td>
        <td class="text"  style="border: 1px solid #ccc;">
          {{if is_numeric($unite_prise)}}
          <ul>
            <li>{{$intitule_prise_med.$line_id.autre.$unite_prise}}</li>
          </ul>
          {{else}}
	        <ul>
	        {{foreach from=$intitule_prise_med.$line_id.$unite_prise item=_prise}}
	          <li>{{$_prise}}</li>
	        {{/foreach}}
	        </ul>
	        {{/if}}
        </td>
        
  			<td class="text" style="text-align: center">
  			  {{if $_line->_traitement}}
  			    Traitement Personnel
  			  {{else}}
  			   {{$_line->_ref_praticien->_view}}
  			  {{/if}}
  			</td>
  			<td style="border: 1px solid #ccc; text-align: center">
          {{if !$_line->signee && !$_line->valide_pharma}}
  			    DP
  			  {{else}}
  			    {{if !$_line->signee}}
  			      D
  			    {{/if}}
  			    {{if !$_line->valide_pharma}}
  			      P
  			    {{/if}}
  			  {{/if}}
        </td>
        <!-- Affichage des heures de prises des medicaments -->
		  	{{foreach from=$dates item=date}}
		    {{if array_key_exists($line_id, $list_prises_med.$date)}}
		      {{foreach from=$tabHours item=_hour}}
		      <td style="padding: 0; width: 0.5cm; border: 1px solid #ccc; text-align: center">
			      {{if array_key_exists($unite_prise, $list_prises_med.$date.$line_id)}}
					    {{assign var=prise_line value=$list_prises_med.$date.$line_id.$unite_prise}}	            
	            {{if is_array($prise_line) && array_key_exists($_hour, $prise_line)}}
	              {{assign var=quantite value=$prise_line.$_hour}}
	            {{else}}
	             {{assign var=quantite value=""}}
	            {{/if}}
	            {{$quantite}}
		         {{/if}}
	         </td>
	       {{/foreach}}
	     {{else}}	    
	        {{if ($_line->_date_arret_fin >= $date && $_line->debut <= $date) || (!$_line->_date_arret_fin && $_line->debut <= $date)}}
	          {{foreach from=$tabHours item=_hour}}
					    <td style="padding: 0; border: 1px solid #ccc">
					    </td>
					  {{/foreach}}
	        {{else}} 
				    {{foreach from=$tabHours item=_hour}}
					    <td style="padding: 0; border: 1px solid #ccc; text-align:center">
					      <img src="images/icons/gris.gif" />
					    </td>
					  {{/foreach}}
			    {{/if}}
		    {{/if}}    
        {{/foreach}}
      </tr>
    {{/foreach}}
  {{/foreach}} 
  <!-- Affichage des elements -->
  {{foreach from=$all_lines_element key=name_chap item=elements_chap}}
    {{foreach from=$elements_chap key=name_cat item=elements_cat}}
      {{assign var=categorie value=$categories.$name_cat}}
      {{foreach from=$elements_cat item=_element}}
        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}}
       {{assign var=element_id value=$element->_id}}
      <tr>
        <td class="text">{{$element->_view}}</td>
        <td class="text" style="border: 1px solid #ccc;">
          {{if is_numeric($unite_prise)}}
          <ul>
            <li>{{$intitule_prise_element.$element_id.autre.$unite_prise}}</li>
          </ul>
          {{elseif $unite_prise != "aucune_prise"}}
	        <ul>
	        {{foreach from=$intitule_prise_element.$element_id.$unite_prise item=_prise}}
	          <li>{{$_prise}}</li>
	        {{/foreach}}
	        </ul>
	        {{/if}}
        </td>
       
  			<td class="text" style="text-align: center">
  			   {{$element->_ref_praticien->_view}} 
  			</td>
  			 <td style="border: 1px solid #ccc; text-align: center">
  			  {{if !$element->signee}}
  			    D
  			  {{/if}}
        </td>
        <!-- Affichage des heures de prises des medicaments -->
		  	{{foreach from=$dates item=date}}
		    {{if array_key_exists($element_id, $list_prises_element.$date)}}
		      {{foreach from=$tabHours item=_hour}}
		      <td style="padding: 0; border: 1px solid #ccc; text-align: center">
			      {{if array_key_exists($unite_prise, $list_prises_element.$date.$element_id)}}
					    {{assign var=prise_line value=$list_prises_element.$date.$element_id.$unite_prise}}	            
	            {{if is_array($prise_line) && array_key_exists($_hour, $prise_line)}}
	              {{assign var=quantite value=$prise_line.$_hour}}
	            {{else}}
	             {{assign var=quantite value=""}}
	            {{/if}}
	            {{$quantite}}
		         {{/if}}
	         </td>
	       {{/foreach}}
	     {{else}}	    
	        {{if $element->_date_arret_fin >= $date && $element->debut <= $date}}
	          {{foreach from=$tabHours item=_hour}}
					    <td style="padding: 0; border: 1px solid #ccc">
					    </td>
					  {{/foreach}}
	        {{else}} 
				    {{foreach from=$tabHours item=_hour}}
					    <td style="padding: 0; border: 1px solid #ccc; text-align:center">
					      <img src="images/icons/gris.gif"/>
					    </td>
					  {{/foreach}}
			    {{/if}}
		    {{/if}}   
        {{/foreach}}
        </tr>
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
	  <tr>
	    <th class="title" colspan="2">Remarque</th>
	    <th class="title" colspan="2">Pharmacien</th>
	    <th class="title" colspan="8">Signature IDE</th>
	    <th class="title" colspan="8">Signature IDE</th>
	    <th class="title" colspan="8">Signature IDE</th>
	  </tr>
	  <tr>
	    <td style="border: 1px solid #ccc; height: 1.5cm" colspan="2"></td>
	    <td class="text" style="border: 1px solid #ccc; text-align: center" colspan="2">
	    {{if $pharmacien->_id}}
	      {{$pharmacien->_view}} {{$last_log->date|date_format:"%d/%m/%Y à %Hh%M"}}
	    {{/if}}  
	      
	    </td>
	    <td style="border: 1px solid #ccc;" colspan="8" ></td>
	    <td style="border: 1px solid #ccc;" colspan="8"></td>
	    <td style="border: 1px solid #ccc;" colspan="8"></td>
	  </tr>
  </table>

<!-- Re-ouverture des tableaux -->
<table>
  <tr>
    <td>