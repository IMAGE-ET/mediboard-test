{{mb_script module=dPcabinet script=facture}}
<script>
refresh = function(facture, name) {
  var oForm = getForm('eclatement_facture');
  
  var url = new Url('dPcabinet', 'ajax_cut_facture');
  url.addParam('factureconsult_id'  , oForm.factureconsult_id.value);
  url.addParam(facture.name         , facture.value);
  url.addParam('caisse'             , name);
  url.addParam('refresh'            , 1);
  url.requestUpdate('caisse_'+name);
}

Main.add(function () {
  var tabs = Control.Tabs.create('tab-cut-facture', false);
});
</script>

<table class="main tbl">
  <tr>
    <th class="category" colspan="2">{{$facture->_view}}</th>
  </tr>
  <tr>
    <td style="text-align:center;">Montant total: {{mb_value object=$facture field="_montant_avec_remise"}} </td>
  </tr>
</table>
<form name="eclatement_facture" id="cut_facture" action="" method="post" onsubmit="Facture.modifCloture(this);">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_cut_facture_aed" />
  <input type="hidden" name="factureconsult_id" value="{{$facture->_id}}" />
  <input type="hidden" name="patient_id" value="{{$facture->patient_id}}" />
  <table>
    <tr>
      {{foreach from=$proposition_tarifs item=tarifs key=caisse name=ici}}
        <td id="caisse_{{$caisse}}" VALIGN="top">
          {{mb_include module="cabinet" template="inc_vw_cut_facture"}}
        </td>
      {{/foreach}}
    </tr>
    <tr>
      <td colspan="{{$proposition_tarifs|@count}}"  class="button">
        <button type="button" class="submit" onclick="Facture.cut(this.form);">{{tr}}Create{{/tr}}</button>
        <button type="button" class="cancel" onclick=" Facture.modal.close();" >{{tr}}Cancel{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>