{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<script type="text/javascript">
cancelTarif = function(action) {
  var oForm = document.tarifFrm;
  
  if(action == "delActes"){
    $V(oForm._delete_actes, 1);
    $V(oForm.tarif, "");
  }
  
  {{if $app->user_prefs.autoCloseConsult}}
  $V(oForm.chrono, "48");
  {{/if}}
  
  $V(oForm.valide, 0);
  $V(oForm._somme, 0);
  
  // On met à 0 les valeurs de tiers 
  $V(oForm.tiers_date_reglement, "");
  $V(oForm.patient_date_reglement, "");
  
  Reglement.submit(oForm);
}

validTarif = function(){
  var oForm = document.tarifFrm;
  
  $V(oForm.du_tiers,  $V(oForm._somme) - $V(oForm.du_patient));
  
  if ($V(oForm.tarif) == ""){
    $V(oForm.tarif, "manuel");
  }
  Reglement.submit(oForm);
}

modifTotal = function(){
  var oForm = document.tarifFrm;
  var secteur1 = oForm.secteur1.value;
  var secteur2 = oForm.secteur2.value;

  $V(oForm._somme, Math.round(100*(parseFloat(secteur1) + parseFloat(secteur2))) / 100);
  $V(oForm.du_patient, $V(oForm._somme)); 
}

modifSecteur2 = function(){
  var oForm = document.tarifFrm;
  var secteur1 = oForm.secteur1.value;
  var somme = oForm._somme.value;
  
  $V(oForm.du_patient, somme);
  $V(oForm.secteur2, Math.round(100*(parseFloat(somme) - parseFloat(secteur1))) / 100);
}

printActes = function(){
  var url = new Url('dPcabinet', 'print_actes');
	url.addParam('consultation_id', '{{$consult->_id}}');
	url.popup(600, 600, 'Impression des actes');
}

checkActe = function(button) {
	{{if array_key_exists('sigems', $modules)}}
	  button.disabled = "disabled";
	  $(button).setOpacity(0.5);
	  var url = new Url('sigems', 'ajax_check_actes');
	  url.addParam("sejour_id", button.form.sejour_id.value);
	  $('systemMsg').show().update('<div class="loading">Recherche des actes. Veuillez patienter.</div>');
	  url.requestJSON(checkSigemsActes);
	{{else}}
	  button.form.du_tiers.value = 0; 
	  button.form.du_patient.value = 0; 
	  cancelTarif();
	{{/if}}
}

checkSigemsActes = function(actes) {
	if (!actes) {
    getForm('tarifFrm').du_tiers.value = 0; 
    getForm('tarifFrm').du_patient.value = 0; 
    cancelTarif();
  } else {
	  $('systemMsg').show().update('<div class="error">Des actes ont été validés par la facturation, vous ne pouvez pas modifier votre cotation.</div>'); 
  }
	$('buttonCheckActe').setOpacity(1).disabled = false;
}

Main.add( function(){
  prepareForm(document.accidentTravail);
	
	{{if $consult->_ref_patient->ald}}
	if($('accidentTravail_concerne_ALD_1')){
	  $('accidentTravail_concerne_ALD_1').checked = "checked";
		submitFormAjax(document.accidentTravail, 'systemMsg');
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
          
            <form name="accidentTravail" action="" method="post">
              <input type="hidden" name="m" value="dPcabinet" />
              <input type="hidden" name="dosql" value="do_consultation_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
              <table class="form">
                <tr>
                  <th>
                   {{mb_label object=$consult field="accident_travail"}}
                 </th>
                 <td class="date">
                   {{mb_field object=$consult field="accident_travail" form="accidentTravail" onchange="submitFormAjax(this.form,'systemMsg');" register=true}}
                 </td>
                 <td>
                   {{if $patient->cmu}}
                   <strong>Bénéficiaire d'une CMU</strong>
                   {{/if}}
                 </td>
               </tr>
							 {{if $consult->_ref_patient->ald && !$consult->tarif}}
                <tr>
                  <th>{{mb_label object=$consult field=concerne_ALD}}</th>
                  <td colspan="2">{{mb_field object=$consult field=concerne_ALD onchange="submitFormAjax(this.form,'systemMsg');"}}</td>
                </tr>
                {{/if}}
								
              </table>  
            </form>
            
            <!-- Formulaire de selection de tarif -->
            <form name="selectionTarif" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      	      <input type="hidden" name="m" value="dPcabinet" />
      	      <input type="hidden" name="del" value="0" />
      	      <input type="hidden" name="dosql" value="do_consultation_aed" />
              <input type="hidden" name="_delete_actes" value="1" />
      	      <input type="hidden" name="_bind_tarif" value="1" />
      	      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
      	     
      	      <table class="form">
      	        {{if !$consult->tarif}}
      	        <tr>
      	          <th><label for="choix" title="Type de cotation pour la consultation. Obligatoire.">Cotation</label></th>
      	          <td>
      	            <select name="_tarif_id"  class="notNull str" style="width: 130px;" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete : Reglement.reload } );">
      	              <option value="" selected="selected">&mdash; Choisir la cotation</option>
      	              {{if $tarifsChir|@count}}
        	              <optgroup label="Tarifs praticien">
        	              {{foreach from=$tarifsChir item=_tarif}}
        	                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
        	              {{/foreach}}
        	              </optgroup>
      	              {{/if}}
      	              {{if $tarifsCab|@count}}
        	              <optgroup label="Tarifs cabinet">
        	              {{foreach from=$tarifsCab item=_tarif}}
        	                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
        	              {{/foreach}}
        	              </optgroup>
      	              {{/if}}
      	            </select>
      	          </td>
      	        </tr>
      	        {{else}}
								 {{if $consult->_ref_patient->ald}}
                  <tr>
                    <th>{{mb_label object=$consult field=concerne_ALD}}</th>
                    <td>{{mb_value object=$consult field=concerne_ALD}}</td>
                  </tr>
								  {{/if}}
        	        <tr>
        	          <th>{{mb_label object=$consult field=tarif}}</th>
        	          <td>{{mb_value object=$consult field=tarif}}</td>    
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
                var oForm = document.forms['tarifFrm'];
                if(oForm && oForm.du_patient && oForm.du_patient.value == "0"){
                  $V(oForm.du_patient, $V(oForm._somme)); 
                }
              } );
            </script>
            <form name="tarifFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
           {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
           {{mb_field object=$consult field="sejour_id" hidden=1 prop=""}}
      
            <table width="100%">
              <!-- A regler -->
              <tr>
                <th>{{mb_label object=$consult field="_somme"}}</th>
                <td>
                  {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
                  <input type="hidden" name="patient_date_reglement" value="" />
                  {{if $consult->valide}}
        	          {{mb_value object=$consult field="secteur1"}} (S1) +
        	          {{mb_value object=$consult field="secteur2"}} (S2) =
         			      {{mb_value object=$consult field="_somme" value=$consult->secteur1+$consult->secteur2 onchange="modifSecteur2()"}}
                  {{else}}
                    {{mb_label object=$consult field="secteur1"}}
        	          {{mb_field object=$consult field="secteur1" onchange="modifTotal()"}} +
        	          {{mb_label object=$consult field="secteur2"}}
        	          {{mb_field object=$consult field="secteur2" onchange="modifTotal()"}} =
       			        <input type="text" size="6" name="_somme" class="notNull currency" value="{{$consult->secteur1+$consult->secteur2}}" onchange="modifSecteur2()" /> {{$dPconfig.currency_symbol}}
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
                <form name="reglement-add" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : Reglement.reload } );">
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
                      <td><label title="{{mb_value object=$curr_reglement field=date}}">{{$curr_reglement->date|date_format:"%d/%m/%Y"}}</td>
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