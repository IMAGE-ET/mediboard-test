<script>
refreshTarif = function(){
  $('inc_codage_ngap_button_create').disabled = true;
  var oForm = document.editNGAP;
  var url = new Url("dPcabinet", "httpreq_vw_tarif_code_ngap");
  url.addElement(oForm.quantite);
  url.addElement(oForm.code);
  url.addElement(oForm.coefficient);
  url.addElement(oForm.demi);
  url.addElement(oForm.complement);
  url.addElement(oForm.executant_id);
  url.addElement(oForm.gratuit);
  url.requestUpdate('tarifActe', function() {
    $('inc_codage_ngap_button_create').disabled = false;
  });
};

ActesNGAP = {
  list_prats: {
    {{foreach from=$acte_ngap->_list_executants item=_executant name=executants}}
      {{$_executant->_id}}: {{if $_executant->spec_cpam_id}}{{$_executant->spec_cpam_id}}{{else}}0{{/if}}{{if !$smarty.foreach.executants.last}}, {{/if}}
    {{/foreach}}
  },

  checkExecutant: function(executant_id) {
    if (!ActesNGAP.list_prats[executant_id]) {
      alert("{{if $app->_ref_user->isPraticien()}}{{tr}}CActeNGAP-specialty-undefined_medecin{{/tr}}{{else}}{{tr}}CActeNGAP-specialty-undefined_user{{/tr}}{{/if}}");
    }
  },

  refreshList: function() {
    var url = new Url("dPcabinet", "httpreq_vw_actes_ngap");
    url.addParam("object_id", "{{$object->_id}}");
    url.addParam("object_class", "{{$object->_class}}");
    url.requestUpdate('listActesNGAP');
  },

  remove: function(acte_ngap_id){
    var oForm = document.editNGAP;
    oForm.del.value = 1;
    oForm.acte_ngap_id.value = acte_ngap_id;
    this.submit();
  },
  
  changeExecutant: function(acte_ngap_id, executant_id) {
    ActesNGAP.checkExecutant(executant_id);

    var oForm = document.changeExecutant;
    $V(oForm.acte_ngap_id, acte_ngap_id); 
    $V(oForm.executant_id, executant_id);

    submitFormAjax(oForm, 'systemMsg');
  },

  changePrescripteur: function(acte_ngap_id, prescripteur_id) {
    ActesNGAP.checkExecutant(prescripteur_id);

    var oForm = document.changePrescripteur;
    $V(oForm.acte_ngap_id, acte_ngap_id);
    $V(oForm.prescripteur_id, prescripteur_id);

    submitFormAjax(oForm, 'systemMsg');
  },

  submit: function() {
    ActesNGAP.checkExecutant($V(document.editNGAP.executant_id));
    var oForm = document.editNGAP;
    submitFormAjax(oForm, 'systemMsg', {
      onComplete: function() { 
        ActesNGAP.refreshList();
        if (window.Reglement) {
          Reglement.reload(false);
        }
        if (typeof DevisCodage !== 'undefined') {
          DevisCodage.refresh('{{$object->_id}}');
        }
      }
    } );
  },

  checkNumTooth: function() {
    var num_tooth = $V("editNGAP_numero_dent");

    if (num_tooth < 11 || (num_tooth > 18 && num_tooth < 21) || (num_tooth > 28 && num_tooth < 31) || (num_tooth > 38 && num_tooth < 41) || (num_tooth > 48 && num_tooth < 51) || (num_tooth > 55 && num_tooth < 61) || (num_tooth > 65 && num_tooth < 71) || (num_tooth > 75 && num_tooth < 81) ||  num_tooth > 85) {
      alert("Le numéro de dent saisi ne correspond pas à la numérotation internationale!");
    }
  }
}

{{if $object instanceof CConsultation}}
  {{assign var=sejour value=$object->_ref_sejour}}
  Main.add(function() {
    if (window.tabsConsult || window.tabsConsultAnesth) {
      var count_items = {{$object->_count_actes}};
      {{if $sejour->DP}}
      count_items++;
      {{/if}}
      {{if $sejour->DR}}
      count_items++;
      {{/if}}
      count_items += {{$sejour->_diagnostics_associes|@count}};
      Control.Tabs.setTabCount("Actes", count_items);
    }
  });
{{/if}}
</script>

{{mb_default var=_is_dentiste value=false}}
{{assign var=can_view_tarif value=true}}

{{if $conf.dPsalleOp.CActeCCAM.restrict_display_tarif}}
  {{if !$app->_ref_user->isPraticien() && !$app->_ref_user->isSecretaire()}}
    {{assign var=can_view_tarif value=false}}
  {{/if}}
{{/if}}

 <form name="editNGAP" method="post" action="">
  <input type="hidden" name="acte_ngap_id" value="" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_acte_ngap_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_id" value="{{$object->_id}}" />
  <input type="hidden" name="object_class" value="{{$object->_class}}" />
  <table class="form">
    
    {{if $object->_coded}}
    {{if $object->_class == "CConsultation"}}
    <tr>
      <td colspan="10">
        <div class="small-info">
        La cotation des actes est terminée.<br />
        Pour pouvoir coder des actes, veuillez dévalider la consultation.
        </div>
      </td>
    </tr>
    {{else}}
    <tr>
      <td colspan="10" class="text">
        <div class="small-info">
          Les actes ne peuvent plus être modifiés pour la raison suivante : {{tr}}config-dPsalleOp-COperation-modif_actes-{{$conf.dPsalleOp.COperation.modif_actes}}{{/tr}}
          <br />
          Veuillez contacter le PMSI pour toute modification.
        </div>
      </td>
    </tr>
    {{/if}}
    {{/if}}
    {{if (!$can->edit && $subject->_class == "CConsultation") || !$can->read}}
    <tr>
      <td colspan="10" class="text">
        <div class="small-info">Vous n'avez pas les droits nécessaires pour coder les actes</div>
      </td>
    </tr>
    {{else}}
    
    <tr>
      <th class="category">{{mb_title object=$acte_ngap field=quantite}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=code}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=coefficient}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=demi}}</th>
      {{if !$object->_coded}}
        {{if $can->edit}}
          <th class="category">{{mb_title object=$acte_ngap field=montant_base}}</th>
          <th class="category">{{mb_title object=$acte_ngap field=montant_depassement}}</th>
        {{/if}}
      {{else}}
        {{if $can_view_tarif && ($conf.dPsalleOp.CActeCCAM.tarif || $object->_class == "CConsultation")}}
          <th class="category">{{mb_title object=$acte_ngap field=montant_base}}</th>
          <th class="category">{{mb_title object=$acte_ngap field=montant_depassement}}</th>
        {{/if}}
      {{/if}}
      <th class="category">{{mb_title object=$acte_ngap field=complement}}</th>

      <th class="category">{{mb_title object=$acte_ngap field=gratuit}}</th>
      {{if $object->_class == "CConsultation"}}
        <th class="category">{{mb_title object=$acte_ngap field=lieu}}</th>
      {{/if}}

      {{if $object->_ref_patient->ald}}
        <th class="category">{{mb_title object=$acte_ngap field=ald}}</th>
      {{/if}}

      {{if $_is_dentiste}}
        <th class="category">{{mb_title object=$acte_ngap field=numero_dent}}</th>
      {{/if}}

      <th class="category">{{mb_title object=$acte_ngap field=execution}}</th>

      <th class="category">{{mb_title object=$acte_ngap field=executant_id}}</th>
      {{if !$object->_coded}}
        {{if $can->edit}}
           <th class="category">{{tr}}Action{{/tr}}</th>
        {{/if}}
      {{/if}}
    </tr>
    
    {{if !$object->_coded}}
      {{if $can->edit}}
        <tr>
          <td>{{mb_field object=$acte_ngap field="quantite" onchange="refreshTarif()" onkeyup="refreshTarif()"}}</td>
          <td>
            {{mb_field object=$acte_ngap field="code"}}
            <div style="display: none; width: 300px;" class="autocomplete" id="code_auto_complete"></div>
          </td>
          <td>{{mb_field object=$acte_ngap field="coefficient" size="3" onkeyup="refreshTarif()"}}</td>
          <td>{{mb_field object=$acte_ngap field="demi" onchange="refreshTarif()" onkeyup="refreshTarif()"}}</td>
          <td id="tarifActe">
            {{mb_field object=$acte_ngap field="montant_base"}}
            {{mb_field object=$acte_ngap field="lettre_cle" hidden=hidden}}
          </td>
          <td>{{mb_field object=$acte_ngap field="montant_depassement"}}</td>
          <td>{{mb_field object=$acte_ngap field="complement" onchange="refreshTarif()" onkeyup="refreshTarif()" emptyLabel="None"}}</td>
          <td>{{mb_field object=$acte_ngap field=gratuit onchange="refreshTarif()" onkeyup="refreshTarif()" typeEnum='select'}}</td>
          {{if $object->_class == "CConsultation"}}
            <td>{{mb_field object=$acte_ngap field="lieu"}}</td>
          {{/if}}

          {{if $object->_ref_patient->ald}}
            <td>{{mb_field object=$acte_ngap field="ald"}}</td>
          {{/if}}

          {{if $_is_dentiste}}
            <td>{{mb_field object=$acte_ngap field=numero_dent onchange="ActesNGAP.checkNumTooth()"}}</td>
          {{/if}}

          <td>{{mb_field object=$acte_ngap field=execution form="editNGAP" register=true}}</td>

          <td>
            {{if $object->_class == 'CConsultation' && $object->sejour_id}}
              {{mb_label object=$acte_ngap field=executant_id}} :
            {{/if}}
            <select onchange="ActesNGAP.checkExecutant($V(document.editNGAP.executant_id))" name="executant_id" style="width: 120px;" class="{{$acte_ngap->_props.executant_id}}">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{mb_include module=mediusers template=inc_options_mediuser list=$acte_ngap->_list_executants selected=$acte_ngap->executant_id}}
            </select>
            {{if $object->_class == 'CConsultation' && $object->sejour_id}}
              <br/>
              {{mb_label object=$acte_ngap field=prescripteur_id}} :
              <select onchange="ActesNGAP.checkExecutant($V(document.editNGAP.prescripteur_id))" name="prescripteur_id" style="width: 120px;" class="{{$acte_ngap->_props.prescripteur_id}}">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$acte_ngap->_list_executants selected=$acte_ngap->prescripteur_id}}
              </select>
            {{/if}}
          </td>
          <td>
            <button id="inc_codage_ngap_button_create" type="button" class="new" onclick="ActesNGAP.submit()">
              {{tr}}Create{{/tr}}
            </button>
          </td>     
        </tr>
      {{/if}}
    {{/if}}
    
    {{foreach from=$object->_ref_actes_ngap item="_acte_ngap"}}
    <tr {{if $_acte_ngap->lettre_cle == '1'}}style="font-weight: bold;"{{/if}}>
      <td>{{mb_value object=$_acte_ngap field="quantite"}}</td>
      <td>
        {{mb_include module=system template=inc_object_idsante400 object=$_acte_ngap}}
        {{mb_include module=system template=inc_object_history object=$_acte_ngap}}
        {{mb_value object=$_acte_ngap field="code"}}
      </td>
      <td>{{mb_value object=$_acte_ngap field="coefficient"}}</td>
      <td>{{mb_value object=$_acte_ngap field="demi"}}</td>
      {{if !$object->_coded}}
        {{if $can->edit}}
        <td>
        {{mb_value object=$_acte_ngap field="montant_base"}}
        {{mb_value object=$_acte_ngap field="lettre_cle"}}
        </td>
        <td>{{mb_value object=$_acte_ngap field="montant_depassement"}}</td>
        {{/if}}
      {{else}}
        {{if $can_view_tarif && ($conf.dPsalleOp.CActeCCAM.tarif || $object->_class == "CConsultation")}}
        <td>{{mb_value object=$_acte_ngap field="montant_base"}}</td>
        <td>{{mb_value object=$_acte_ngap field="montant_depassement"}}</td>
        {{/if}}
      {{/if}}
      <td>
        {{if $_acte_ngap->complement}}
          {{mb_value object=$_acte_ngap field="complement"}}
        {{else}}
          Aucun
        {{/if}}
      </td>

      <td>
        {{mb_value object=$_acte_ngap field=gratuit}}
      </td>
      {{if $object->_class == "CConsultation"}}
        <td>{{mb_value object=$_acte_ngap field="lieu"}}</td>
      {{/if}}

      {{if $object->_ref_patient->ald}}
        <td>{{mb_value object=$_acte_ngap field="ald"}}</td>
      {{/if}}

      {{if $_is_dentiste}}
        <td>{{mb_value object=$_acte_ngap field=numero_dent}}</td>
      {{/if}}

      <td>{{mb_value object=$_acte_ngap field=execution}}</td>

      {{assign var="executant" value=$_acte_ngap->_ref_executant}}
      {{assign var=prescripteur value=$_acte_ngap->_ref_prescripteur}}
      <td>
        {{if !$object->_coded}}
          {{if $can->edit}}
            {{if $object->_class == 'CConsultation' && $object->sejour_id}}
              <span style="font-weight: normal;">{{mb_label object=$_acte_ngap field=executant_id}} :</span>
            {{/if}}
            <select onchange="ActesNGAP.changeExecutant('{{$_acte_ngap->_id}}', $V(this))" name="executant" style="width: 150px;" class="{{$acte_ngap->_props.executant_id}}">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{mb_include module=mediusers template=inc_options_mediuser selected=$_acte_ngap->executant_id list=$acte_ngap->_list_executants}}
            </select>
            {{if $object->_class == 'CConsultation' && $object->sejour_id}}
              <br/>
              <span style="font-weight: normal;">{{mb_label object=$_acte_ngap field=prescripteur_id}} :</span>
              <select onchange="ActesNGAP.changePrescripteur('{{$_acte_ngap->_id}}', $V(this))" name="prescripteur_id" style="width: 150px;" class="{{$_acte_ngap->_props.prescripteur_id}}">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser selected=$_acte_ngap->prescripteur_id list=$acte_ngap->_list_executants}}
              </select>
            {{/if}}
          {{else}}
            {{if $object->_class == 'CConsultation' && $object->sejour_id}}
              <span style="font-weight: normal;">{{tr}}CActeNGAP-executant_id-court{{/tr}} : </span>
            {{/if}}
            <span class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
             {{$executant}}
            </span>
            {{if $object->_class == 'CConsultation' && $object->sejour_id}}
              <br/>
              <span style="font-weight: normal;">{{tr}}CActeNGAP-prescripteur_id-court{{/tr}} : </span>
              <span class="mediuser" style="border-color: #{{$prescripteur->_ref_function->color}};">
               {{$prescripteur}}
              </span>
            {{/if}}
          {{/if}}
        {{else}}
          {{if $object->_class == 'CConsultation' && $object->sejour_id}}
            <span style="font-weight: normal;">{{tr}}CActeNGAP-executant_id-court{{/tr}} : </span>
          {{/if}}
          <span class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
           {{$executant}}
          </span>
          {{if $object->_class == 'CConsultation' && $object->sejour_id}}
            <br>
            <span style="font-weight: normal;">{{tr}}CActeNGAP-prescripteur_id-court{{/tr}} : </span>
            <span class="mediuser" style="border-color: #{{$prescripteur->_ref_function->color}};">
             {{$prescripteur}}
            </span>
          {{/if}}
        {{/if}}
      </td>

      {{if !$object->_coded}}
        {{if $can->edit}}
          <td>
             <button type="button" class="trash" onclick="ActesNGAP.remove({{$_acte_ngap->_id}})">
              {{tr}}Delete{{/tr}}
             </button>
          </td>
        {{/if}}
      {{/if}}
   </tr>
   {{/foreach}}
   {{/if}}
 </table>
</form>


<form name="changeExecutant" method="post" action=""> 
  <input type="hidden" name="acte_ngap_id" value="" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_acte_ngap_aed" />
  
  <input type="hidden" name="executant_id" value="" />
</form>

<form name="changePrescripteur" method="post" action="">
  <input type="hidden" name="acte_ngap_id" value="" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_acte_ngap_aed" />

  <input type="hidden" name="prescripteur_id" value="" />
</form>

<script>

{{if !$object->_coded}}

// Preparation du formulaire
prepareForm(document.editNGAP);

// UpdateFields de l'autocomplete
function updateFields(selected) {
  $V(document.editNGAP.code, selected.down('.code').innerHTML, false);
  refreshTarif();
}

// Autocomplete
var url = new Url("dPcabinet", "httpreq_do_ngap_autocomplete");
url.addParam("object_id", "{{$object->_id}}");
url.addParam("object_class", "{{$object->_class}}");
url.autoComplete(getForm('editNGAP').code, 'code_auto_complete', {
    minChars: 1,
    updateElement: updateFields,
    callback: function(input, queryString) {
      var executant_id = $V(getForm('editNGAP').executant_id);
      return queryString + "&executant_id=" + executant_id;
    }
} );

{{/if}}

</script>