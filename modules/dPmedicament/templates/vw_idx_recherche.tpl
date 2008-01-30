<script type="text/javascript">


function setClose(libelle) {
  var oSelector = window.opener.MedSelector;
  oSelector.set(libelle);
  if(oSelector.selfClose) {
    window.close();
  }
}


function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(700, 620, "Descriptif produit");
}

function changeFormSearch(){
  var oForm = document.formSearch;
  var type_recherche = oForm.type_recherche.value;
  if(type_recherche != "nom"){
    oForm.position_text.checked = false;
    oForm.position_text.disabled = true;
  }
  if(type_recherche == "nom"){
    oForm.position_text.disabled = false;
  }
}

function produitSelector(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_idx_recherche");
  url.addParam("dialog", "1");
  url.popup(400, 400, "Recherche Medicament");
}


function pageMain(){
  changeFormSearch();
}

</script>

<table class="main">
  <tr>
    <td><button type="button" class="search" onclick="produitSelector();" /></td>
  </tr>
  <tr>
    <td>Recherche par
      <form name="formSearch" action="?" method="get">
     {{if $dialog}}
      <input type="hidden" name="dialog" value="1" />
      <input type="hidden" name="m" value="dPmedicament" />
      <input type="hidden" name="a" value="vw_idx_recherche" />
      
      {{else}}
      <input type="hidden" name="m" value="dPmedicament" />
      <input type="hidden" name="tab" value="vw_idx_recherche" />
      
      {{/if}}
        <select name="type_recherche" onchange="changeFormSearch(this.value);">
          <option value="nom" {{if $type_recherche == 'nom'}}selected = "selected"{{/if}}>Nom</option>
          <option value="cip" {{if $type_recherche == 'cip'}}selected = "selected"{{/if}}>CIP</option>
          <option value="ucd" {{if $type_recherche == 'ucd'}}selected = "selected"{{/if}}>UCD</option>
        </select>
        <br />
        <input type="text" name="produit" value="{{$produit}}"/>
        <button type="button" class="search" onclick="submit();">Rechercher</button>
        <br />
        <input type="checkbox" name="supprime" value="1" {{if $supprime == 1}}checked = "checked"{{/if}} />
        Afficher les produits supprimés
        <br />
        <input type="checkbox" name="position_text" value="partout" {{if $type_recherche == 'partout'}}checked = "checked"{{/if}} />
        Rechercher n'importe où dans le nom du produit
      </form>
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>CIP</th>
          <th>Produit</th>
        </tr>
        {{foreach from=$produits item="produit"}}
        <tr>
          <td>
            {{if $dialog}}
            <img src="./images/icons/plus.gif" onclick="setClose('{{$produit->libelle}}')" alt="Produit Hospitalier" title="Produit Hospitalier" />
            {{/if}}
           
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
            <a href="#produit{{$produit->code_cip}}" onclick="viewProduit({{$produit->code_cip}})" {{if $produit->_suppression}}style="color: red"{{/if}}>{{$produit->libelle}}</a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
  
  
  
  
  
  