<script type="text/javascript">
 
loadDCI = function(DC_search, DCI_code, dialog){
  url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_vw_DCI");
  url.addParam("DC_search", DC_search);
  url.addParam("DCI_code", DCI_code);
  url.addParam("dialog", dialog);
  url.requestUpdate("DCI", { waitingText: null } );
}


</script>

<table class="form">
  <tr>
    <td>
      <form name="rechercheDCI" action="?" method="get">
        Dénomination commune
        <input type="text" name="DCI" value="{{$DC_search}}" />
        <button type="button" class="search" onclick="loadDCI(this.form.DCI.value, '', '{{$dialog}}')">Rechercher</button>
      </form>
    </td>
  </tr>
</table>

{{if $tabDCI}}
<table class="tbl">
  <tr>
    <th>{{$tabDCI|@count}} DCI trouvées</th>
  </tr>
  {{foreach from=$tabDCI item=_DCI}}
  <tr>
    <td><a href="#" onclick="loadDCI('', '{{$_DCI->Code}}', '{{$dialog}}')">{{$_DCI->Libelle}}</td>
  </tr>
  {{/foreach}}
</table>
{{/if}}

{{if $DCI_code}}
<table class="tbl">
  <tr>
    <th {{if $dialog}}colspan="2"{{/if}}>{{$DCI->_ref_produits|@count}} produits trouvés</th>
  </tr>
  {{foreach from=$tabProduit item=dosageProduit key="dosage"}}
  <tr>
    <th {{if $dialog}}colspan="2"{{/if}} class="title">{{$dosage}}</th>
  </tr>
    {{foreach from=$dosageProduit item="formeProduit" key="forme"}}
    <tr>
      <th {{if $dialog}}colspan="2"{{/if}}>{{$forme}}</th>
    </tr>
      {{foreach from=$formeProduit item="_produit"}}
          <tr>
            {{if $dialog}}
            <td>
            <img src="./images/icons/plus.gif" onclick="setClose('{{$_produit->Libelle}}', '{{$_produit->CIP}}')" alt="Ajouter à la prescription" title="Ajouter à la prescription" />
            </td>
            {{/if}}
            <td>
            <a href="#produit{{$_produit->CIP}}" onclick="viewProduit({{$_produit->CIP}})">{{$_produit->Libelle}}</a>
            </td>
          </tr>
        {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>
{{/if}}