<script type="text/javascript">

refreshTarif = function(){
  var oForm = document.editNGAP;
  var url = new Url("dPcabinet", "httpreq_vw_tarif_code_ngap");
  url.addElement(oForm.quantite);
  url.addElement(oForm.code);
  url.addElement(oForm.coefficient);
  url.addElement(oForm.demi);
  url.addElement(oForm.complement);
  url.requestUpdate('tarifActe');
}
  
ActesNGAP = {
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
  
  changeExecutant: function(acte_ngap_id, executant_id){
    var oForm = document.changeExecutant;
    $V(oForm.acte_ngap_id, acte_ngap_id); 
    $V(oForm.executant_id, executant_id);
    
    submitFormAjax(oForm, 'systemMsg');
  },
  
  submit: function() {
    var oForm = document.editNGAP;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete: function() { 
        ActesNGAP.refreshList();
        if (window.Reglement) {
          Reglement.reload(false);
        }
      }
    } );
  }
}

</script>

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
      <th class="category">{{mb_title object=$acte_ngap field=exoneration}}</th>

      {{if $m=="dPcabinet"}}
        <th class="category">{{mb_title object=$acte_ngap field=lieu}}</th>
      {{/if}}

      {{if $object->_ref_patient && $object->_ref_patient->ald}}
        <th class="category">{{mb_title object=$acte_ngap field=ald}}</th>
      {{/if}}

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
            {{mb_field object=$acte_ngap field="code" onchange="refreshTarif()"}}
            <div style="display: none; width: 300px;" class="autocomplete" id="code_auto_complete"></div>
          </td>
          <td>{{mb_field object=$acte_ngap field="coefficient" size="3" onchange="refreshTarif()" onkeyup="refreshTarif()"}}</td>
          <td>{{mb_field object=$acte_ngap field="demi" onchange="refreshTarif()" onkeyup="refreshTarif()"}}</td>
          <td id="tarifActe">
            {{mb_field object=$acte_ngap field="montant_base"}}
            {{mb_field object=$acte_ngap field="lettre_cle" hidden=hidden}}
          </td>
          <td>{{mb_field object=$acte_ngap field="montant_depassement"}}</td>
          <td>{{mb_field object=$acte_ngap field="complement" onchange="refreshTarif()" onkeyup="refreshTarif()" emptyLabel="None"}}</td>
          <td>{{mb_field object=$acte_ngap field="exoneration"}}</td>

          {{if $m=="dPcabinet"}}
            <td>{{mb_field object=$acte_ngap field="lieu"}}</td>
          {{/if}}

          {{if $object->_ref_patient && $object->_ref_patient->ald}}
            <td>{{mb_field object=$acte_ngap field="ald"}}</td>
          {{/if}}

          <td>
            <select name="executant_id" style="width: 120px;" class="{{$acte_ngap->_props.executant_id}}">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{mb_include module=mediusers template=inc_options_mediuser list=$acte_ngap->_list_executants selected=$acte_ngap->executant_id}}
            </select>
          </td>
          <td>
            <button type="button" class="new" onclick="ActesNGAP.submit()">
              {{tr}}Create{{/tr}}
            </button>
          </td>     
        </tr>
      {{/if}}
    {{/if}}
    
    {{foreach from=$object->_ref_actes_ngap item="_acte_ngap"}}
    <tr {{if $_acte_ngap->lettre_cle == '1'}}style="font-weight: bold;"{{/if}}>
      <td>{{mb_value object=$_acte_ngap field="quantite"}}</td>
      <td>{{mb_value object=$_acte_ngap field="code"}}</td>
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
      <td>{{mb_value object=$_acte_ngap field="exoneration"}}</td>

      {{if $m=="dPcabinet"}}
        <td>{{mb_value object=$_acte_ngap field="lieu"}}</td>
      {{/if}}

      {{if $object->_ref_patient && $object->_ref_patient->ald}}
        <td>{{mb_value object=$_acte_ngap field="ald"}}</td>
      {{/if}}

      {{assign var="executant" value=$_acte_ngap->_ref_executant}}
      <td>
        {{if !$object->_coded}}
          {{if $can->edit}}
            <select onchange="ActesNGAP.changeExecutant('{{$_acte_ngap->_id}}', $V(this))" name="executant" style="width: 150px;" class="{{$acte_ngap->_props.executant_id}}">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{mb_include module=mediusers template=inc_options_mediuser selected=$_acte_ngap->executant_id list=$acte_ngap->_list_executants}}
            </select>
          {{else}}
            <div class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
             {{$executant}}
            </div>
          {{/if}}
        {{else}}
        <div class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
         {{$executant}}
        </div>
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

<script type="text/javascript">

{{if !$object->_coded}}

// Preparation du formulaire
prepareForm(document.editNGAP);

// UpdateFields de l'autocomplete
function updateFields(selected) {
  $V(document.editNGAP.code, selected.down('.code').innerHTML, true);
}

// Autocomplete
var url = new Url("dPcabinet", "httpreq_do_ngap_autocomplete");
url.addParam("object_id", "{{$object->_id}}");
url.addParam("object_class", "{{$object->_class}}");
url.autoComplete(getForm('editNGAP').code, 'code_auto_complete', {
    minChars: 1,
    updateElement: updateFields
} );

{{/if}}
  
</script>