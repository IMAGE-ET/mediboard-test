<script type="text/javascript">

{{if $move_dossier_soin}}
	Main.add(function () {
	  moveDossierSoin();
    viewDossierSoin('{{$mode_dossier}}');
  
  	// On vide les valeurs du formulaires d'ajout/modification de planification
    document.addPlanif.select('input').each(function(element){
      if(element.name != "dosql" && element.name != "m" && element.name != "del" && element.name != "planification"){
         $V(element, "");
       }
    });
  });
{{/if}}

</script>

</script>
<!-- Affichage des heures de prises des medicaments -->			 
{{foreach from=$tabHours key=_view_date item=_hours_by_moment}}
  {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
    {{foreach from=$_dates key=_date item=_hours}}
      {{foreach from=$_hours key=_heure_reelle item=_hour}}

  {{assign var=list_administrations value=""}}
  {{if @$line->_administrations.$unite_prise.$_date.$_hour.list}}
    {{assign var=list_administrations value=$line->_administrations.$unite_prise.$_date.$_hour.list}}
  {{/if}}
  {{assign var=planification_id value=""}}
    {{assign var=origine_date_planif value=""}}
  {{if @$line->_administrations.$unite_prise.$_date.$_hour.planification_id}}
    {{assign var=planification_id value=$line->_administrations.$unite_prise.$_date.$_hour.planification_id}}
    {{assign var=origine_date_planif value=$line->_administrations.$unite_prise.$_date.$_hour.original_date_planif}}
  {{/if}}
      
  {{assign var=_date_hour value="$_date $_heure_reelle"}}						    

  <!-- S'il existe des prises prevues pour la date $_date -->
   {{if @is_array($line->_quantity_by_date.$unite_prise.$_date) || @$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
		{{assign var=prise_line value=$line->_quantity_by_date.$unite_prise.$_date}}
		
		<!-- Quantites prevues -->
	    {{assign var=quantite value="-"}}
	    {{assign var=quantite_depart value="-"}}
	    {{assign var=heure_reelle value=""}}
	    {{if (($line->_debut_reel <= $_date_hour && $line->_fin_reelle > $_date_hour) || (!$line->_fin_reelle && $line_class == "CPrescriptionLineMedicament")) 
	         && (@array_key_exists($_hour, $prise_line.quantites) || @$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee)}}
	          
	      {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
	        {{assign var=quantite value=$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
	      {{else}}
			    {{assign var=quantite value=$prise_line.quantites.$_hour.total}}
			    {{if @$prise_line.quantites.$_hour.0.heure_reelle}}
			    {{assign var=heure_reelle value=$prise_line.quantites.$_hour.0.heure_reelle}}
			    {{/if}}
		    {{/if}}
		  {{/if}}
		  
		  {{assign var=_quantite value=$quantite}}
		  {{if !$heure_reelle}}
		    {{assign var=heure_reelle value=$_hour}}
		  {{/if}}
		  
     <td id="drop_{{$line_id}}_{{$line_class}}_{{$unite_prise}}_{{$_date}}_{{$_hour}}" 
     		class="{{$line_id}}_{{$line_class}} {{$_view_date}}-{{$moment_journee}} {{if ($quantite == '0' || $quantite == '-')}}canDrop{{/if}} colorPlanif" 
     		style='display: none; text-align: center; {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
   			   
		  <div {{if @is_array($line->_administrations.$unite_prise.$_date.$_hour.administrations) || $origine_date_planif}}
		  					onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}")'
		  		 {{/if}}
		       id="drag_{{$line_id}}_{{$unite_prise}}_{{$_date}}_{{$heure_reelle}}_{{$_quantite}}_{{$planification_id}}"
		       {{if ($line->_fin_reelle && $line->_fin_reelle <= $_date_hour) || $line->_debut_reel > $_date_hour || !$line->_active}}
		      style="background-color: #aaa"
		       
		       
		      {{if $dPconfig.dPprescription.CAdministration.hors_plage}}
		        onclick='toggleSelectForAdministration(this, {{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}","{{$planification_id}}");'
	          ondblclick='addAdministration({{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}","{{$planification_id}}");'
		      {{/if}}
		    {{else}}
		      onclick='toggleSelectForAdministration(this, {{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}","{{$planification_id}}");'
	        ondblclick='addAdministration({{$line_id}}, "{{$quantite}}", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}","{{$planification_id}}");'
		    {{/if}}
		    class="{{if $quantite && $quantite!="-" && @$prise_line.quantites.$_hour|@count < 4}}
		      draggablePlanif
		    {{/if}}  
		      tooltip-trigger administration
	      {{if $quantite > 0}}
			    {{if @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)
			         && @$line->_administrations.$unite_prise.$_date.$_hour.quantite != ''}}
				     {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite == $quantite}} administre
				     {{elseif @$line->_administrations.$unite_prise.$_date.$_hour.quantite == 0}} administration_annulee
				     {{else}} administration_partielle
				     {{/if}}
			     {{else}}
			       {{if $_date_hour < $now}} non_administre
				     {{else}} a_administrer
				     {{/if}}
			     {{/if}}
		     {{/if}}
			 {{if @$line->_transmissions.$unite_prise.$_date.$_hour.nb}}transmission{{/if}}
			 {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
			 planification{{/if}}">
			 
       {{if $quantite!="-" || @array_key_exists($_hour, $line->_administrations.$unite_prise.$_date)}}
				 {{if !$quantite}}
				   {{assign var=quantite value="0"}}
				 {{/if}}
				 {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
				    {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite}}
				      {{$line->_administrations.$unite_prise.$_date.$_hour.quantite}}
				    {{else}}
				      0
				    {{/if}}
				    /{{$line->_administrations.$unite_prise.$_date.$_hour.quantite_planifiee}}
				 {{else}}
		       {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite}}
			       {{$line->_administrations.$unite_prise.$_date.$_hour.quantite}}/{{$quantite}}
		       {{elseif $line->_active}}
			       {{if $quantite}}0/{{$quantite}}{{/if}}
			     {{/if}}
				 {{/if}}
		   {{/if}}
		   
		   
			</div>
	    <script type="text/javascript">
	      // $prise_line.quantites.$_hour|@count < 4 => pour empecher de deplacer une case ou il y a plusieurs prises
          {{if $quantite && @$prise_line.quantites.$_hour|@count < 4}}
		      drag = new Draggable("drag_{{$line_id}}_{{$unite_prise}}_{{$_date}}_{{$heure_reelle}}_{{$_quantite}}_{{$planification_id}}", oDragOptions);
		    {{/if}}
		  </script>
         
		    <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}" style="display: none; text-align: left">
			     {{if $planification_id}}
			       <strong>Date d'origine:</strong> {{$origine_date_planif|date_format:$dPconfig.datetime}}<br />
			     {{/if}}
			     {{if @is_array($line->_administrations.$unite_prise.$_date.$_hour.administrations)}}
			       <strong>Administrations:</strong>
			     <ul>
					   {{foreach from=$line->_administrations.$unite_prise.$_date.$_hour.administrations item=_log_administration}}
					     {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
					     {{if $line_class == "CPrescriptionLineMedicament"}}
						     <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->_ref_object->dateTime|date_format:$dPconfig.datetime}}</li>		 
						   {{else}}
							   <li>{{$_log_administration->_ref_object->quantite}} {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} effectué par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->_ref_object->dateTime|date_format:$dPconfig.datetime}}</li>		         				        
						   {{/if}}        
						   <ul>
						     {{foreach from=$line->_transmissions.$unite_prise.$_date.$_hour.list.$administration_id item=_transmission}}
							     <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:$dPconfig.datetime}}:<br /> {{$_transmission->text}}</li>
						     {{/foreach}}
						   </ul>
					   {{/foreach}}
				   </ul>
			     {{/if}}
       </div>
	   </td>
   {{else}}
      <td class="{{$_view_date}}-{{$moment_journee}} canDrop colorPlanif"
          id="drop_{{$line_id}}_{{$line_class}}_{{$unite_prise}}_{{$_date}}_{{$_hour}}"
          style='display: none; text-align: center; {{if array_key_exists("$_date $_hour:00:00", $operations)}}border-right: 3px solid black;{{/if}}'>
	     <div class="tooltip-trigger administration  {{if @$line->_transmissions.$unite_prise.$_date.$_hour.nb}}transmission{{/if}}"
	            {{if @is_array($line->_administrations.$unite_prise.$_date.$_hour.administrations)}}
	          		onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}")'
	            {{/if}}
	            {{if ($line->_fin_reelle && $line->_fin_reelle <= $_date_hour) || $line->_debut_reel > $_date_hour || !$line->_active}}
                   style="background-color: #aaa"
                 {{else}}
                   onclick='toggleSelectForAdministration(this, {{$line_id}}, "", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}");'
                   ondblclick='addAdministration({{$line_id}}, "", "{{$unite_prise}}", "{{$line_class}}","{{$_date}}","{{$_hour}}","{{$list_administrations}}");'
                 {{/if}}
                 >
   	          {{if @$line->_administrations.$unite_prise.$_date.$_hour.quantite}}
                {{$line->_administrations.$unite_prise.$_date.$_hour.quantite}} / -
              {{/if}}
           </div>
           <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_date}}-{{$_hour}}" style="display: none; text-align: left">
	         {{if @is_array($line->_administrations.$unite_prise.$_date.$_hour.administrations)}}
		         <ul>
				       {{foreach from=$line->_administrations.$unite_prise.$_date.$_hour.administrations item=_log_administration}}
				         {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
				           {{if $line_class == "CPrescriptionLineMedicament"}}
						     <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:$dPconfig.datetime}}</li>		 
						   {{else}}
							 <li>{{$_log_administration->_ref_object->quantite}} {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} effectué par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:$dPconfig.datetime}}</li>		         				        
						   {{/if}}
					       <ul>
					        {{foreach from=$line->_transmissions.$unite_prise.$_date.$_hour.list.$administration_id item=_transmission}}
					          <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:$dPconfig.datetime}}:<br /> {{$_transmission->text}}</li>
					        {{/foreach}}
					        </ul>
					     {{/foreach}}
			       </ul>
		       {{/if}}
		    </div>
	     </td>
	   {{/if}}
     {{/foreach}}
    {{/foreach}}		   
  {{/foreach}}
{{/foreach}}