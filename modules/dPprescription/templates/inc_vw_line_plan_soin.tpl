{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}
<tr>
  <!-- Affichage du libelle -->
  <td class="text"  style="border: 1px solid #ccc;">
    {{if $line->_class_name == "CPrescriptionLineMedicament"}}
      {{$line->_ucd_view}}
      - {{$line->voie}}
	  {{else}}
      {{$line->_view}}
    {{/if}}
    {{if $line->commentaire}}
      ({{$line->commentaire}})
    {{/if}}
  </td>
  <!-- Affichage des prises -->
  <td class="text" style="border: 1px solid #ccc;">
    <small>
     {{if @$line->_prises_for_plan.$unite_prise}}
      {{if is_numeric($unite_prise)}}
        <!-- Cas des posologies de type "tous_les", "fois par" ($unite_prise == $prise->_id) -->
        {{assign var=prise value=$line->_prises_for_plan.$unite_prise}}
        <ul>
          <li>{{$prise->_short_view}}</li>
        </ul>
        {{if $line->_class_name == "CPrescriptionLineMedicament"}}
          ({{$prise->_ref_object->_unite_administration}})<br />
        {{/if}}
      {{else}}
        <!-- Cas des posologies sous forme de moments -->
        <ul>
        {{foreach from=$line->_prises_for_plan.$unite_prise item=_prise}}
          <li>{{$_prise->_short_view}}</li>
        {{/foreach}}
        </ul>
        {{if $line->_class_name == "CPrescriptionLineMedicament"}}
          ({{$_prise->_ref_object->_unite_administration}})<br />
        {{/if}}
      {{/if}}
     {{/if}}
    </small>
  </td>
  <!-- Affichage du praticien responsable de la ligne -->
  <td class="text" style="text-align: center">
    {{if $line_class == "CPrescriptionLineMedicament" && $line->_traitement}}
      Traitement Personnel
    {{/if}}
    {{if !$line->_traitement}}{{$line->_ref_praticien->_view}}{{/if}}
  </td>
  <!-- Affichage des signatures de la ligne -->
  <td style="border-left: 1px solid #ccc; text-align: center">
    {{if $line_class == "CPrescriptionLineMedicament" && !$line->signee && !$line->valide_pharma}}
	  DP
    {{else}}
	  {{if !$line->signee}}D{{/if}}
	  {{if $line_class == "CPrescriptionLineMedicament" && !$line->valide_pharma}}P{{/if}}
    {{/if}}
  </td>

  <!-- Affichage des heures de prises des medicaments -->			 
  {{foreach from=$tabHours item=_hours_by_moment}}
    {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
      {{foreach from=$_dates key=_date item=_hours  }}
        {{foreach from=$_hours key=_heure_reelle item=_hour}}
        
        {{assign var=_date_hour value="$_date $_heure_reelle"}}			
				  <td style="padding: 0; width: 0.5cm; border: 1px solid #ccc; text-align: center">
				    {{assign var=quantite value=""}}  
				    
				    {{if @is_array($line->_quantity_by_date.$unite_prise.$_date.quantites)
				         || @$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
				   
				       {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
					        {{assign var=quantite value=$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
					     {{else}}
				           {{assign var=prise_line value=$line->_quantity_by_date.$unite_prise.$_date}}
					         {{assign var=quantite value=$prise_line.quantites.$_hour.total}}
				      {{/if}}
				    {{/if}}
				    
				    {{if $quantite == 0}}
				      {{assign var=quantite value=""}}
				    {{/if}}
				    
				    {{if $line_class == "CPrescriptionLineMedicament"}}
						  {{if $line->_debut_reel > $_date_hour || ($line->_fin_reelle && $line->_fin_reelle < $_date_hour) || !$line->_active}}
						    <img src="images/icons/gris.gif" />
						  {{else}}
						   {{$quantite}}
						  {{/if}}
				    {{else}}
				      {{if $line->_debut_reel > $_date_hour || $line->_fin_reelle < $_date_hour || !$line->_active}}
			          <img src="images/icons/gris.gif" />
			        {{else}}
			          {{$quantite}}
			        {{/if}}
				    {{/if}}
				  </td>
		    {{/foreach}}
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</tr>