<script>
  impression = function(){
    var form = document.printFactures;
    var url = new Url('facturation', 'ajax_edit_bvr');
    url.addParam('facture_class', 'CFactureEtablissement');
    url.addParam('type_pdf'     , 'impression');
    url.addElement(form.definitive);
    url.addElement(form.factures);
    url.addParam('suppressHeaders', '1');
    url.requestUpdate(SystemMessage.id);
  }
</script>
<form name="printFactures" action="" method="get">
  <input hidden="hidden" name="m" value="dPfacturation"/>
  <input hidden="hidden" name="a" value="ajax_edit_bvr"/>
  <input hidden="hidden" name="facture_class" value="CFactureEtablissement"/>
  <input hidden="hidden" name="type_pdf" value="impression"/>
  <input hidden="hidden" name="definitive" value="{{$definitive}}"/>
  <input hidden="hidden" name="suppressHeaders" value="1""/>
  <table class="form main">
    <tr>
      <th class="title" colspan="7">Impression des factures</th>
    </tr>
    <tr>
      <th></th>
      <th class="category">{{mb_label object=$facture field=numero}}</th>
      <th class="category">{{mb_label object=$facture field=praticien_id}}</th>
      <th class="category">{{mb_label object=$facture field=patient_id}}</th>
      <th class="category">Type</th>
      <th class="category">{{mb_label object=$facture field=statut_pro}}</th>
      <th class="category">Assurance</th>
    </tr>
    {{foreach from=$factures item=facture}}
      <tr>
        <td><input type="checkbox" name="factures[]" value="{{$facture->_id}}" checked="checked"/></td>
        <td>{{$facture}}</td>
        <td>
          <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_praticien->_guid}}')">{{$facture->_ref_praticien}}</a>
        </td>
        <td>
          <a href="#" onmouseover="ObjectTooltip.createEx(this, '{{$facture->_ref_patient->_guid}}')">{{$facture->_ref_patient}}</a>
        </td>
        <td>{{mb_value object=$facture field=type_facture}}</td>
        <td>{{mb_value object=$facture field=statut_pro}}</td>
        <td>
          {{if $facture->assurance_maladie}}
            {{$facture->_ref_assurance_maladie->nom}}
          {{elseif $facture->assurance_accident}}
            {{$facture->_ref_assurance_accident->nom}}
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
    <tr>
      <td colspan="7" class="button">
        <button class="print" type="submit">Imprimer</button>
      </td>
    </tr>
  </table>
</form>