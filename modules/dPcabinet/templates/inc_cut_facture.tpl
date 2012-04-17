{{mb_script module=dPcabinet script=facture}}

<script>
refresh = function(oForm) {
  var url = new Url('dPcabinet', 'ajax_cut_facture');
  url.addParam('factureconsult_id'    , oForm.factureconsult_id.value);
  url.addParam('nb_factures'    , oForm.nbfacture.value);
  url.requestUpdate(Facture.modal.container.down('.content'));
}
</script>

<form name="eclatement_facture" id="cut_facture" action="" method="post" onsubmit="Facture.modifCloture(this);">
  <table class="main tbl">
    <input type="hidden" name="m" value="dPcabinet" />
    <input type="hidden" name="dosql" value="do_cut_facture_aed" />
    <input type="hidden" name="factureconsult_id" value="{{$facture->_id}}" />
    <input type="hidden" name="patient_id" value="{{$facture->patient_id}}" />
    
    <tr>
      <th class="category" colspan="2">{{$facture->_view}}</th>
    </tr>
    <tr>
      <td>Montant Total de la facture:</td>
      <td>{{mb_value object=$facture field="_montant_avec_remise"}} </td>
    </tr>
    <tr>
      <td>Scinder la facture en:</td>
      <td><input type="text" name="nbfacture" value="{{$nb_factures}}" onchange="refresh(this.form);" /></td>
    </tr>
    {{section name=tab start=0 loop=$nb_factures}}
      <tr>
        <td>Facture n° {{math equation=" x + 1" x=$smarty.section.tab.index}}</td>
        <td><input type="text" name="tarif{{$smarty.section.tab.index}}" value="{{$proposition_tarifs|string_format:"%.2f"}}""/></td>
      </tr>
    {{/section}}
    <tr>
      <td colspan="2"  class="button">
        <button type="button" class="submit" onclick="Facture.cut(this.form);">{{tr}}Create{{/tr}}</button>
        <button type="button" class="cancel" onclick=" Facture.modal.close();" >{{tr}}Cancel{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>