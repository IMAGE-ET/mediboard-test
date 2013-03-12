{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

{{mb_script module="dPcabinet" script="facture" ajax="true"}}

{{if $consult->_ref_plageconsult->pour_compte_id}}
  {{assign var=pour_compte_praticien_id value=$consult->_ref_plageconsult->pour_compte_id}}
{{else}}
  {{assign var=pour_compte_praticien_id value=$praticien->_id}}
{{/if}}

<script type="text/javascript">
pursueTarif = function() {
  var form = document.tarifFrm;
  $V(form.tarif, "pursue");
  $V(form.valide, 0);
  Reglement.submit(form, false);
}	
  
cancelTarif = function(action, callback) {
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
  
  Reglement.submit(form, true, callback);
}

validTarif = function(){
  var form = document.tarifFrm;
  
  $V(form.du_tiers,  Math.round(($V(form._somme) - $V(form.du_patient))*100)/100);
  
  if ($V(form.tarif) == ""){
    $V(form.tarif, "manuel");
  }
  Reglement.submit(form, true, loadFacture);
}

loadFacture = function() {
  Facture.load(document.tarifFrm, '{{$consult->patient_id}}', '{{$pour_compte_praticien_id}}', '{{$consult->_id}}', 1);
}
reloadFacture = function() {
  {{if $consult->facture_id}}
    Facture.reload('{{$consult->patient_id}}', '{{$consult->_id}}', 1, '{{$consult->facture_id}}');
  {{/if}}
}

modifTotal = function(){
  var form = document.tarifFrm;
  if (!form.secteur1.value) {
    form.secteur1.value = 0;
  }
  var secteur1 = form.secteur1.value;
  if (!form.secteur2.value) {
    form.secteur2.value = 0;
  }
  var secteur2 = form.secteur2.value;
  $V(form._somme, Math.round(100*(parseFloat(secteur1) + parseFloat(secteur2))) / 100);
  $V(form.du_patient, Math.round(100* parseFloat(form._somme.value)) / 100); 
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
  button.form.facture_id.value = ""; 
  cancelTarif(null, reloadFacture);
}

Main.add( function(){
  prepareForm(document.accidentTravail);
  
  {{if $consult->type_assurance}}
    var url = new Url("cabinet", "ajax_type_assurance");
    url.addParam("consult_id", '{{$consult->_id}}');
    url.requestUpdate("area_type_assurance");
  {{/if}}
  
  {{if $consult->_ref_patient->ald}}
    if($('accidentTravail_concerne_ALD_1')){
      $('accidentTravail_concerne_ALD_1').checked = "checked";
      onSubmitFormAjax(document.accidentTravail);
    }
  {{/if}}	
});
</script>

{{assign var=modFSE value="fse"|module_active}}

{{mb_ternary var=gestionFSE test=$consult->sejour_id value=0 other=$modFSE}}

<table class="form">
  <tr>
    <td colspan="2">
      {{mb_include module="cabinet" template="inc_type_assurance_reglement/inc_type_assurance"}}
    </td>
  </tr>

  <tr>
    <!--  type assurance -->
    <td colspan="2" id="area_type_assurance">
    </td>
  </tr>

  <tr>
    {{if $gestionFSE}}
      {{if $modFSE->canRead()}}
        <td class="halfPane">
          <!-- Inclusion de la gestion de la FSE -->
          {{mb_include module=fse template=inc_gestion_fse}}
        </td>
      {{/if}}
    {{/if}}
  
    <td>
      <fieldset>
        <legend>Cotation</legend>
        
        <div style="text-align: center; font-weight: bold;">
          {{if $patient->cmu}}
            Couverture Maladie Universelle<br/>
          {{/if}}
        
          {{if $patient->ald}}
            Affection Longue Durée<br/>
          {{else}}
            {{mb_form name="frm-Patient-ALD" m="patients" dosql="do_patients_aed" method="post" onsubmit="return onSubmitFormAjax(this);"}}
              {{mb_class object=$patient}}
              {{mb_key object=$patient}}
              <input type="hidden" name="callback" value="Reglement.reload"/>

              {{mb_title object=$patient field=ald}}
              {{mb_field object=$patient field=ald onchange="this.form.onsubmit();"}}
            {{/mb_form}}
          {{/if}}
        </div>
        
          {{if $consult->valide}}
          <!-- Creation d'un nouveau tarif avec les actes NGAP de la consultation courante -->
          <form name="creerTarif" action="?m=dPcabinet&amp;tab=vw_compta" method="post">
            <input type="hidden" name="dosql" value="do_tarif_aed" />
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="_tab" value="vw_edit_tarifs" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="_bind_consult" value="1" />
            <input type="hidden" name="_consult_id" value="{{$consult->_id}}" />
          </form>
          {{/if}}
        
          <!-- Formulaire de selection de tarif -->
          <form name="selectionTarif" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, Reglement.reload.curry(true));">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_key object=$consult}}
            <input type="hidden" name="_bind_tarif" value="1" />
            <input type="hidden" name="_delete_actes" value="0" />

            {{if $consult->tarif == "pursue"}}
            {{mb_field object=$consult field=tarif hidden=1}}
            {{/if}}
           
            <table class="form">
              {{if !$consult->tarif || $consult->tarif == "pursue"}}
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
                      <option value="" selected="selected">&mdash; {{tr}}Choose{{/tr}}</option>
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
                {{if $consult->_ref_patient->ald ||  $consult->concerne_ALD}}
                <tr>
                  <th>{{mb_label object=$consult field=concerne_ALD}}</th>
                  <td>{{mb_value object=$consult field=concerne_ALD}}</td>
                </tr>
                {{/if}}
                
                <tr>
                  <th>{{mb_label object=$consult field=tarif}}</th>
                  <td>
                    {{if $consult->valide}}
                      <!-- Creation d'un nouveau tarif avec les actes NGAP de la consultation courante -->
                      <button class="submit" type="button" style="float: right;" onclick="getForm('creerTarif').submit()">Nouveau tarif</button>
                    {{/if}}
                    {{mb_value object=$consult field=tarif}}
                  </td>
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
                $V(form.du_patient, Math.round($V(form._somme)*100)/100); 
              }
            } );
          </script>
          
          <form name="tarifFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_consultation_aed" />
          <input type="hidden" name="type_facture" value="{{if $consult->pec_at == 'arret'}}accident{{else}}maladie{{/if}}"/>
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
                   {{if !@$modules.tarmed->_can->read || $conf.tarmed.CCodeTarmed.use_cotation_tarmed != "1"}}
                    <br />
                    {{mb_value object=$consult field="secteur1"}} (S1) +
                    {{mb_value object=$consult field="secteur2"}} (S2)
                   {{/if}}
                {{else}}
                
                  {{math equation="x+y" x=$consult->secteur1 y=$consult->secteur2 assign=somme}}
                  {{mb_field size=6 object=$consult field="_somme" value="$somme" onchange="modifSecteur2()"}}
                  {{if !@$modules.tarmed->_can->read || $conf.tarmed.CCodeTarmed.use_cotation_tarmed != "1"}}
                    <br />
                    {{mb_label object=$consult field="secteur1"}}
                    {{mb_field object=$consult field="secteur1" onchange="modifTotal()"}} +
                    {{mb_label object=$consult field="secteur2"}}
                    {{mb_field object=$consult field="secteur2" onchange="modifTotal()"}}
                  {{else}}
                    {{mb_field object=$consult field="secteur1" onchange="modifTotal()" hidden=1}}
                    {{mb_field object=$consult field="secteur2" onchange="modifTotal()" hidden=1}}
                  {{/if}}
                {{/if}}
                {{if $consult->patient_date_reglement}}
                  {{mb_field object=$consult field="du_patient" hidden=1}}
                  {{mb_field object=$consult field="du_tiers" hidden=1}}
                  {{mb_field object=$consult field="patient_date_reglement" hidden=1}}
                {{/if}}
              </td>
            </tr>
            
            {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
            <tr>
              <th>Codes CCAM</th>
              <td>{{mb_field object=$consult field="_tokens_ccam" readonly="readonly" hidden=1}}
                {{foreach from=$consult->_ref_actes_ccam item="acte_ccam"}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$acte_ccam->_guid}}');">{{$acte_ccam->_shortview}}</span>
                {{/foreach}}
              </td>
            </tr>
            <tr>
              <th>Codes NGAP</th>
              <td>{{mb_field object=$consult field="_tokens_ngap" readonly="readonly" hidden=1}}
                {{foreach from=$consult->_ref_actes_ngap item=acte_ngap}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$acte_ngap->_guid}}');">{{$acte_ngap->_shortview}}</span>
                {{/foreach}}
              </td>
            </tr>
            {{/if}}
            
            {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
            <tr>
              <th>Codes Tarmed</th>
              <td>{{mb_field object=$consult field="_tokens_tarmed" readonly="readonly" hidden=1}}
                {{foreach from=$consult->_ref_actes_tarmed item=acte_tarmed}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$acte_tarmed->_guid}}');">{{$acte_tarmed->_shortview}}</span>
                {{/foreach}}
              </td>
            </tr>
            <tr>
              <th>Codes Prestation</th>
              <td>{{mb_field object=$consult field="_tokens_caisse" readonly="readonly" hidden=1}}
                {{foreach from=$consult->_ref_actes_caisse item=acte_caisse}}
                  <span onmouseover="ObjectTooltip.createEx(this, '{{$acte_caisse->_guid}}');">{{$acte_caisse->_shortview}}</span>
                {{/foreach}}
              </td>
            </tr>
            {{/if}}
            
            {{if $consult->tarif && $consult->patient_date_reglement == "" && $consult->valide == "1"}}
            <tr>
              <td colspan="2" class="button">
                <input type="hidden" name="valide" value="1" />
                <input type="hidden" name="secteur1" value="{{$consult->secteur1}}" />
                <input type="hidden" name="secteur2" value="{{$consult->secteur2}}" />
                <input type="hidden" name="du_patient" value="{{$consult->du_patient}}" />
                <input type="hidden" name="du_tiers" value="{{$consult->du_tiers}}" />
                <input type="hidden" name="facture_id" value="{{$consult->facture_id}}" />
                
                {{if $app->user_prefs.autoCloseConsult}}
                <input type="hidden" name="chrono" value="{{$consult->chrono}}" />
                {{/if}}
                
                {{if !$consult->_current_fse && !count($consult->_ref_reglements)}}
                <button class="cancel" type="button" id="buttonCheckActe" onclick="checkActe(this);">
                  Rouvrir la cotation
                </button>
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
                  {{if !@$modules.tarmed->_can->read || $conf.tarmed.CCodeTarmed.use_cotation_tarmed != "1"}}
                    <button type="button" class="tick" onclick="$V(this.form.du_patient, 0);">Tiers-payant total</button>
                  {{/if}}   
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
                  <button class="submit" type="button" onclick="validTarif();">Cloturer la cotation</button>
                  <button class="cancel" type="button" onclick="cancelTarif('delActes')">Vider la cotation</button>
                </td>
              </tr>
            {{/if}}
          </table>
          </form>
          <!-- Fin du formulaire de tarification -->
      </fieldset>
    </td>
  </tr>
  <tr>
    <td id="load_facture" colspan="2">
      {{if $facture->_id || $consult->_ref_reglements|@count}}
        {{mb_include module=cabinet template="inc_vw_facturation"}}
      {{/if}}
    </td>
  </tr>
  {{if array_key_exists("sigems", $modules)}}
    <!-- Inclusion de la gestion du système de facturation -->
    {{mb_include module=sigems template=check_actes_reels}}
  {{/if}}
</table>