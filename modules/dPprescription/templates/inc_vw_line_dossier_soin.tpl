{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}
{{assign var=transmissions_line value=$line->_transmissions}}
{{assign var=administrations_line value=$line->_administrations}}
{{assign var=transmissions value=$prescription->_transmissions}}

{{if $line->_class_name == "CPrescriptionLineMedicament"}}
  {{assign var=nb_lines_chap value=$prescription->_nb_produit_by_chap.$type}}
{{else}}
  {{assign var=nb_lines_chap value=$prescription->_nb_produit_by_chap.$name_chap}}
{{/if}}

<tr id="line_{{$line_class}}_{{$line_id}}_{{$unite_prise|regex_replace:'/[^a-z0-9_-]/i':'_'}}">
  {{if $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
    {{if $line_class == "CPrescriptionLineMedicament"}}
      {{assign var=libelle_ATC value=$line->_ref_produit->_ref_ATC_2_libelle}}
      <!-- Cas d'une ligne de medicament -->
      <th class="text {{if @$transmissions.ATC.$libelle_ATC|@count}}transmission{{else}}transmission_possible{{/if}}" rowspan="{{$prescription->_nb_produit_by_cat.$type.$_key_cat_ATC}}" 
          onclick="addCibleTransmission('','','{{$libelle_ATC}}','{{$libelle_ATC}}');">
	      <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$libelle_ATC}}')">
          {{$libelle_ATC|smarty:nodefaults}}
        </span>
        <div id="tooltip-content-{{$libelle_ATC}}" style="display: none; color: black; text-align: left">
       		{{if @is_array($transmissions.ATC.$libelle_ATC)}}
  		      <ul>
  			  {{foreach from=$transmissions.ATC.$libelle_ATC item=_trans}}
  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:$dPconfig.datetime}}:<br /> {{$_trans->text}}</li>
  			  {{/foreach}}
  		      </ul>
  			  {{else}}
  			    Aucune transmission
  			  {{/if}}
		    </div>
		  
	      {{if $line->_ref_produit->_ref_fiches_ATC}}
	        <img src="images/icons/search.png" onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-{{$_key_cat_ATC}}")' />
	      {{/if}}
      </th>
      <div id="tooltip-content-{{$_key_cat_ATC}}" style="display: none;">
					<strong>Fiches disponibles</strong><br />
          <ul>
          {{foreach from=$line->_ref_produit->_ref_fiches_ATC item=_fiche_ATC}}
	          <li><a href="#{{$_fiche_ATC->_id}}" onclick="viewFicheATC('{{$_fiche_ATC->_id}}')";>Fiche ATC {{if $_fiche_ATC->libelle}}{{$_fiche_ATC->libelle}}{{/if}}</a></li>
	        {{/foreach}}
	        </ul>
      </div>
    {{else}}
        <!-- Cas d'une ligne d'element, possibilité de rajouter une transmission à la categorie -->
        {{assign var=categorie_id value=$categorie->_id}}
        <th class="text {{if @$transmissions.CCategoryPrescription.$name_cat|@count}}transmission{{else}}transmission_possible{{/if}}" 
            rowspan="{{$prescription->_nb_produit_by_cat.$name_cat}}" 
            onclick="addCibleTransmission('CCategoryPrescription','{{$name_cat}}','{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} - {{$categorie->nom}}');">
          <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$name_cat}}')">
            {{$categorie->nom}}
          </span>
          <div id="tooltip-content-{{$name_cat}}" style="display: none; color: black; text-align: left">
       		{{if @is_array($transmissions.CCategoryPrescription.$name_cat)}}
  		      <ul>
  			  {{foreach from=$transmissions.CCategoryPrescription.$name_cat item=_trans}}
  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:$dPconfig.datetime}}:<br /> {{$_trans->text}}</li>
  			  {{/foreach}}
  		      </ul>
  			{{else}}
  			  Aucune transmission
  			{{/if}}
		  </div>
	    </th>
    {{/if}}
  {{/if}}
  
  
  {{if $smarty.foreach.$last_foreach.first}}
    <td class="text" rowspan="{{$nb_line}}"
         {{if $line->_class_name == 'CPrescriptionLineMedicament' && $line->traitement_personnel}}
	       style="background-color: #BDB"
	       {{/if}}>
    {{if $line->_recent_modification}}
      <img style="float: right" src="images/icons/ampoule.png" title="Ligne recemment modifiée"/>
    {{/if}}
    
    {{if is_array($line->_dates_urgences) && array_key_exists($date, $line->_dates_urgences)}}
          <img style="float: right" src="images/icons/ampoule_urgence.png" title="Urgence"/>
    {{/if}}
    
    <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}}">
	  <div onclick='addCibleTransmission("{{$line_class}}","{{$line->_id}}","{{$line->_view}}");' 
	       class="{{if @$transmissions.$line_class.$line_id|@count}}transmission{{else}}transmission_possible{{/if}}">
	    <span onmouseover="ObjectTooltip.createEx(this, '{{$line->_guid}}')">
	      {{if $line_class == "CPrescriptionLineMedicament"}}
	        {{$line->_ucd_view}}  - <span style="font-size: 0.8em">{{$line->_forme_galenique}}</span>
	        {{if $line->traitement_personnel}} (Traitement perso){{/if}}
	        {{if $line->commentaire}}<br /> ({{$line->commentaire}}){{/if}}
	      {{else}}
	        {{$line->_view}}
	      {{/if}} 
	    </span>
	   
	  </div>
	  <small>
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	    {{$line->voie}}
	  {{/if}}
		
    {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		{{if $line->_ref_produit_prescription->unite_prise}}
			({{$line->_ref_produit_prescription->unite_prise}})
		{{else}}
			{{if $line->_unite_administration && ($line->_unite_administration != $line->_forme_galenique)}}
	      <br />
	      ({{$line->_unite_administration}})<br />
	    {{/if}}
		{{/if}}
		{{/if}}
    </small>
    
    {{if $line->conditionnel}}
      <form action="?" method="post" name="activeCondition-{{$line_id}}-{{$line_class}}">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="{{$dosql}}" />
        <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
        <input type="hidden" name="del" value="0" />
        
        {{if !$line->condition_active}}
	      <!-- Activation -->
	      <input type="hidden" name="condition_active" value="1" />
	      <button class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin('','{{$chapitre}}', true); } });">
	        Activer
	      </button>
	      {{else}}
 				<!-- Activation -->
	      <input type="hidden" name="condition_active" value="0" />
	      <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin('','{{$chapitre}}', true); } });">
	        Désactiver
	      </button>
	       {{/if}}
       </form>
		{{/if}}
		
				
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	  
	  {{if !$line->_count.administration}}
	    {{if ($line->_ref_substitution_lines.CPrescriptionLineMedicament|@count || $line->_ref_substitution_lines.CPerfusion|@count) &&
	    			$line->_ref_substitute_for->substitution_plan_soin}}
	    <form action="?" method="post" name="changeLine-{{$line->_guid}}">
	      <input type="hidden" name="m" value="dPprescription" />
	      <input type="hidden" name="dosql" value="do_substitution_line_aed" />
	      <select name="object_guid" style="width: 75px;" 
	              onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { 
	                           loadTraitement(document.form_prescription.sejour_id.value,'{{$date}}','','administration')
	                         } } )">
	        <option value="">Subst.</option>
		      {{foreach from=$line->_ref_substitution_lines item=lines_subst_by_chap}}
		          {{foreach from=$lines_subst_by_chap item=_line_subst}}
							{{if $_line_subst->_class_name == "CPerfusion"}}
							<option value="{{$_line_subst->_guid}}">{{$_line_subst->_short_view}}
              {{else}}
		          <option value="{{$_line_subst->_guid}}">{{$_line_subst->_view}}
		          {{/if}}
							{{if !$_line_subst->substitute_for_id}}(originale){{/if}}</option>
		        {{/foreach}}
		      {{/foreach}}
	      </select>
	    </form>
	    {{/if}}
	    {{/if}}
	    </div>
		</td>
	  {{/if}}
  {{/if}}
  
  <!-- Affichage des posologies de la ligne -->
  <td class="text">
  	{{if !$line->signee && $line->_class_name == "CPrescriptionLineMedicament" && $dPconfig.dPprescription.CPrescription.show_unsigned_med_msg}}
		
		{{else}}
	    <small>
	    {{if @$line->_prises_for_plan.$unite_prise}}
	      {{if is_numeric($unite_prise)}}
	        <!-- Cas des posologies de type "tous_les", "fois par" ($unite_prise == $prise->_id) -->
	        <div style="white-space: nowrap;">
		        {{assign var=prise value=$line->_prises_for_plan.$unite_prise}}
		        {{$prise->_short_view}}
	        </div>
	      {{else}}
	        <!-- Cas des posologies sous forme de moments -->
	        {{foreach from=$line->_prises_for_plan.$unite_prise item=_prise}}
	          <div style="white-space: nowrap;">
	            {{$_prise->_short_view}}
						</div>
	        {{/foreach}}
	      {{/if}}
	    {{/if}}
	    </small>
		{{/if}}
  </td>
  
  {{if $smarty.foreach.$global_foreach.first && $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
  <th class="before" style="cursor: pointer" onclick="showBefore();" rowspan="{{$nb_lines_chap}}" onmouseout="clearTimeout(timeOutBefore);">
   <img src="images/icons/a_left.png" />
  </th>
  {{/if}}
 
	  <td id="first_{{$line_id}}_{{$line_class}}_{{$unite_prise}}" style="display: none;">
	  </td>
	  
		{{if !$line->signee && $line->_class_name == "CPrescriptionLineMedicament" && $dPconfig.dPprescription.CPrescription.show_unsigned_med_msg}}
      {{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
        {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
          <td class="{{$_view_date}}-{{$moment_journee}}" colspan="{{if $moment_journee == 'soir'}}{{$count_soir-2}}{{/if}}
                               {{if $moment_journee == 'nuit'}}{{$count_nuit-2}}{{/if}}
                               {{if $moment_journee == 'matin'}}{{$count_matin-2}}{{/if}}">
		        <div class="small-warning">Ligne non signée</div>
		      </td>      
		      {{/foreach}}
		    {{/foreach}}
   {{else}} 
     {{include file="../../dPprescription/templates/inc_vw_content_line_dossier_soin.tpl" nodebug=true}}
   {{/if}}
	 
	  <td id="last_{{$line_id}}_{{$line_class}}_{{$unite_prise}}" style="display: none;">
	  </td>
 
 {{if $smarty.foreach.$global_foreach.first &&  $smarty.foreach.$first_foreach.first  && $smarty.foreach.$last_foreach.first}}
   <th class="after" style="cursor: pointer" onclick="showAfter();" rowspan="{{$nb_lines_chap}}" onmouseout="clearTimeout(timeOutAfter);">
     <img src="images/icons/a_right.png" />
   </th>
 {{/if}}
 
 <!-- Signature du praticien -->
 <td style="text-align: center">
   {{if $line->signee}}
   <img src="images/icons/tick.png" title="Signée le {{$line->_ref_log_signee->date|date_format:$dPconfig.datetime}} par {{$line->_ref_praticien->_view}}" />
   {{else}}
   <img src="images/icons/cross.png" title="Non signée par le praticien" />
   {{/if}}
 </td>
 <!-- Signature du pharmacien -->
 <td style="text-align: center">
	  {{if $line_class == "CPrescriptionLineMedicament"}}
	    {{if $line->valide_pharma}}
	    <img src="images/icons/tick.png" title="Signée par le pharmacien" />
	    {{else}}
	    <img src="images/icons/cross.png" title="Non signée par le pharmacien" />
	    {{/if}}
	  {{else}}
	    - 
	  {{/if}}
  </td>
</tr>