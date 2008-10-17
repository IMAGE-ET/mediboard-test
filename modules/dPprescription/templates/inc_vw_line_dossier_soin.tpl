{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}
{{assign var=transmissions_line value=$line->_transmissions}}
{{assign var=administrations_line value=$line->_administrations}}

<tr id="line_{{$line_class}}_{{$line_id}}">
  {{if $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
    {{if $line_class == "CPrescriptionLineMedicament"}}
      <!-- Cas d'une ligne de medicament -->
      <th class="text" rowspan="{{$prescription->_nb_produit_by_cat.$type}}">
        {{$line->_ref_produit->_ref_ATC_2_libelle}}
      </th>
    {{else}}
        <!-- Cas d'une ligne d'element, possibilité de rajouter une transmission à la categorie -->
        {{assign var=categorie_id value=$categorie->_id}}
        <th class="text {{if @$transmissions.CCategoryPrescription.$categorie_id|@count}}transmission{{else}}transmission_possible{{/if}}" 
            rowspan="{{$prescription->_nb_produit_by_cat.$type}}" 
            onclick="addCibleTransmission('CCategoryPrescription','{{$type}}','{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} - {{$categorie->nom}}');">
          <div class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$type}}'} })">
            <a href="#">{{$categorie->nom}}</a>
          </div>
          <div id="tooltip-content-{{$type}}" style="display: none; color: black; text-align: left">
       		{{if @is_array($transmissions.CCategoryPrescription.$type)}}
  		      <ul>
  			  {{foreach from=$transmissions.CCategoryPrescription.$type item=_trans}}
  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_trans->text}}</li>
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
    <td class="text" rowspan="{{$nb_line}}" style="text-align: center">
    {{if !$line->conditionnel}}
     -
    {{else}}
      <form action="?" method="post" name="activeCondition-{{$line_id}}-{{$line_class}}">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="{{$dosql}}" />
        <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
        <input type="hidden" name="del" value="0" />
        
        {{if !$line->condition_active}}
	      <!-- Activation -->
	      <input type="hidden" name="condition_active" value="1" />
	      <button class="tick" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin(); } });">
	        Activer
	      </button>
	      {{else}}
 				<!-- Activation -->
	      <input type="hidden" name="condition_active" value="0" />
	      <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ refreshDossierSoin(); } });">
	        Désactiver
	      </button>
	       {{/if}}
       </form>
		{{/if}}
    </td>
    <td class="text" rowspan="{{$nb_line}}">
	  <div onclick="addCibleTransmission('{{$line_class}}', '{{$line->_id}}', '{{$line->_view}}');" 
	       class="{{if @$transmissions.$line_class.$line_id|@count}}transmission{{else}}transmission_possible{{/if}}">
	    <a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$line_class}}', object_id: {{$line->_id}} } })">
	      {{if $line_class == "CPrescriptionLineMedicament"}}
	        {{$line->_ucd_view}}
	        {{if $line->_traitement}} (Traitement perso){{/if}}
	        {{if $line->commentaire}}<br /> ({{$line->commentaire}}){{/if}}
	      {{else}}
	        {{$line->_view}}
	      {{/if}} 
	    </a>
	  </div>
	  {{if $line->_class_name == "CPrescriptionLineMedicament" && $line->_ref_substitution_lines|@count}}
    <form action="?" method="post" name="changeLine-{{$line_id}}">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_substitution_line_aed" />
      <select name="prescription_line_medicament_id" style="width: 75px;" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { refreshDossierSoin(); } } )">
        <option value="">Conserver</option>
      {{foreach from=$line->_ref_substitution_lines item=_line_subst}}
        <option value="{{$_line_subst->_id}}">{{$_line_subst->_view}}
        {{if !$_line_subst->substitute_for}}(originale){{/if}}</option>
      {{/foreach}}
      </select>
    </form>
    {{/if}}
	</td>
  {{/if}}
  
  
  <!-- Affichage des posologies de la ligne -->
  <td class="text">
    <small>
    {{if @$line->_prises_for_plan.$unite_prise}}
      {{if is_numeric($unite_prise)}}
        <!-- Cas des posologies de type "tous_les", "fois par" ($unite_prise == $prise->_id) -->
        <div style="white-space: nowrap;">
	        {{assign var=prise value=$line->_prises_for_plan.$unite_prise}}
	        {{$prise->_short_view}}
	        <br />
	        {{if $line->_class_name == "CPrescriptionLineMedicament"}}
	          ({{$prise->_ref_object->_unite_administration}})<br />
	        {{/if}}
        </div>
      {{else}}
        <!-- Cas des posologies sous forme de moments -->
        {{foreach from=$line->_prises_for_plan.$unite_prise item=_prise}}
          <div style="white-space: nowrap;">
            {{$_prise->_short_view}}
					</div>
        {{/foreach}}
        {{if $line->_class_name == "CPrescriptionLineMedicament"}}
          ({{$_prise->_ref_object->_unite_administration}})<br />
        {{/if}}
      {{/if}}
    {{/if}}
    </small>
  </td>
  
  <!-- Affichage des heures de prises des medicaments -->			    
  {{foreach from=$tabHours item=_hours_by_date key=_date}}
	  {{foreach from=$_hours_by_date item=_hour}}
		  {{assign var=list_administrations value=""}}
		  {{if @$line->_administrations.$unite_prise.$_date.$_hour.list}}
		    {{assign var=list_administrations value=$line->_administrations.$unite_prise.$_date.$_hour.list}}
		  {{/if}}
		  {{assign var=_date_hour value="$_date $_hour:00:00"}}						    
		
		  <!-- S'il existe des prises prevues pour la date $_date -->
	    {{if @is_array($line->_quantity_by_date.$unite_prise.$_date)}}
				{{assign var=prise_line value=$line->_quantity_by_date.$unite_prise.$_date}}
				
	      <td style="text-align: center" class="{{$_date_hour}}">
			    {{assign var=quantite value="-"}}
			    {{if (($line->_debut_reel < $_date_hour && $line->_fin_reelle > $_date_hour) || (!$line->_fin_reelle && $line_class == "CPrescriptionLineMedicament")) && array_key_exists($_hour, $prise_line.quantites)}}
				    {{assign var=quantite value=$prise_line.quantites.$_hour}}
				  {{/if}}
				  
				  <div onmouseover='ObjectTooltip.create(this, {mode: "dom",  params: {element: "tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}"} })'
				    {{if ($line->_fin_reelle && $line->_fin_reelle <= $_date_hour) || $line->_debut_reel > $_date_hour || !$line->_active}}
				      style="background-color: #aaa"
				    {{else}}
				      onclick='toggleSelectForAdministration(this, {{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}");'
			        ondblclick='addAdministration({{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}");'
				    {{/if}}
				    class="tooltip-trigger administration
			      {{if $quantite > 0}}
					    {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
						     {{if $line->_administrations.$unite_prise.$_date.$_hour.quantite == $quantite}} administre
						     {{elseif $line->_administrations.$unite_prise.$_date.$_hour.quantite == 0}} administration_annulee
						     {{else}} administration_partielle
						     {{/if}}
					     {{else}}
					       {{if $_date_hour < $now}} non_administre
						     {{else}} a_administrer
						     {{/if}}
					     {{/if}}
				     {{/if}}
					 {{if @$line->_transmissions.$unite_prise.$_date.$_hour.nb}}transmission{{/if}}">
		       {{if $quantite!="-" || @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
						 {{if !$quantite}}
						   {{assign var=quantite value="0"}}
						 {{/if}}
			       {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
				       {{$line->_administrations.$unite_prise.$_date.$_hour.quantite}}/{{$quantite}}
			       {{elseif $line->_active}}
				       {{if $quantite}}0/{{$quantite}}{{/if}}
				     {{/if}}

				   {{/if}}
					</div>
			    
			    <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}" style="display: none; text-align: left">
				   {{if @is_array($line->_administrations.$unite_prise.$_date.$_hour.administrations)}}
				     <ul>
						   {{foreach from=$line->_administrations.$unite_prise.$_date.$_hour.administrations item=_log_administration}}
						     {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
						     {{if $line_class == "CPrescriptionLineMedicament"}}
							     <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		 
							   {{else}}
								   <li>{{$_log_administration->_ref_object->quantite}} {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} effectué par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		         				        
							   {{/if}}        
							   <ul>
							     {{foreach from=$line->_transmissions.$unite_prise.$_date.$_hour.list.$administration_id item=_transmission}}
								     <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_transmission->text}}</li>
							     {{/foreach}}
							   </ul>
						   {{/foreach}}
					   </ul>
			     {{else}}
				     {{if $line_class == "CPrescriptionLineMedicament"}}
				       Aucune administration
				     {{else}}
				       Pas de {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}
				     {{/if}}
			   {{/if}}
	       </div>
		   </td>
	   {{else}}
	     <td style="text-align: center" class="{{$_date_hour}}">
		     <div class="tooltip-trigger administration  {{if @$line->_transmissions.$unite_prise.$_date.$_hour.nb}}transmission{{/if}}"
		          onmouseover='ObjectTooltip.create(this, {mode: "dom",  params: {element: "tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}"} })'
		            {{if ($line->_fin_reelle && $line->_fin_reelle <= $_date_hour) || $line->_debut_reel > $_date_hour || !$line->_active}}
                    style="background-color: #aaa"
                  {{else}}
                    onclick='toggleSelectForAdministration(this, {{$line_id}}, "", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}");'
                    ondblclick='addAdministration({{$line_id}}, "", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}");'
                  {{/if}}
                  >
    	          {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
	                {{$line->_administrations.$unite_prise.$_date.$_hour.quantite}} / -
	              {{/if}}
	           </div>
	           <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}" style="display: none; text-align: left">
		         {{if @is_array($line->_administrations.$unite_prise.$_date.$_hour.administrations)}}
			         <ul>
					       {{foreach from=$line->_administrations.$unite_prise.$_date.$_hour.administrations item=_log_administration}}
					         {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
					           {{if $line_class == "CPrescriptionLineMedicament"}}
							     <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		 
							   {{else}}
								 <li>{{$_log_administration->_ref_object->quantite}} {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} effectué par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		         				        
							   {{/if}}
						       <ul>
						        {{foreach from=$line->_transmissions.$unite_prise.$_date.$_hour.list.$administration_id item=_transmission}}
						          <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_transmission->text}}</li>
						        {{/foreach}}
						        </ul>
						     {{/foreach}}
				       </ul>
			       {{else}}
			         {{if $line_class == "CPrescriptionLineMedicament"}}
			           Aucune administration
			         {{else}}
			           Pas de {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}
			         {{/if}}
			       {{/if}}
			    </div>
		     </td>
		 {{/if}}
	 {{/foreach}}
 {{/foreach}}		   

 <!-- Signature du praticien -->
 <td style="text-align: center">
   {{if $line->signee}}
   <img src="images/icons/tick.png" alt="Signée par le praticien" title="Signée par le praticien" />
   {{else}}
   <img src="images/icons/cross.png" alt="Non signée par le praticien" title="Non signée par le praticien" />
   {{/if}}
 </td>
 <!-- Signature du pharmacien -->
 <td style="text-align: center">
	  {{if $line_class == "CPrescriptionLineMedicament"}}
	    {{if $line->valide_pharma}}
	    <img src="images/icons/tick.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
	    {{else}}
	    <img src="images/icons/cross.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
	    {{/if}}
	  {{else}}
	    - 
	  {{/if}}
  </td>
</tr>	 