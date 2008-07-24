<script type="text/javascript">

printDossierSoin = function(prescription_id, date){
  url = new Url;
  url.setModuleAction("dPprescription", "vw_plan_soin_pdf");
  url.addParam("prescription_id", prescription_id);
  url.popup(900, 600, "Plan de soin");
}

addCibleTransmission = function(object_class, object_id, view) {
  oDiv = $('cibleTrans');
  if(!oDiv) {
    return;
  }
  oForm = document.forms['editTrans'];
  $V(oForm.object_class, object_class);
  $V(oForm.object_id, object_id);
  oDiv.innerHTML = view;
  oForm.text.focus();
}


addAdministration = function(line_id, quantite, key_tab, object_class, date, heure){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_add_administration");
  url.addParam("line_id",  line_id);
  url.addParam("quantite", quantite);
  url.addParam("key_tab", key_tab);
  url.addParam("object_class", object_class);
  url.addParam("date", date);
  url.addParam("heure", heure);
  url.popup(400,300,"Administration");
}


viewLegend = function(){
  var url = new Url;
  url.setModuleAction("dPhospi", "vw_lengende_dossier_soin");
  url.popup(300,150, "Légende");
}


viewDossier = function(prescription_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "vw_dossier_cloture");
  url.addParam("prescription_id", prescription_id);
  url.popup(500,500,"Dossier cloturé");
}

</script>


<table class="tbl">
  <tr>
    <th class="title">{{$sejour->_view}} (Dr {{$sejour->_ref_praticien->_view}})</th>
  </tr>
</table>


{{if $prescription_id}}

	<h2 style="text-align: center">Dossier de soin du {{$date|@date_format:"%d/%m/%Y"}}</h2>
	<table style="width: 100%">
	  <tr>
	    <td>
	      <button type="button" class="print" onclick="printDossierSoin('{{$prescription_id}}','{{$date}}');" title="{{tr}}Print{{/tr}}">
		      Imprimer la feuille de soins immédiate
	      </button>
	      <!-- 
	      <button type="button" class="search" onclick="viewDossier('{{$prescription_id}}');">Dossier cloturé</button>
	       -->
	    </td>
	    <td style="text-align: right">
	      <button type="button" class="search" onclick="viewLegend()">Légende</button>
	    </td>
	  </tr>
	</table>
	<table class="tbl">
	  <tr>
	    <th rowspan="2">Type</th>
	    <th rowspan="2">Libelle</th>
	    <th rowspan="2">Posologie</th>
	    <th colspan="8">Heures</th>
	    <th rowspan="2" colspan="2">Signatures<br /> Prat. / Pharm.</th>
	  </tr>
	  <tr>
	   {{foreach from=$tabHours item=_hour}}
		   <th>{{$_hour}}h</th>           
		 {{/foreach}}
	  </tr>
	  <!-- Affichage des medicaments -->
	  {{if $lines_med|@count}}
	    {{foreach from=$lines_med item=_line name="foreach_med"}}
	
			  {{foreach from=$_line key=unite_prise item=line name="foreach_line"}}       
				
					  <tr id="line_CPrescriptionLineMedicament_{{$line->_id}}">
					    {{if $smarty.foreach.foreach_med.first && $smarty.foreach.foreach_line.first}}
					    <th rowspan="{{$nb_produit_by_cat.med}}">Medicaments</th>
					    {{/if}}
					    {{if $smarty.foreach.foreach_line.first}}
					    <td class="text" rowspan="{{$_line|@count}}">
					    {{assign var=line_id value=$line->_id}}
					    {{assign var=line_class value=$line->_class_name}}
					      <div onclick="addCibleTransmission('{{$line->_class_name}}', '{{$line->_id}}', '{{$line->_view}}');" 
					           class="{{if @$transmissions.$line_class.$line_id|@count}}
					                   transmission
					                   {{else}}
					                    transmission_possible 
					                   {{/if}}">
					        <a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$line->_class_name}}', object_id: {{$line->_id}} } })">
					          {{$line->_ref_produit->libelle}}  
					          {{if $line->_traitement}}(Traitement perso){{/if}}
					        </a>
					      </div>
					    </td>
					    {{/if}}
					    <td class="text">   
						    {{assign var=line_id value=$line->_id}}
						    {{if array_key_exists($line_id, $prises_med) && array_key_exists($unite_prise,$prises_med.$line_id)}}
							    <ul>
							    {{foreach from=$prises_med.$line_id.$unite_prise item=prise name=prises}}
							      <li>
							      {{if $prise->nb_tous_les && $prise->unite_tous_les && $prise->unite_tous_les == "jour"}}
							        {{$prise->quantite}} {{$prise->unite_prise}}
							      {{else}}
							        {{$prise->_view}}
							      {{/if}}
							      </li>
							    {{/foreach}}
							    </ul>
						    {{/if}}
					    </td>
					    <!-- Affichage des heures de prises des medicaments -->
					    {{if count($list_prises_med) && @array_key_exists($unite_prise, $list_prises_med.$line_id)}}
						    {{assign var=prise_line value=$list_prises_med.$line_id.$unite_prise}}
						    {{foreach from=$tabHours item=_hour key=_date_hour}}
					        <td style="text-align: center">
					          {{if (($line->_debut_reel < $_date_hour && $line->_fin_reelle > $_date_hour) || !$line->_fin_reelle) && array_key_exists($_hour, $prise_line)}}
							        {{assign var=quantite value=$prise_line.$_hour}}
							      {{else}}
							        {{assign var=quantite value="-"}}
							      {{/if}}     
						        <div onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_hour}}'} })"
						             class="tooltip-trigger administration
						                   {{if $quantite > 0}}
						                      {{if @array_key_exists($_hour, $administrations.$line_id.$unite_prise)}}
						                        {{if $administrations.$line_id.$unite_prise.$_hour.quantite == $quantite}}
						                          administre
						                        {{elseif $administrations.$line_id.$unite_prise.$_hour.quantite == 0}}
						                          administration_annulee
						                        {{else}}
						                          administration_partielle
						                        {{/if}}
						                      {{else}}
						                        {{if $date == $now|date_format:'%Y-%m-%d' && $_hour < $now|date_format:'%H'}}
						                          non_administre
						                        {{else}}
						                          a_administrer
						                        {{/if}}
						                      {{/if}}
																{{/if}}
																{{if @$transmissions.$line_id.$unite_prise.$_hour.nb}}
																  transmission
																{{/if}}
																"
						              onclick="addAdministration({{$line_id}}, '{{$quantite}}', '{{$unite_prise}}', '{{$line->_class_name}}','{{$date}}','{{$_hour}}');">
							        {{if $quantite!="-" || @array_key_exists($_hour, $administrations.$line_id.$unite_prise)}}
						            {{if @array_key_exists($_hour, $administrations.$line_id.$unite_prise)}}
						              {{$administrations.$line_id.$unite_prise.$_hour.quantite}}
							          {{else}}
							            0
							          {{/if}} / {{$quantite}}
							        {{/if}}
							         </div>
											<div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_hour}}" style="display: none; text-align: left">
											 {{if @array_key_exists($_hour, $administrations.$line_id.$unite_prise) && @array_key_exists("administrations", $administrations.$line_id.$unite_prise.$_hour)}}
							          <ul>
							          {{foreach from=$administrations.$line_id.$unite_prise.$_hour.administrations item=_log_administration}}
							            {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
							            <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		         
								            <ul>
								              {{foreach from=$transmissions.$line_id.$unite_prise.$_hour.list.$administration_id item=_transmission}}
								                <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_transmission->text}}</li>
								              {{/foreach}}
								            </ul>
							          {{/foreach}}
							          </ul>
							        {{else}}
							            Aucune administration
							        {{/if}}
											</div>
					         
					        </td>
						     {{/foreach}}
						   {{else}}
						     {{foreach from=$tabHours item=_hour}}
						     <td style="text-align: center">
						       <div class="tooltip-trigger administration
						                    {{if @$transmissions.$line_id.$unite_prise.$_hour.nb}}
																  transmission
																{{/if}}"
						            onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_hour}}'} })"
						           onclick="addAdministration({{$line_id}}, '', '{{$unite_prise}}', '{{$line->_class_name}}','{{$date}}','{{$_hour}}');">
	       	           {{if @array_key_exists($_hour, $administrations.$line_id.$unite_prise)}}
					             {{$administrations.$line_id.$unite_prise.$_hour.quantite}} / -
					           {{/if}}
					          </div>
					           <div id="tooltip-content-{{$line_id}}-{{$unite_prise}}-{{$_hour}}" style="display: none; text-align: left">
											 {{if @array_key_exists($_hour, $administrations.$line_id.$unite_prise) && @array_key_exists("administrations", $administrations.$line_id.$unite_prise.$_hour)}}
							         <ul>
							          {{foreach from=$administrations.$line_id.$unite_prise.$_hour.administrations item=_log_administration}}
							            {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
							            <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_ref_object->_ref_produit->libelle_unite_presentation}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		         
								            <ul>
								              {{foreach from=$transmissions.$line_id.$unite_prise.$_hour.list.$administration_id item=_transmission}}
								                <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_transmission->text}}</li>
								              {{/foreach}}
								            </ul>
								        {{/foreach}}
								      </ul>
							        {{else}}
							            Aucune administration
							        {{/if}}
									</div>
					       
						     </td>
						     {{/foreach}}
						   {{/if}}
					   <td style="text-align: center">
						    {{if $line->signee}}
						    <img src="images/icons/tick.png" alt="Signée par le praticien" title="Signée par le praticien" />
						    {{else}}
						    <img src="images/icons/cross.png" alt="Non signée par le praticien" title="Non signée par le praticien" />
						    {{/if}}
						</td>
						<td style="text-align: center">
						    {{if $line->valide_pharma}}
						    <img src="images/icons/tick.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
						    {{else}}
						    <img src="images/icons/cross.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
						    {{/if}}
					    </td>
					  </tr>	     
			  {{/foreach}} 		 
		  {{/foreach}}
	  {{/if}}
	  
	  <!-- Affichage des elements -->
	  {{foreach from=$lines_element key=name_chap item=elements_chap}}
	    {{foreach from=$elements_chap key=name_cat item=elements_cat}}
	      {{assign var=categorie value=$categories.$name_chap.$name_cat}}
	      {{foreach from=$elements_cat item=_element name="foreach_cat"}}
	        {{foreach from=$_element key=unite_prise item=element name="foreach_elt"}}   
	         
	        <tr id="line_CPrescriptionLineElement_{{$element->_id}}">
			      {{if $smarty.foreach.foreach_elt.first && $smarty.foreach.foreach_cat.first}}
			      {{assign var=categorie_id value=$categorie->_id}}
			        <th class="{{if @$transmissions.CCategoryPrescription.$categorie_id|@count}}
					                   transmission
					                   {{else}}
					                    transmission_possible 
					                   {{/if}}" 
			            rowspan="{{$nb_produit_by_cat.$name_cat}}" 
			            onclick="addCibleTransmission('CCategoryPrescription', '{{$name_cat}}','{{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}} - {{$categorie->nom}}');">
			              {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}<br /><a href="#">{{$categorie->nom}}</a>
			        </th>
			      {{/if}}

				    <td class="text">
				    {{assign var=element_id value=$element->_id}}
				      <div onclick="addCibleTransmission('{{$element->_class_name}}', '{{$element->_id}}', '{{$element->_view}}');" 
				          class="{{if @$transmissions.CPrescriptionLineElement.$element_id|@count}}
					                   transmission
					                   {{else}}
					                    transmission_possible 
					                   {{/if}}">
					        <a href="#" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$element->_class_name}}', object_id: {{$element->_id}} } })">{{$element->_view}}</a>
				      </div>
				    </td>
	   	      <td class="text">
					    
					    {{if @array_key_exists($element_id, $prises_element) && @array_key_exists($unite_prise, $prises_element.$element_id)}}
					      <ul>
						    {{foreach from=$prises_element.$element_id.$unite_prise item=prise name=prises}}
						    <li>
						      {{if $prise->nb_tous_les && $prise->unite_tous_les && $prise->unite_tous_les == "jour"}}
						        {{$prise->quantite}} {{$prise->_ref_object->_unite_prise}}
						      {{else}}
						        {{$prise->_view}}
						      {{/if}}
						    </li>
						    {{/foreach}}
						    </ul>
					    {{/if}}
				     </td>
					    <!-- Affichage des heures de prises des medicaments -->
					    {{if count($list_prises_element) && @array_key_exists($unite_prise, $list_prises_element.$element_id)}}
						    {{assign var=prise_line value=$list_prises_element.$element_id.$unite_prise}}
						    {{foreach from=$tabHours item=_hour key=_date_hour}}
					        <td style="text-align: center">
						        {{if $element->_debut_reel < $_date_hour && $element->_fin_reelle > $_date_hour && array_key_exists($_hour, $prise_line)}}
							        {{assign var=quantite value=$prise_line.$_hour}}
							      {{else}}
							        {{assign var=quantite value="-"}}
							      {{/if}}        
						        <div onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$name_cat}}-{{$element_id}}-{{$unite_prise}}-{{$_hour}}'} })"
						             class="tooltip-trigger administration
						                   {{if $quantite > 0}}
						                      {{if @array_key_exists($_hour, $administrations.$name_chap.$name_cat.$element_id.$unite_prise)}}
						                        {{if $administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.quantite == $quantite}}
						                          administre
						                        {{elseif $administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour == 0}}
						                          administration_annulee
						                        {{else}}
						                          administration_partielle
						                        {{/if}}
						                      {{else}}
						                        {{if $date == $now|date_format:'%Y-%m-%d' && $_hour < $now|date_format:'%H'}}
						                          non_administre
						                        {{else}}
						                          a_administrer
						                        {{/if}}
						                      {{/if}}
																{{/if}}
																{{if @$transmissions.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.nb}}
																  transmission
																{{/if}}"
						        onclick="addAdministration('{{$element_id}}', '{{$quantite}}', '{{$unite_prise}}', '{{$element->_class_name}}','{{$date}}','{{$_hour}}');">
						          {{if $quantite!="-" || @array_key_exists($_hour, $administrations.$name_chap.$name_cat.$element_id.$unite_prise)}}
						         {{if @array_key_exists($_hour, $administrations.$name_chap.$name_cat.$element_id.$unite_prise)}}
					             {{$administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.quantite}}
					             {{else}}
					             0
					             {{/if}}
					             / {{$quantite}}
					           {{/if}}
						        </div>
						        <div id="tooltip-content-{{$name_cat}}-{{$element_id}}-{{$unite_prise}}-{{$_hour}}" style="display: none; text-align: left">
											 {{if @array_key_exists($_hour, $administrations.$name_chap.$name_cat.$element_id.$unite_prise) && @array_key_exists("administrations", $administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour)}}
							          <ul>
							          {{foreach from=$administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.administrations item=_log_administration}}
							            {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
							            <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_unite_prise}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		         
								         
								            <ul>
								              {{foreach from=$transmissions.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.list.$administration_id item=_transmission}}
								                <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_transmission->text}}</li>
								              {{/foreach}}
								            </ul>
								        
							          {{/foreach}}
							          </ul>
							        {{else}}
							            Pas de {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}
							        {{/if}}
											</div>
					        </td>
						     {{/foreach}}
						   {{else}}
						     {{foreach from=$tabHours item=_hour}}
						     <td style="text-align: center">
						       <div onmouseover="ObjectTooltip.create(this, {mode: 'dom',  params: {element: 'tooltip-content-{{$name_cat}}-{{$element_id}}-{{$unite_prise}}-{{$_hour}}'} })"
						            class="tooltip-trigger administration
						            {{if @$transmissions.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.nb}}
												 transmission
											  {{/if}}"
						        onclick="addAdministration('{{$element_id}}', '', '{{$unite_prise}}', '{{$element->_class_name}}','{{$date}}','{{$_hour}}');">
						         {{if @array_key_exists($_hour, $administrations.$name_chap.$name_cat.$element_id.$unite_prise)}}
						           {{$administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.quantite}} / -
					           {{/if}}
						       </div>
						       <div id="tooltip-content-{{$name_cat}}-{{$element_id}}-{{$unite_prise}}-{{$_hour}}" style="display: none; text-align: left">
										{{if @array_key_exists($_hour, $administrations.$name_chap.$name_cat.$element_id.$unite_prise) && @array_key_exists("administrations", $administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour)}}
							        <ul>
							        {{foreach from=$administrations.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.administrations item=_log_administration}}
							           {{assign var=administration_id value=$_log_administration->_ref_object->_id}}
							            <li>{{$_log_administration->_ref_object->quantite}} {{$_log_administration->_ref_object->_unite_prise}} administré par {{$_log_administration->_ref_user->_view}} le {{$_log_administration->date|date_format:"%d/%m/%Y à %Hh%M"}}</li>		         
								         
								            <ul>
								              {{foreach from=$transmissions.$name_chap.$name_cat.$element_id.$unite_prise.$_hour.list.$administration_id item=_transmission}}
								                <li>{{$_transmission->_view}} le {{$_transmission->date|date_format:"%d/%m/%Y à %Hh%M"}}:<br /> {{$_transmission->text}}</li>
								              {{/foreach}}
								            </ul>
								         
							        {{/foreach}}
							        </ul>
							      {{else}}
							        Pas de {{tr}}CCategoryPrescription.chapitre.{{$name_chap}}{{/tr}}
							      {{/if}}
									</div>
						     </td>
						     {{/foreach}}
						   {{/if}}
					   <td style="text-align: center">
					      {{if $element->signee}}
					        <img src="images/icons/tick.png" alt="Signée par le praticien" title="Signée par le praticien" />
					      {{else}}
					        <img src="images/icons/cross.png" alt="Non signée par le praticien" title="Non signée par le praticien" />
					      {{/if}}
						</td>
						<td style="text-align: center">
					    -
					    </td>
	          </tr>
	        {{/foreach}}
	      {{/foreach}}
	    {{/foreach}}
	  {{/foreach}}
	</table>
{{else}}
	<div class="big-info">
	  Ce dossier ne possède pas de prescription de séjour
	</div>
{{/if}}