<script type="text/javascript">

function setClose(code, line_id) {
  var oSelector = window.opener.EquivSelector;
  oSelector.set(code, line_id);
  if(oSelector.selfClose) {
    window.close();
  }
}

function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(815, 620, "Descriptif produit");
}

</script>


<table class="tbl">
  <tr>
    <th>CIP</th>
    <th>UCD</th>
    <th>Produit</th>
    <th>Laboratoire</th>
  </tr>
  {{foreach from=$equivalents item="produit"}}
  <tr>
    <td>
      <img src="./images/icons/plus.gif" onclick="setClose('{{$produit->code_cip}}', '{{$line_id}}')" alt="Produit Hospitalier" title="Produit Hospitalier" />    
      {{$produit->code_cip}}
      {{if $produit->hospitalier}}
      <img src="./images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      {{/if}}
      {{if $produit->_generique}}
      <img src="./images/icons/generiques.gif" alt="Produit Générique" title="Produit Générique" />
      {{/if}}
      {{if $produit->_referent}}
      <img src="./images/icons/referents.gif" alt="Produit Référent" title="Produit Référent" />
      {{/if}}
    </td>
    <td>
      {{$produit->code_ucd}}
    </td>
    <td>
      <a href="#produit{{$produit->code_cip}}" onclick="viewProduit({{$produit->code_cip}})" {{if $produit->_supprime}}style="color: red"{{/if}}>{{$produit->libelle}}</a>
    </td>
    <td>
      {{$produit->nom_laboratoire}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="4">Aucun équivalent trouvé</td>
  </tr>
  {{/foreach}}
</table>
   