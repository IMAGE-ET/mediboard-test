{{if $conf.dPfacturation.CRelance.use_relances}}
  {{mb_script module=facturation script=relance ajax="true"}}
{{/if}}
<script>
viewFacture = function(element, facture_id, facture_class){
  if (element) {
    element.up("tr").addUniqueClassName("selected");
  }
   
  var url = new Url("facturation"     , "ajax_view_facture");
  url.addParam("facture_id", facture_id);
  url.addParam("object_class", facture_class);
  url.requestUpdate('load_facture');
}

showLegend = function() {
  new Url('facturation', 'vw_legende').
  addParam('classe', '{{$facture->_class}}').
  requestModal(200);
}
</script>

<table class="main" style="overflow:auto;">
  <tr>
    <td style="width:200px;" id="liste_factures">
      {{mb_include module=facturation template=inc_list_factures}}
    </td>
    <td id="load_facture">
      {{mb_include module=facturation template=inc_vw_facturation }}
    </td>
  </tr>
</table>