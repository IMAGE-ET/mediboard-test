<script>
  refreshAssurance = function() {
    var url = new Url("facturation", "ajax_list_assurances");
    url.addParam("facture_id"   , '{{$facture->_id}}');
    url.addParam("facture_class", '{{$facture->_class}}');
    url.addParam("patient_id"   , '{{$facture->patient_id}}');
    url.requestUpdate("refresh-assurance");
  }
  closeEdition = function(form) {
    return onSubmitFormAjax(form, {
      onComplete : function() {
        Control.Modal.close();
        gestionFacture();
      }}
    );
  }
  typeFacture = function() {
    var form = getForm('Edit-CFacture');
    var type_facture  = form.type_facture.value;

    $('assurances').hide();
    $('reference_accident').hide();
    $('assur_accident').hide();
    $('assur_maladie').hide();

    if (type_facture == 'accident') {
      $('assurances').show();
      $('reference_accident').show();
      $('assur_accident').show();
      form.assurance_maladie.value  = '';
    }
    else if (type_facture == 'maladie') {
      $('assurances').show();
      $('assur_maladie').show();
      if (form.statut_pro.value == 'invalide') {
        $('reference_accident').show();
      }
      form.assurance_accident.value = '';
    }
    else if (type_facture == 'esthetique') {
      form.assurance_maladie.value  = '';
      form.assurance_accident.value = '';
    }
  }
</script>
<form name="Edit-CFacture" action="" method="post" onsubmit="return closeEdition(this);">
  {{mb_key    object=$facture}}
  {{mb_class  object=$facture}}
  <input type="hidden" name="patient_id"    value="{{$facture->patient_id}}"/>
  <input type="hidden" name="praticien_id"  value="{{$facture->praticien_id}}"/>
  <input type="hidden" name="_sejour_id"    value="{{$facture->_sejour_id}}"/>
  <input type="hidden" name="ouverture"     value="{{$facture->ouverture}}"/>
  <input type="hidden" name="numero"        value="{{$facture->numero}}"/>
  <table class="form">
    <tr>
      <td class="narrow">{{mb_label object=$facture field=type_facture}}</td>
      <td>{{mb_field object=$facture field=type_facture onchange="typeFacture();"}}</td>
      <td class="narrow"> {{mb_label object=$facture field=cession_creance}}</td>
      <td>{{mb_field object=$facture field=cession_creance}}</td>
      <td>
        {{if $facture->_class == "CFactureEtablissement"}}
          {{mb_label object=$facture field=dialyse}}
          {{mb_field object=$facture field=dialyse}}
        {{/if}}
      </td>
    </tr>
    <tr>
      <td>{{mb_label object=$facture field=envoi_xml}}</td>
      <td>{{mb_field object=$facture field=envoi_xml}}</td>
      {{if $facture->_class == "CFactureCabinet"}}
        <td>{{mb_label object=$facture field=npq}}</td>
        <td>{{mb_field object=$facture field=npq onchange="Facture.modifCloture(this.form);" readonly=$facture->cloture}}</td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
      <td>
        {{mb_label object=$facture field=statut_pro}}
        {{mb_field object=$facture field=statut_pro emptyLabel="Choisir un status"}}
      </td>
    </tr>
    <tr id="assurances">
      <td>Assurance</td>
      <td id="assur_maladie">
        <select name="assurance_maladie" style="width: 15em;">
          <option value="" {{if !$facture->assurance_maladie}}selected="selected" {{/if}}>&mdash; Choisir une assurance</option>
          {{foreach from=$facture->_ref_patient->_ref_correspondants_patient item=_assurance}}
            <option value="{{$_assurance->_id}}">
              {{$_assurance->nom}}
              {{if $_assurance->date_debut && $_assurance->date_fin}}
                Du {{$_assurance->date_debut|date_format:"%d/%m/%Y"}} au {{$_assurance->date_fin|date_format:"%d/%m/%Y"}}
              {{/if}}
            </option>
          {{/foreach}}
        </select>
      </td>
      <td style="display: none;" id="assur_accident">
        <select name="assurance_accident" style="width: 15em;">
          <option value="" {{if !$facture->assurance_accident}}selected="selected" {{/if}}>&mdash; Choisir une assurance</option>
          {{foreach from=$facture->_ref_patient->_ref_correspondants_patient item=_assurance}}
            <option value="{{$_assurance->_id}}">
              {{$_assurance->nom}}
              {{if $_assurance->date_debut && $_assurance->date_fin}}
                Du {{$_assurance->date_debut|date_format:"%d/%m/%Y"}} au {{$_assurance->date_fin|date_format:"%d/%m/%Y"}}
              {{/if}}
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr style="display: none;" id="reference_accident">
      <td colspan="2">
          <b>{{mb_label object=$facture field="ref_accident"}}:</b>{{mb_field object=$facture field="ref_accident"}}
      </td>
    </tr>
    <tr>
      <td class="button" colspan="6">
        {{if $facture->_id}}
          <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="reset" onclick="confirmDeletion(this.form, {typeName:'la facture ',objName: $V(this.form.type_facture)})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>