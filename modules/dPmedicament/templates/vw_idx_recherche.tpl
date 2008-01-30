<script type="text/javascript">

function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(700, 620, "Descriptif produit");
}

</script>

<table class="main">
  <tr>
    <td>
      <form name="formSearch" action="?m=dPmedicament&tab=vw_idx_recherche" method="post">
        <input type="text" name="produit" value="{{$produit}}"/>
        <button type="button" class="search" onclick="submit();">Rechercher</button>
        <br />
        Afficher les produits supprimés
        <input type="checkbox" name="supprime" value="1" {{if $supprime == 1}}checked = "checked"{{/if}} />
        <br />
        Rechercher n'importe où dans le nom du produit
        <input type="checkbox" name="position_text" value="partout" {{if $position_text == 'partout'}}checked = "checked"{{/if}} />
      </form>
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="2">{{$mbProduit->distObj->TabProduit|@count}} Résultat(s)</th>
        </tr>
        <tr>
          <th>CIP</th>
          <th>Produit</th>
        </tr>
        {{foreach from=$mbProduit->distObj->TabProduit item="produit"}}
        <tr>
          <td>
            {{$produit->CodeCIP}}
            {{if $produit->Hospitalier}}
            <img src="./images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
            {{/if}}
          </td>
          <td>
            <a href="#produit{{$produit->CodeCIP}}" onclick="viewProduit({{$produit->CodeCIP}})" {{if $produit->DateSupp}}style="color: red"{{/if}}>{{$produit->Libelle}}</a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
  
  
  
  
  
  