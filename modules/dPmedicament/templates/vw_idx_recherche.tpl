<script type="text/javascript">

function loadArbreATC(codeATC, dialog){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_vw_arbre_ATC");
  url.addParam("codeATC", codeATC);
  url.addParam("dialog", dialog);
  url.requestUpdate("ATC", { waitingText: null } );
}

function loadArbreBCB(codeBCB, dialog){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_vw_arbre_BCB");
  url.addParam("codeBCB", codeBCB);
  url.addParam("dialog", dialog);
  url.requestUpdate("BCB", { waitingText: null } );
}

function viewATC(){
  $('ATC').show();
  $('BCB').hide();
}

function viewBCB(){
  $('BCB').show();
  $('ATC').hide();
}


function setClose(libelle, code_cip) {
  var oSelector = window.opener.MedSelector;
  oSelector.set(libelle, code_cip);
  window.close();
}

function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(900, 620, "Descriptif produit");
}

function changeFormSearch(){
  var oForm = document.formSearch;
  var type_recherche = oForm.type_recherche.value;
  if(type_recherche != "nom"){
    oForm.position_text.checked = false;
    oForm.position_text.disabled = true;
  } else {
    oForm.position_text.disabled = false;
  }
}

Main.add(function () {
  changeFormSearch();
  searchTabs = new Control.Tabs('main_tab_group');
  searchTabs.setActiveTab('{{$onglet_recherche}}');
  
  // Au chargement, vue des classes ATC
  viewATC();  
});
</script>

<ul id="main_tab_group" class="control_tabs">
  <li><a href="#produits">Produits</a></li>
  <li><a href="#classes">Classes</a></li>
  <li><a href="#composants">Composants</a></li>
  <li><a href="#DCI">DCI</a></li>
</ul>
<hr class="control_tabs" />

<div id="produits" style="display: none;">
  Recherche par
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
    <input type="checkbox" name="position_text" value="partout" {{if $param_recherche == 'partout'}}checked = "checked"{{/if}} />
    Rechercher n'importe où dans le nom du produit
    <br />
    <input type="checkbox" name="rechercheLivret" value="1" {{if $rechercheLivret == 1}}checked = "checked"{{/if}} />
    Rechercher uniquement dans le livret thérapeutique
  </form>
  <table class="tbl">
    <tr>
      <th style="width: 0.1%;">CIP</th>
      <th style="width: 0.1%;"></th>
      <th>UCD</th>
      <th>Produit</th>
      <th>Laboratoire</th>
    </tr>
    {{foreach from=$produits item="produit"}}
    <tr>
      <td>
        {{if $dialog && !$produit->_supprime}}
        <img src="./images/icons/plus.gif" onclick="setClose('{{$produit->libelle}}', '{{$produit->code_cip}}')" alt="Sélectionner" title="Sélectionner" />
        {{/if}}
       
        {{$produit->code_cip}}
      </td>
      <td>
        {{if !$produit->inLivret}}
        <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret thérapeutique" title="Produit non présent dans le livret thérapeutique" />
        {{/if}}
        
        {{if $produit->hospitalier}}
        <img src="./images/icons/hopital.gif" alt="Produit hospitalier" title="Produit hospitalier" />
        {{/if}}
        {{if $produit->_generique}}
        <img src="./images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
        {{/if}}
        {{if $produit->_referent}}
        <img src="./images/icons/referents.gif" alt="Produit référent" title="Produit référent" />
        {{/if}}
        {{if $produit->_supprime}}
        <img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
        {{/if}}
        {{if !$produit->inT2A}}
          <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
        {{/if}}  
      </td>
      <td>
        {{$produit->code_ucd}}
      </td>
      <td class="text">
        <a href="#produit{{$produit->code_cip}}" onclick="viewProduit({{$produit->code_cip}})" {{if $produit->_supprime}}style="color: red"{{/if}}>{{$produit->libelle_long}}</a>
      </td>
      <td>
        {{$produit->nom_laboratoire}}
      </td>
    </tr>
    {{/foreach}}
  </table>
</div>
  
<div id="classes" style="display: none;">
  <input type="radio" name="type_classe" value="atc" checked="checked" onchange="viewATC();" />
  Classes ATC
  <input type="radio" name="type_classe" value="bcb" onchange="viewBCB();"/>
  CLasses BCB
  <div id="ATC">{{include file="inc_vw_arbre_ATC.tpl"}}</div>
  <div id="BCB">{{include file="inc_vw_arbre_BCB.tpl"}}</div>
</div>
<div id="composants" style="display: none;">{{include file="inc_vw_composants.tpl"}}</div>
<div id="DCI" style="display: none;">{{include file="inc_vw_DCI.tpl"}}</div>
