{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<script type="text/javascript">
	
pursueTarif = function() {
  var form = document.tarifFrm;
  $V(form.tarif, "pursue");
  $V(form.valide, 0);
  Reglement.submit(form, false);
}	
	
cancelTarif = function(action) {
  var form = document.tarifFrm;
  
  if(action == "delActes") {
    $V(form._delete_actes, 1);
    $V(form.tarif, "");
  }
  
  {{if $app->user_prefs.autoCloseConsult}}
  $V(form.chrono, "48");
  {{/if}}
  
  $V(form.valide, 0);
  $V(form._somme, 0);
  
  // On met à 0 les valeurs de tiers 
  $V(form.tiers_date_reglement, "");
  $V(form.patient_date_reglement, "");
  
  Reglement.submit(form, true);
}

validTarif = function(){
  var form = document.tarifFrm;
  
  $V(form.du_tiers,  $V(form._somme) - $V(form.du_patient));
  
  if ($V(form.tarif) == ""){
    $V(form.tarif, "manuel");
  }
  Reglement.submit(form, true);
}

modifTotal = function(){
  var form = document.tarifFrm;
  var secteur1 = form.secteur1.value;
  var secteur2 = form.secteur2.value;

  $V(form._somme, Math.round(100*(parseFloat(secteur1) + parseFloat(secteur2))) / 100);
  $V(form.du_patient, $V(form._somme)); 
}

modifSecteur2 = function(){
  var form = document.tarifFrm;
  var secteur1 = form.secteur1.value;
  var somme = form._somme.value;
  
  $V(form.du_patient, somme);
  $V(form.secteur2, Math.round(100*(parseFloat(somme) - parseFloat(secteur1))) / 100);
}

printActes = function(){
  var url = new Url('dPcabinet', 'print_actes');
	url.addParam('consultation_id', '{{$consult->_id}}');
	url.popup(600, 600, 'Impression des actes');
}

checkActe = function(button) {
  button.form.du_tiers.value = 0; 
  button.form.du_patient.value = 0; 
  cancelTarif();
}

Main.add( function(){
  prepareForm(document.accidentTravail);
	
	{{if $consult->_ref_patient->ald}}
	if($('accidentTravail_concerne_ALD_1')){
	  $('accidentTravail_concerne_ALD_1').checked = "checked";
		onSubmitFormAjax(document.accidentTravail);
	}
	{{/if}}	
});
</script>

{{mb_ternary var=gestionFSE test=$consult->sejour_id value=0 other=$app->user_prefs.GestionFSE}}

<table class="main">
  <tr>
  {{if $gestionFSE}}
    <td style="width: 50%;">
      <!-- Inclusion de la gestion de la FSE -->
      {{include file="inc_vw_gestion_fse.tpl"}}
    </td>
  {{/if}}
  
    <td>
      <table class="form">
      	<tr>
          <th class="category">
            {{if $consult->valide}}
            <!-- Creation d'un nouveau tarif avec les actes NGAP de la consultation courante -->
            <form name="creerTarif" action="?m=dPcabinet&amp;tab=vw_compta" method="post" style="float: right;">
              <input type="hidden" name="dosql" value="do_tarif_aed" />
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="_tab" value="vw_edit_tarifs" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="_bind_consult" value="1" />
              <input type="hidden" name="_consult_id" value="{{$consult->_id}}" />
              <button class="submit" type="submit">Nouveau tarif</button>
            </form>
            {{/if}}
            Règlement
          </th>
        </tr>
        
      	<tr>
          <!-- Règlements -->  
          <td>
          	<div style="text-align: center; font-weight: bold;">
              {{if $patient->cmu}}
                Couverture Maladie Universelle<br/>
              {{/if}}
            
              {{if $patient->ald}}
                Affection Longue Durée<br/>
              {{/if}}
            </div>
          
            <!-- Formulaire de selection de tarif -->
            <form name="selectionTarif" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : Reglement.reload.curry(true) } );">
      	      <input type="hidden" name="m" value="dPcabinet" />
      	      <input type="hidden" name="del" value="0" />
      	      <input type="hidden" name="dosql" value="do_consultation_aed" />
              {{mb_key object=$consult}}
              <input type="hidden" name="_bind_tarif" value="1" />

              {{if $consult->tarif == "pursue"}}
              {{mb_field object=$consult field=tarif hidden=1}}
              <input type="hidden" name="_delete_actes" value="0" />
              {{else}}
              <input type="hidden" name="_delete_actes" value="1" />
							{{/if}}
      	     
      	      <table class="form">
      	        {{if !$consult->tarif || $consult->tarif == "pursue"}}
	       	        <tr>
	       	          <th>{{mb_label object=$consult field=accident_travail}}</th>
	       	          <td>{{mb_field object=$consult field=accident_travail form=selectionTarif register=true}}</td>    
	       	        </tr>
	
	                {{if $consult->_ref_patient->ald}}
	                <tr>
	                  <th>{{mb_label object=$consult field=concerne_ALD}}</th>
	                  <td>{{mb_field object=$consult field=concerne_ALD}}</td>
	                </tr>
	                {{/if}}
	      	        
	      	        <tr>
	      	          <th><label for="choix" title="Type de cotation pour la consultation. Obligatoire.">Cotation</label></th>
	      	          <td>
	      	            <select name="_tarif_id"  class="notNull str" style="width: 130px;" onchange="this.form.onsubmit();">
	      	              <option value="" selected="selected">&mdash; Choisir la cotation</option>
	      	              {{if $tarifs.user|@count}}
	        	              <optgroup label="Tarifs praticien">
	        	              {{foreach from=$tarifs.user item=_tarif}}
	        	                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
	        	              {{/foreach}}
	        	              </optgroup>
	      	              {{/if}}
	      	              {{if $tarifs.func|@count}}
	        	              <optgroup label="Tarifs cabinet">
	        	              {{foreach from=$tarifs.func item=_tarif}}
	        	                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
	        	              {{/foreach}}
	        	              </optgroup>
	      	              {{/if}}
	      	            </select>
	      	          </td>
	      	        </tr>
      	        {{else}}
      	          
                  {{if $consult->accident_travail}}
        	        <tr>
        	          <th>{{mb_label object=$consult field=accident_travail}}</th>
        	          <td>{{mb_value object=$consult field=accident_travail}}</td>    
        	        </tr>
        	        {{/if}}

                  {{if $consult->_ref_patient->ald ||  $consult->concerne_ALD}}
                  <tr>
                    <th>{{mb_label object=$consult field=concerne_ALD}}</th>
                    <td>{{mb_value object=$consult field=concerne_ALD}}</td>
                  </tr>
								  {{/if}}
        	        
        	        <tr>
        	          <th>{{mb_label object=$consult field=tarif}}</th>
        	          <td>{{mb_value object=$consult field=tarif}}</td>
										{{if !$consult->valide}}
                    <td class="button">
                    	<button type="button" class="add" onclick="pursueTarif();">
                    		{{tr}}Add{{/tr}}
											</button>
										</td>
										{{/if}}
        	        </tr>
      	        {{/if}}
      	      </table>
            </form>
            <!-- Fin formulaire de selection du tarif -->
            
            <hr />
            
            <!-- Formulaire de tarification -->
            <script type="text/javascript">
              Main.add( function(){
                // Mise a jour de du_patient
                var form = document.forms['tarifFrm'];
                if(form && form.du_patient && form.du_patient.value == "0"){
                  $V(form.du_patient, $V(form._somme)); 
                }
              } );
            </script>
            <form name="tarifFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_key object=$consult}}
            {{mb_field object=$consult field="sejour_id" hidden=1}}
      
            <table width="100%">
              <!-- A régler -->
              <tr>
                <th>{{mb_label object=$consult field="_somme"}}</th>
                <td>
                  {{mb_field object=$consult field="tarif" hidden=1}}
                  <input type="hidden" name="patient_date_reglement" value="" />
                  {{if $consult->valide}}
                    {{mb_value object=$consult field="_somme" value=$consult->secteur1+$consult->secteur2 onchange="modifSecteur2()"}}
										<br />
        	          {{mb_value object=$consult field="secteur1"}} (S1) +
        	          {{mb_value object=$consult field="secteur2"}} (S2)
                  {{else}}
                    <input type="text" size="6" name="_somme" class="notNull currency" value="{{$consult->secteur1+$consult->secteur2}}" onchange="modifSecteur2()" /> 
                    {{$dPconfig.currency_symbol}}
										<br />
                    {{mb_label object=$consult field="secteur1"}}
        	          {{mb_field object=$consult field="secteur1" onchange="modifTotal()"}} +
        	          {{mb_label object=$consult field="secteur2"}}
        	          {{mb_field object=$consult field="secteur2" onchange="modifTotal()"}}
                  {{/if}}
                  {{if $consult->patient_date_reglement}}
                    {{mb_field object=$consult field="du_patient" hidden=1 prop=""}}
                    {{mb_field object=$consult field="du_tiers" hidden=1 prop=""}}
                    {{mb_field object=$consult field="patient_date_reglement" hidden=1 prop=""}}
                  {{/if}}
                </td>
              </tr>
              <tr>
                <th>Codes CCAM</th>
                <td>{{mb_field object=$consult field="_tokens_ccam" readonly="readonly" hidden=1 prop=""}}
                  {{foreach from=$consult->_ref_actes_ccam item="acte_ccam"}}
                  	<span onmouseover="ObjectTooltip.createEx(this, '{{$acte_ccam->_guid}}');">{{$acte_ccam->_shortview}}</span>
                  {{/foreach}}
                </td>
              </tr>
              <tr>
                <th>Codes NGAP</th>
                <td>{{mb_field object=$consult field="_tokens_ngap" readonly="readonly" hidden=1 prop=""}}
                  {{foreach from=$consult->_ref_actes_ngap item=acte_ngap}}
                  	<span onmouseover="ObjectTooltip.createEx(this, '{{$acte_ngap->_guid}}');">{{$acte_ngap->_shortview}}</span>
                  {{/foreach}}
                </td>
              </tr>
      
              {{if $consult->tarif && $consult->patient_date_reglement == "" && $consult->valide == "1"}}
              <tr>
                <td colspan="2" class="button">
                  <input type="hidden" name="valide" value="1" />
                  <input type="hidden" name="secteur1" value="{{$consult->secteur1}}" />
                  <input type="hidden" name="secteur2" value="{{$consult->secteur2}}" />
                  <input type="hidden" name="du_patient" value="{{$consult->du_patient}}" />
                  <input type="hidden" name="du_tiers" value="{{$consult->du_tiers}}" />
                  
                  {{if $app->user_prefs.autoCloseConsult}}
                  <input type="hidden" name="chrono" value="{{$consult->chrono}}" />
                  {{/if}}
                  
                  {{if !$consult->_current_fse && $consult->_ref_reglements|@count == 0}}
                  <button class="cancel" type="button" id="buttonCheckActe" onclick="checkActe(this)">Annuler la validation</button>
                  {{/if}}
                  <button class="print" type="button" onclick="printActes()">Imprimer les actes</button>
                </td>
              </tr>
              {{elseif !$consult->patient_date_reglement}}
                {{if !$consult->sejour_id}}
                <tr>
                  <th>{{mb_label object=$consult field="du_patient"}}</th>
                  <td>
                    {{mb_field object=$consult field="du_patient"}}
                    {{mb_field object=$consult field="du_tiers" hidden="1"}}
                    <button type="button" class="tick" onclick="$V(this.form.du_patient, 0);">Tiers-payant total</button>   
                  </td>
                </tr>
                {{/if}}
                <tr>
                  <td colspan="2" class="button">
                    <input type="hidden" name="_delete_actes" value="0" />
                    <input type="hidden" name="valide" value="1" />
                    
                    {{if $app->user_prefs.autoCloseConsult}}
                    <input type="hidden" name="chrono" value="64" />
                    {{/if}}
                    
                    <button class="submit" type="button" onclick="validTarif();">Valider la cotation</button>
                    <button class="cancel" type="button" onclick="cancelTarif('delActes')">Annuler la cotation</button>
                  </td>
                </tr>
              {{/if}}
            </table>
            </form>
            <!-- Fin du formulaire de tarification -->
            
            
            <!-- Debut du formulaire de rajout de reglements -->
            {{if $consult->tarif && $consult->valide == "1"}}
              {{if $consult->sejour_id}}
               <div style="text-align: center; font-weight: bold;">
                 ATU : Règlement à effectuer au bureau des sorties
               </div>
              {{else}}
              
               {{if $consult->du_patient}}
                <!-- Formulaire de suppression d'un reglement (car pas possible de les imbriquer) -->
                <form name="reglement-delete" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
                  <input type="hidden" name="m" value="dPcabinet" />
                  <input type="hidden" name="del" value="1" />
                  <input type="hidden" name="dosql" value="do_reglement_aed" />
                  <input type="hidden" name="reglement_id" value="" />
                </form>
               
                <script type="text/javascript">Main.add( function() { prepareForm(document.forms["reglement-add"]); } );</script>
								
                <form name="reglement-add" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : Reglement.reload.curry(false) } );">
                 <input type="hidden" name="m" value="dPcabinet" />
                 <input type="hidden" name="del" value="0" />
                 <input type="hidden" name="dosql" value="do_reglement_aed" />
                 <input type="hidden" name="date" value="now" />
                 <input type="hidden" name="emetteur" value="patient" />
                {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
                
                 <table style="width: 100%;">
                    <tr>
                      <th class="category">{{mb_label object=$reglement field=mode}}</th>
                      <th class="category">{{mb_label object=$reglement field=montant}}</th>
                      <th class="category">{{mb_label object=$reglement field=banque_id}}</th>
                      <th class="category">{{mb_label object=$reglement field=date}}</th>
                      <th class="category"></th>
                    </tr>
                    
                    <!--  Liste des reglements deja effectués -->
                    {{foreach from=$consult->_ref_reglements item=curr_reglement}}
                    <tr>
                      <td>{{mb_value object=$curr_reglement field=mode}}</td>
                      <td>{{mb_value object=$curr_reglement field=montant}}</td>
                      <td>
                      {{if $curr_reglement->_ref_banque}}
                        {{mb_value object=$curr_reglement->_ref_banque field=_view}}
                      {{/if}}
                      </td>
                      <td>
                      	<label title="{{mb_value object=$curr_reglement field=date}}">
												{{$curr_reglement->date|date_format:$dPconfig.date}}
											</td>
                      <td>
                        <a class="button remove notext" href="" onclick="return Reglement.cancel({{$curr_reglement->_id}});"></a>
                      </td>
                    </tr>
                    {{/foreach}}
                   
                    {{if $reglement->montant > 0}}
                    <tr>
                      <td>{{mb_field object=$reglement field="mode" defaultOption="&mdash; Mode"}}</td>
                      <td>{{mb_field object=$reglement field="montant"}}</td>
                      <td colspan="2">
                        <select name="banque_id">
                           <option value="">&mdash; {{tr}}CReglement-banque_id{{/tr}} &mdash;</option> 
                           {{foreach from=$banques item=banque}}
                             <option value="{{$banque->_id}}">{{$banque->_view}}</option>
                           {{/foreach}}
                        </select>
                      </td>
                      <td><button class="add notext" type="submit" onclick="return this.form.onsubmit();">+</button></td>
                    </tr>
                    {{/if}}
                    <tr>
                      <td colspan="4" style="text-align: center;">
                      	{{mb_value object=$consult field=_reglements_total_patient}} réglés, 
                      	<strong>{{mb_value object=$consult field=_du_patient_restant}} restant</strong>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" style="text-align: center;">
                      	<strong>
      	                  {{if $consult->patient_date_reglement}}
      	                  {{mb_label object=$consult field=patient_date_reglement}}
      	                  le 
      	                  {{mb_value object=$consult field=patient_date_reglement}}
      	                  {{/if}}
                        </strong>
                      </td>
                    </tr>
                  </table>
                </form>
              {{/if}}
            {{/if}}
          {{/if}}
          <!-- Fin du formulaire de rajout de reglement -->
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>