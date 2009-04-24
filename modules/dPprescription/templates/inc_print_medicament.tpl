{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$med->_protocole}}
<!-- Affichage normal -->
<li>{{if !$praticien->_id}}({{$med->_ref_praticien->_view}}){{/if}}
  <strong>{{$med->_ucd_view}} ({{$med->voie}}) {{if $med->conditionnel}}(Conditionnel){{/if}}</strong>
  <ul>
    {{if $med->_ref_prises|@count || $med->_duree_prise || $med->date_arret || $med->_specif_prise}}
    <li>
		  <!-- Affichage des prises s'il y en a -->
		  {{foreach from=$med->_ref_prises item=prise}}
		    {{if $prise->quantite}}
		      {{$prise->_view}}, 
		    {{/if}}
		  {{foreachelse}}
		  <!-- Sinon, affichage de la posologie selectionnée -->
		    {{$med->_ref_posologie->_view}}
		  {{/foreach}}
		  
		  <!-- Duree de la prise --> 
		  {{$med->_duree_prise}}
		  
		  {{if $med->date_arret}}
		    (Médicament arrêté le {{$med->date_arret|date_format:"%d/%m/%Y"}}) 
		  {{/if}}
		  
		  <!-- Commentaire -->
		  {{if $med->commentaire}}
		  <em>, {{$med->commentaire}}</em>
		  {{/if}}
		  
		  <!-- Remarque sur la prise -->
		  {{if $med->_specif_prise && $med->_ref_prises|@count}}
		    <br />({{$med->_specif_prise}})
		  {{/if}}
    </li>
    {{/if}}
  </ul>
</li>
{{else}}
<!-- Affichage dans le cas d'un protocole -->
<li>
  <strong>{{$med->_ucd_view}} {{if $med->conditionnel}}(Conditionnel){{/if}}</strong>
  <ul>
    <li>
		  <!-- Affichage des prises s'il y en a -->
		  {{foreach from=$med->_ref_prises item=prise}}
		    {{if $prise->quantite}}
		      {{$prise->_view}}, 
		    {{/if}}
		  {{foreachelse}}
		  <!-- Sinon, affichage de la posologie selectionnée -->
		    {{$med->_ref_posologie->_view}}
		  {{/foreach}}
		  
		  <!-- Duree de la prise --> 
		 {{if $med->duree && $med->duree != "1"}}
     Durée de {{mb_value object=$med field=duree}} jour(s) 
    {{/if}}
    
    <!-- Date de debut de la ligne -->
    {{if $med->jour_decalage && $med->unite_decalage}} 
      {{if $med->duree > 1 || $med->jour_decalage_fin}}
	    à partir de
	    {{else}}
	    à
	    {{/if}}
			{{if $prescription->object_class == "CSejour"}}
			{{assign var=line_jour_decalage value=$med->jour_decalage}}
			 {{$traduction.$line_jour_decalage}}
			{{else}}
			 J
			{{/if}}
			
			{{if ($med->unite_decalage == "jour" && $med->decalage_line != 0) || ($med->unite_decalage == "heure")}}
				{{if $med->decalage_line >= 0}}+{{/if}} 
				{{mb_value object=$med field=decalage_line size="3"}}
				{{if $prescription->object_class == "CSejour"}} 
				  {{mb_value object=$med field=unite_decalage}}
				{{else}}
				  (jours)
				{{/if}} 
			{{/if}}
			
			
			 <!-- Heure de debut -->
			 {{if $med->time_debut}}
				 à {{mb_value object=$med field=time_debut}}
			 {{/if}}
		 {{/if}}
		 
		 
		 {{if $med->jour_decalage_fin && $med->unite_decalage_fin}}
		   {{assign var=line_jour_decalage_fin value=$med->jour_decalage_fin}}
			 <!-- Date de fin -->
			 jusqu'à {{$traduction.$line_jour_decalage_fin}}
			 
			 {{if ($med->unite_decalage_fin == "jour" && $med->decalage_line_fin != 0) || ($med->unite_decalage_fin == "heure")}}
				 {{if $med->decalage_line_fin >= 0}}+{{/if}} {{mb_value object=$med field=decalage_line_fin increment=1 }}
				 {{mb_value object=$med field=unite_decalage_fin }}
			 {{/if}}
			 <!-- Heure de fin -->
			 {{if $med->time_fin}} 
				à {{mb_value showPlus=1 object=$med field=time_fin}}		
			 {{/if}}	
		 {{elseif $med->jour_decalage && !$med->duree && $prescription->type == "sejour"}}
		    jusqu'à la fin du séjour.
		 {{/if}}
		 
		 {{if !$med->duree && !($med->jour_decalage && $med->unite_decalage) && !($med->jour_decalage_fin && $med->unite_decalage_fin)}}
		 Aucune date
		 {{/if}}
     {{if $med->commentaire}}
       , {{mb_value object=$med field="commentaire"}}
     {{/if}}
      
		  
		 <!-- Remarque sur la prise -->
		 {{if $med->_specif_prise && $med->_ref_prises|@count}}
		   <br />({{$med->_specif_prise}})
		 {{/if}}
    </li>
  </ul>
</li>
{{/if}}