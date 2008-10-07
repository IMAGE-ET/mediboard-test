{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}
<tr>
  <td class="text"  style="border: 1px solid #ccc;">
    {{$line->_view}}
    {{if $line->commentaire}}
      ({{$line->commentaire}})
    {{/if}}
  </td>
  <td class="text"  style="border: 1px solid #ccc;">
    {{if is_numeric($unite_prise)}}
      <ul>
	    <li>{{$prescription->_intitule_prise.$suffixe.$line_id.autre.$unite_prise}}</li>
	  </ul>
    {{elseif $unite_prise != "aucune_prise"}}
  	  <ul>
	  {{foreach from=$prescription->_intitule_prise.$suffixe.$line_id.$unite_prise item=_prise}}
	    <li>{{$_prise}}
	    {{if $line->_class_name == "CPrescriptionLineMedicament" && $unite_prise == $line->_ref_produit->libelle_presentation}}
        ({{$line->_ref_produit->libelle_unite_presentation}})
      {{/if}}
    </li>
	  {{/foreach}}
	  </ul>
    {{/if}}
    
  </td>
  <td class="text" style="text-align: center">
    {{if $line_class == "CPrescriptionLineMedicament" && $line->_traitement}}
      Traitement Personnel
    {{/if}}
    {{if !$line->_traitement}}{{$line->_ref_praticien->_view}}{{/if}}
  </td>
  <td style="border-left: 1px solid #ccc; text-align: center">
    {{if $line_class == "CPrescriptionLineMedicament" && !$line->signee && !$line->valide_pharma}}
	  DP
    {{else}}
	  {{if !$line->signee}}D{{/if}}
	  {{if $line_class == "CPrescriptionLineMedicament" && !$line->valide_pharma}}P{{/if}}
    {{/if}}
  </td>
  <!-- Affichage des heures de prises des medicaments -->
  {{foreach from=$dates item=date}}
	{{foreach from=$tabHours.$date key=_real_hour item=_hour name="foreach_date"}}
	  <td style="padding: 0; width: 0.5cm; border: 1px solid #ccc;
	             {{if $smarty.foreach.foreach_date.first}}border-left: 1px solid black;{{/if}}
	             {{if $smarty.foreach.foreach_date.last}}border-right: 1px solid black;{{/if}}text-align: center">
	    {{assign var=quantite value=""}}  
	    {{if array_key_exists($line_id, $prescription->_list_prises.$suffixe.$date) && array_key_exists($unite_prise, $prescription->_list_prises.$suffixe.$date.$line_id)}}
	      {{assign var=prise_line value=$prescription->_list_prises.$suffixe.$date.$line_id.$unite_prise}}	            
	      {{if is_array($prise_line) && array_key_exists($_hour, $prise_line)}}
	        {{assign var=quantite value=$prise_line.$_hour}}
	      {{/if}}
	    {{/if}}
	    {{if $line_class == "CPrescriptionLineMedicament"}}
		  {{if $line->_debut_reel > $_real_hour || ($line->_fin_reelle && $line->_fin_reelle < $_real_hour) || !$line->_active}}
		    <img src="images/icons/gris.gif" />
		  {{else}}
		    {{$quantite}}
		  {{/if}}
	    {{else}}
	      {{if $line->_debut_reel > $_real_hour || $line->_fin_reelle < $_real_hour || !$line->_active}}
             <img src="images/icons/gris.gif" />
           {{else}}
             {{$quantite}}
           {{/if}}
	    {{/if}}
	  </td>
	{{/foreach}}
  {{/foreach}}
</tr>