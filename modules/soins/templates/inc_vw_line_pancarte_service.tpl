<td style="width: 20%">
  {{include file="../../dPpatients/templates/inc_vw_photo_identite.tpl" patient=$_prescription->_ref_patient size=14 nodebug=true}}
     <a href="#1" onclick="showDossierSoins('{{$_prescription->_ref_object->_id}}','{{$date}}')" style="display: inline;">
     {{assign var=sejour value=$_prescription->_ref_object}}
     <span class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}} {{if $sejour->septique}}septique{{/if}}"
           onmouseover="ObjectTooltip.createEx(this, '{{$_prescription->_ref_object->_guid}}');">           
        {{$_prescription->_ref_patient->_view}}
     </span>
   </a>
</td>
<td style="width: 10%">
	{{$_prescription->_ref_object->_ref_curr_affectation->_ref_lit->_ref_chambre->_view}}
  {{$_prescription->_ref_object->_ref_curr_affectation->_ref_lit->_shortview}}
</td>
<td style="width: 10%">
  <div class="mediuser" style="border-color: #{{$_prescription->_ref_praticien->_ref_function->color}};">
  <label title="{{$_prescription->_ref_praticien->_view}}">
  {{$_prescription->_ref_praticien->_shortview}}
  </label>
  </div>
</td>
{{foreach from=$tabHours key=_date item=_hours_by_moment}}
  {{foreach from=$_hours_by_moment key=moment_journee item=_dates}}
    {{foreach from=$_dates key=_date_reelle item=_hours}}
      {{foreach from=$_hours key=_heure_reelle item=_hour}}
        {{assign var=_date_hour value="$_date_reelle $_heure_reelle"}}                        
        <td style="text-align: center; {{if $smarty.now|date_format:"%Y-%m-%d %H:00:00" == $_date_hour}}background-color: #ccc;{{/if}}" class="{{$_date}}-{{$moment_journee}}">
          {{if @$pancarte.$_prescription_id.$_date_hour}}
            <span style="height: 16px;" onmouseover='ObjectTooltip.createDOM(this, "tooltip-content-prises-{{$_prescription_id}}-{{$_date_reelle}}-{{$_heure_reelle}}");'>
              {{if @$new.$_prescription_id.$_date_hour}}
                <img src="images/icons/ampoule.png" />
              {{/if}}
              {{if @$urgences.$_prescription_id.$_date_hour}}
                <img src="images/icons/ampoule_urgence.png" />
              {{/if}}
            
              {{foreach from=$pancarte.$_prescription_id.$_date_hour key="chapitre" item=quantites}}
                <img src="{{$images.$chapitre}}" 
						 	  {{if $_date_hour <= $smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}}
						 		  style="
									{{if @$alertes.$_prescription_id.$_date_hour.$chapitre == '1'}}
	                  border-bottom: 3px solid 
										{{if !@$nb_adm.$_prescription_id.$_date_hour.$chapitre}}
										  {{if @$cond.$_prescription_id.$_date_hour.$chapitre}}
                        #aaa
                      {{else}}
											  red
											{{/if}}
										{{else}}
										  #FB4
										{{/if}}; height: 13px; margin: -1px;
	                {{else}}
	                  border-bottom: 3px solid #0F0; height: 13px; margin: -1px;
	                {{/if}}"
							  {{/if}} />
              {{/foreach}}
            </span>
            
            <div id="tooltip-content-prises-{{$_prescription_id}}-{{$_date_reelle}}-{{$_heure_reelle}}" style="display:none;">
              <table class="tbl">
                <tr>
                  <th colspan="6" class="title">
                    {{$_prescription->_ref_patient->_view}} - {{$_date_hour|date_format:$conf.datetime}}
                  </th>
                </tr>
                <tr>
                  <th colspan="2">Libelle</th>
                  <th>Prévue</th>
                  <th>Administrée</th>
                  <th>Unité</th>
									<th></th>
                </tr>
                {{foreach from=$pancarte.$_prescription_id.$_date_hour key="chapitre" item=quantites}}
                  <tr>
                    <th colspan="6">
                      <img src="{{$images.$chapitre}}" /><strong> {{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}}</strong>
                    </th>
                  </tr>
                  {{if $chapitre == "perfusion" || $chapitre == "aerosol"}}
                    {{foreach from=$quantites key=prescription_line_mix_id item=quantites_by_perf}}
                      <tr>
                        <th colspan="6">
                          {{assign var=prescription_line_mix value=$list_lines.$chapitre.$prescription_line_mix_id}}
                          {{$prescription_line_mix->_view}}
													
													{{if $prescription_line_mix->conditionnel}}
													<br />
                            {{unique_id var=conditionnel}}
                             <a href="#" onmouseover='ObjectTooltip.createDOM(this, "tooltip-condition-{{$conditionnel}}");' style="display: inline;">Conditionnel</a>
                             <div id="tooltip-condition-{{$conditionnel}}" style="display:none;">
                               <table class="tbl">
                                 <tr>
                                   <th class="title">Condition</th>
                                 </tr>
                                 <tr>
                                   <td class="text">{{mb_value object=$prescription_line_mix field="commentaire"}}</td>
                                 </tr>
                               </table>
                             </div>
                          {{/if}}
													
                        </th>
                      </tr>
                    {{foreach from=$quantites_by_perf key=perf_line_id item=_quantites}}
                          {{assign var=quantite_prevue value=0}}
                          {{assign var=quantite_adm value=0}}
                          {{if array_key_exists('prevue', $_quantites)}}
                            {{assign var=quantite_prevue value=$_quantites.prevue}}
                          {{/if}}
                          {{if array_key_exists('adm', $_quantites)}}
                            {{assign var=quantite_adm value=$_quantites.adm}}
                          {{/if}}  
                          <tr> 
                            <td colspan="2">
                              {{if array_key_exists('new', $_quantites)}}<img src="images/icons/ampoule.png" alt="" title="" />{{/if}}
		                    
															{{assign var=perf_line value=$list_lines.perf_line.$perf_line_id}}
                              {{$perf_line->_ucd_view}} ({{$perf_line->_posologie}})   
                              {{if $quantite_prevue == $quantite_adm}}<img src="images/icons/tick.png" alt="" title="" />{{/if}}
                        		</td>
                            <td>{{$quantite_prevue}}</td>
                            <td>{{$quantite_adm}}</td>
                            <td>{{$perf_line->_unite_administration}}</td>
														<td>
                              {{include file="../../dPprescription/templates/inc_vw_info_line_medicament.tpl" line=$perf_line}}
                            </td> 
                          </tr>
                      {{/foreach}}
                    {{/foreach}}
                  {{else}}
                    {{foreach from=$quantites key=line_id item=_quantite}}
                    {{assign var=quantite_prevue value=0}}
                    {{assign var=quantite_adm value=0}}
                    <tr>
                      {{if array_key_exists('prevue', $_quantite)}}
                        {{assign var=quantite_prevue value=$_quantite.prevue}}
                      {{/if}}
                      {{if array_key_exists('adm', $_quantite)}}
                        {{assign var=quantite_adm value=$_quantite.adm}}
                      {{/if}}   
                      {{if $quantite_prevue || $quantite_adm}}
                        <td>
                          {{if $quantite_prevue == $quantite_adm}}<img src="images/icons/tick.png" alt="" title="" />{{/if}}
                          {{if array_key_exists('new', $_quantite)}}<img src="images/icons/ampoule.png" alt="" title="" />{{/if}}
                          {{if array_key_exists('urgence', $_quantite)}}<img src="images/icons/ampoule_urgence.png" alt="" title="" />{{/if}}
                        </td>
                        <td>
                           {{assign var=line value=$list_lines.$chapitre.$line_id}}
                           {{if $line instanceof CPrescriptionLineMedicament}}
                             <span onmouseover="ObjectTooltip.createEx(this, '{{$line->_guid}}');">{{$line->_ucd_view}}</span><br />
                             <span class="opacity-50" style="font-size:0.8em;">
                               {{$line->_forme_galenique}}
                             </span>
                           {{/if}}
                           {{if $line instanceof CPrescriptionLineElement}}
                             <span onmouseover="ObjectTooltip.createEx(this, '{{$line->_guid}}');">{{$line->_view}}</span>
                          {{/if}}
													
													{{if $line->conditionnel}}
													  {{unique_id var=conditionnel}}
													  - <a href="#" onmouseover='ObjectTooltip.createDOM(this, "tooltip-condition-{{$conditionnel}}");' style="display: inline;">Conditionnel</a>
														 <div id="tooltip-condition-{{$conditionnel}}" style="display:none;">
														   <table class="tbl">
														   	 <tr>
														   	 	 <th class="title">Condition</th>
														   	 </tr>
																 <tr>
																 	 <td class="text">{{mb_value object=$line field="commentaire"}}</td>
																 </tr>
														   </table>
														 </div>
													{{/if}}
                        </td>
                        <td style="text-align: center;">{{$quantite_prevue}}</td>
                        <td style="text-align: center;">{{$quantite_adm}}</td>
                        <td>
                          {{if $line instanceof CPrescriptionLineMedicament}}
													  {{if $line->_unite_administration == "aucune_prise"}}
														  {{$line->_ref_produit->libelle_unite_presentation}}
														{{else}}
														  {{$line->_unite_administration}}
														{{/if}}
                          {{/if}}
                          {{if $line instanceof CPrescriptionLineElement}}
                            {{$line->_unite_prise}}
                          {{/if}}
                        </td>
												<td>
													{{if $line instanceof CPrescriptionLineMedicament}}
												    {{include file="../../dPprescription/templates/inc_vw_info_line_medicament.tpl"}}
												  {{/if}}
												</td>	
                      {{/if}}
                      </tr>
                    {{/foreach}}
                   {{/if}}
                {{/foreach}}
              </table>
            </div>  
          {{/if}}
        </td>
      {{/foreach}}
    {{/foreach}}
  {{/foreach}}
{{/foreach}}