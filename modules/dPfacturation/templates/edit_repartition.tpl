<script>
  modifRepartition = function(form) {
    {{if $facture->_ref_consults|@count == 1}}
      var formConsult = getForm("modif-repartitionConsult");
      formConsult.du_patient.value =  form.du_patient.value;
      formConsult.du_tiers.value =  form.du_tiers.value;
      result = onSubmitFormAjax(formConsult);
    {{/if}}
    return onSubmitFormAjax(form, {
      onComplete : function() {
        {{if $facture->_ref_consults|@count == 1}}
          if ($('reglement')) {
            var url = new Url("cabinet", "httpreq_vw_reglement");
            url.addParam("selConsult", '{{$consult->_id}}');
            url.requestUpdate('reglement');
          }
          else {
            var url = new Url('facturation', 'ajax_view_facture');
            url.addParam('facture_id'   , '{{$facture->_id}}');
            url.addParam('facture_class', '{{$facture->_class}}');
            url.requestUpdate("load_facture");
          }
        {{/if}}
        Control.Modal.close();
      }}
    );
  }

  modifDuPatient = function() {
    var form = getForm("Edit-repartitionFacture");
    var total = form.montant_total.value;
    var du_patient = form.du_patient.value;
    form.du_tiers.value = Math.round((total - du_patient)*100)/100;
  }
  modifDusTiers = function() {
    var form = getForm("Edit-repartitionFacture");
    var total = form.montant_total.value;
    var du_tiers = form.du_tiers.value;
    form.du_patient.value = Math.round((total - du_tiers)*100)/100;
  }
</script>

<form name="Edit-repartitionFacture" action="?m={{$m}}" method="post" onsubmit="modifRepartition(this);">
  {{mb_key    object=$facture}}
  {{mb_class  object=$facture}}
  <input type="hidden" name="del" value="0"/>
  <input type="hidden" name="montant_total" value="{{$montant_total}}"/>
  <table class="form">
    <tr>
      <th class="title" colspan="2">Répartition des montants de la facture {{$facture->_view}}<br/> du {{mb_value object=$facture field=ouverture}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$facture field=du_patient}}</th>
      <td>{{mb_field object=$facture field=du_patient onchange="modifDuPatient();"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$facture field=du_tiers}}</th>
      <td>{{mb_field object=$facture field=du_tiers onchange="modifDusTiers();"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="cancel" type="button" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button>
        <button class="save" type="button" onclick="return modifRepartition(this.form);">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{if $facture->_ref_consults|@count == 1}}
  <form name="modif-repartitionConsult" action="?m={{$m}}" method="post">
    {{mb_key    object=$consult}}
    {{mb_class  object=$consult}}
    <input type="hidden" name="del" value="0"/>
    <input type="hidden" name="du_patient" value=""/>
    <input type="hidden" name="du_tiers" value=""/>
  </form>
{{/if}}