{{assign var=line_id value=$line->_id}}
{{assign var=line_class value=$line->_class_name}}
{{assign var=transmissions_line value=$line->_transmissions}}
{{assign var=administrations_line value=$line->_administrations}}
	
<tr id="line_{{$line_class}}_{{$line_id}}">
  {{if $smarty.foreach.$first_foreach.first && $smarty.foreach.$last_foreach.first}}
    {{if $line_class == "CPrescriptionLineMedicament"}}
      <!-- Cas d'une ligne de medicament -->
      <th rowspan="{{$prescription->_nb_produit_by_cat.$type}}">
        Medicaments
      </th>
    {{else}}
        <!-- Cas d'une ligne d'element, possibilité de rajouter une transmission à la categorie -->
        {{assign var=categorie_id value=$categorie->_id}}
        <th class="{{if @$transmissions.CCategoryPrescription.$categorie_id|@count}}transmission{{else}}transmission_possible{{/if}}" 
            rowspan="{{$prescription->_nb_produit_by_cat.$type}}" 
            onclick="addCibleTransmission('CCategoryPrescription','{{$type}}','{{tr}}CCategoryPrescription.chapitre.{{$type}}{{/tr}} - {{$categorie->nom}}');">
          <div class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$type}}'} })">
            {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}<br /><a href="#">{{$categorie->nom}}</a>
          </div>
          <div id="tooltip-content-{{$type}}" style="display: none; color: black; text-align: left">
       		{{if array_key_exists("CCategoryPrescription", $transmissions) && array_key_exists($type, $transmissions.CCategoryPrescription)}}
  		      <ul>
  			  {{foreach from=$transmissions.CCategoryPrescription.$type item=_trans}}
  			    <li>{{$_trans->_view}} le {{$_trans->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_trans->text}}</li>
  			  {{/foreach}}
  		      </ul>
  			{{else}}
  			  Pas de {{tr}}CCategoryPrescription.chapitre.{{$type}}{{/tr}}
  			{{/if}}
		  </div>           
	    </th>         
    {{/if}}
  {{/if}}			      
  {{if $smarty.foreach.$last_foreach.first}}
    <td class="text" rowspan="{{$nb_line}}" style="text-align: center">
    {{if !$line->conditionnel}}
     -
    {{/if}}
    {{if !$line->_active}}
      <form action="?" method="post" name="activeCondition-{{$line_id}}-{{$line_class}}">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="{{$dosql}}" />
        <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
        <input type="hidden" name="debut" value="{{$real_date}}" />
        <input type="hidden" name="time_debut" value="{{$real_time}}" />
        <input type="hidden" name="del" value="0" />
        {{mb_field object=$line field="condition_active" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete: 
          function(){ refreshDossierSoin(); } });"}}
        {{mb_label object=$line field="condition_active" typeEnum="checkbox"}}
      </form>
    {{/if}}
    {{if $line->_active && $line->conditionnel}}
    Actif
    {{/if}}
    </td>
    <td class="text" rowspan="{{$nb_line}}">
	  <div onclick="addCibleTransmission('{{$line_class}}', '{{$line->_id}}', '{{$line->_view}}');" 
	       class="{{if @$transmissions.$line_class.$line_id|@count}}transmission{{else}}transmission_possible{{/if}}">
	    <a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$line_class}}', object_id: {{$line->_id}} } })">
	      {{if $line_class == "CPrescriptionLineMedicament"}}
	        {{$line->_ref_produit->libelle}}       
	        {{if $line->_traitement}} (Traitement perso){{/if}}
	        {{if $line->commentaire}}<br /> ({{$line->commentaire}}){{/if}}
	      {{else}}
	        {{$line->_view}}
	      {{/if}} 
	    </a>
	  </div>
	</td>
  {{/if}}
  <td class="text">   
    {{if array_key_exists($line_id, $prescription->_intitule_prise.$suffixe)}}
      {{if is_numeric($unite_prise)}}
        <ul>
          <li>{{$prescription->_intitule_prise.$suffixe.$line_id.autre.$unite_prise}}</li>
        </ul>
      {{else}}
        <ul>
        {{if array_key_exists($unite_prise, $prescription->_intitule_prise.$suffixe.$line_id)}}
          {{foreach from=$prescription->_intitule_prise.$suffixe.$line_id.$unite_prise item=_prise}}
            <li>{{$_prise}}</li>
          {{/foreach}}
        {{/if}}
        </ul>
      {{/if}}
    {{/if}}
  </td>
  <!-- Affichage des heures de prises des medicaments -->			    
  {{foreach from=$tabHours item=_hours_by_date key=_date}}
    {{assign var=prise_line value=""}}
	{{if array_key_exists($_date, $list_prises.$suffixe) && array_key_exists($line_id, $list_prises.$suffixe.$_date) && array_key_exists($unite_prise, $list_prises.$suffixe.$_date.$line_id)}}
      {{assign var=prise_line value=$list_prises.$suffixe.$_date.$line_id.$unite_prise}}
	{{/if}}
	{{foreach from=$_hours_by_date item=_hour}}
	  {{assign var=list_administrations value=""}}
	  {{if is_array(@$line->_administrations.$unite_prise) && array_key_exists($_date, $line->_administrations.$unite_prise) && array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
	    {{assign var=list_administrations value=$line->_administrations.$unite_prise.$_date.$_hour.list}}
	  {{/if}}
	  {{assign var=_date_hour value="$_date $_hour:00:00"}}						    
	  {{if array_key_exists($_date, $list_prises.$suffixe) && @array_key_exists($unite_prise, $list_prises.$suffixe.$_date.$line_id)}}
	      <td style="text-align: center" class="{{$_date_hour}}">
		    {{assign var=quantite value="-"}}
		    {{if (($line->_debut_reel < $_date_hour && $line->_fin_reelle > $_date_hour) || (!$line->_fin_reelle && $line_class == "CPrescriptionLineMedicament")) && array_key_exists($_hour, $prise_line)}}
			  {{assign var=quantite value=$prise_line.$_hour}}
			{{/if}}
			<div onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}'} })"
			     {{if ($line->_fin_reelle && $line->_fin_reelle <= $_date_hour) || $line->_debut_reel > $_date_hour || !$line->_active}}
			       style="background-color: #aaa"
			     {{else}}
			       onclick="addAdministration({{$line_id}}, '{{$quantite}}', '{{$unite_prise}}', '{{$line_class}}','{{$_date}}','{{$_hour}}','{{$list_administrations}}');"
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
			     {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
				   {{$line->_administrations.$unite_prise.$_date.$_hour.quantite}}
			     {{else}}
				   0
				 {{/if}} / {{$quantite}}
			   {{/if}}
			 </div>
			 <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}" style="display: none; text-align: left">
	           {{if @array_key_exists($_date, $line->_administrations.$unite_prise) && @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date) && array_key_exists("administrations", $line->_administrations.$unite_prise.$_date.$_hour)}}
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
		            onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}'} })"
		            {{if ($line->_fin_reelle && $line->_fin_reelle <= $_date_hour) || $line->_debut_reel > $_date_hour || !$line->_active}}
                    style="background-color: #aaa"
                  {{else}}
                    onclick="addAdministration({{$line_id}}, '', '{{$unite_prise}}', '{{$line_class}}','{{$_date}}','{{$_hour}}','{{$list_administrations}}');"
                  {{/if}}
                  >
    	          {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
	                {{$line->_administrations.$unite_prise.$_date.$_hour.quantite}} / -
	              {{/if}}
	           </div>
	           <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}" style="display: none; text-align: left">
		         {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date) && @array_key_exists("administrations", $line->_administrations.$unite_prise.$_date.$_hour)}}
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
     <td style="text-align: center">
	    {{if $line->signee}}
	    <img src="images/icons/tick.png" alt="Signée par le praticien" title="Signée par le praticien" />
	    {{else}}
	    <img src="images/icons/cross.png" alt="Non signée par le praticien" title="Non signée par le praticien" />
	    {{/if}}
	  </td>
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