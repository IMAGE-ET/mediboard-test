{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<script type="text/javascript">

// UpdateFields de l'autocomplete
function updateFieldsProduitLivret(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  code_cip = dn[0].firstChild.nodeValue;
  Livret.reloadAlpha('', code_cip);
  Livret.reloadATC('', code_cip);
  $('searchProdLivret_produit').value = "";
}

Main.add(function () {

  $('_list_produits').hide();
  
  // Initialisation des onglets du menu
  Control.Tabs.create('tabs-livret', false);
  
  // Preparation du formulaire
  prepareForm(document.searchProdLivret);
  // Autocomplete
  urlAuto = new Url();
  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.autoComplete("searchProdLivret_produit", "produit_livret_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsProduitLivret,
    callback: 
      function(input, queryString){
        return (queryString + "&inLivret=1"); 
      }
    } );
});


function loadLivretArbreATC(codeATC){
  var url = new Url;
  url.setModuleAction("dPmedicament", "httpreq_vw_livret_arbre_ATC");
  if (codeATC !== '') {
    url.addParam("codeATC", codeATC);
  }
  url.requestUpdate("ATC", { waitingText: null } );
}

var nb_produits = 0;

var Livret = {
  // Ajout d'un produit dans le livret
  addProduit: function(code_cip, view_produit) {
    // Submit du formulaire d'ajout de produit
    var oForm = document.addProduit;
    oForm.code_cip.value = code_cip;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function(){
        nb_produits++;
        if(nb_produits == 5){
          $('list_produits').update("");
        }
        $('_list_produits').show();
        $('list_produits').insert(code_cip+": "+view_produit+"<br />");
      }  
    });
  },
  
  // Suppression d'un produit du livret
  delProduit: function(code_cip, lettre, codeATC) {
    // Submit du formulaire de suppression du produit
    var oForm = document.addProduit;
    oForm.code_cip.value = code_cip;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : function() {
        Livret.reloadAlpha(lettre, code_cip);
        Livret.reloadATC(codeATC, code_cip);
      } 
    });
  },
  
  editProduit: function(code_cip, lettre, codeATC) {
    this.urlEditProd = new Url;
    this.urlEditProd.setModuleAction("dPmedicament", "edit_produit_livret");
    this.urlEditProd.addParam("code_cip", code_cip);
    this.urlEditProd.addParam("lettre", lettre);
    this.urlEditProd.addParam("codeATC", codeATC);
    this.urlEditProd.popup(385, 230, "Modification d'un élément");
  },
  
  // Refresh de la liste des produits dans le livret
  // code permet de rafraichir en fonction du produit ajouté
  reloadAlpha: function(lettre, codeCIP) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_vw_livret");
    url.addParam("lettre", lettre);
    url.addParam("code_cip", codeCIP);
    url.requestUpdate("livret", { waitingText : null });
  },
  
  // Refresh de la liste des produits dans le livret
  // code permet de rafraichir en fonction du produit ajouté
  reloadATC: function(codeATC, codeCIP) {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_vw_livret_arbre_ATC");
    url.addParam("codeATC", codeATC);
    // code permet de selectionner le bon code dans le cas d'un ajout de produit
    url.addParam("code_cip", codeCIP);
    url.requestUpdate("ATC", { waitingText : null });
  }
};


// Visualisation du produit
function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(815, 620, "Descriptif produit");
}

function printLivret(){
  var url = new Url;
  url.setModuleAction("dPmedicament", "print_livret");
  url.popup(850, 650, "Livret Thérapeutique");
}

</script>

<div onclick="this.hide();" id="_list_produits" style="max-height: 100px; overflow: auto; position: fixed; top: 100px; right: 10px; background: #fff;
                                border: 1px solid #666; padding: 5px; right: 5px;">
	<strong>Produits récemment rajoutés</strong>
	<div id="list_produits" ></div>
</div>

<!-- Ajout d'un produit dans le livret -->
<table class="form">
   <tr>
     <th class="title" colspan="2">
       <button style="float: right" type="button" class="print" onclick="printLivret()">Imprimer le livret</button>
       Livret Thérapeutique
     </th>
   </tr>
   <tr>
     <th class="category">Ajout d'un produit dans le livret</th>
     <th class="category">Recherche d'un produit dans le livret</th>
   </tr>
   <tr>
     <td>
       <form action="?" method="get" name="searchProd" onsubmit="return false;">
         <!-- Champ permettant d'effectuer une recherche par autocomplete-->
         <input type="text" name="produit" value=""/>
         <!-- Champ permettant de stocker le libelle dans le cas d'une recherche par la popup -->
         <input type="text" name="_produit" value="" style="display: none;"/>  
         <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
         <button type="button" class="search" onclick="MedSelector.init('produit');">Produits</button>
         <button type="button" class="search" onclick="MedSelector.init('classe');">Classes</button>
         <button type="button" class="search" onclick="MedSelector.init('composant');">Composants</button>
         <button type="button" class="search" onclick="MedSelector.init('DC_search');">DCI</button>
         <input type="hidden" name="code_cip" onchange="Livret.addProduit(this.value, this.form._produit.value);" />
         <script type="text/javascript">
          MedSelector.init = function(onglet){
            this.sForm = "searchProd";
            this.sView = "_produit";
            this.sCode = "code_cip";
            this.sSearch = document.searchProd.produit.value;
            this.sOnglet = onglet;
            this.selfClose = false;
            this.pop();
          }
          /*
       	  MedSelector.doSet = function(){
            var oForm = document[MedSelector.sForm];
            $V(oForm[MedSelector.sView], MedSelector.prepared.nom);
            Livret.addProduit(MedSelector.prepared.code);
          }*/
        </script>
      </form>
    </td>
    <td>
      Libelle
       <form action="?" method="get" name="searchProdLivret" onsubmit="return false;">
         <input type="text" name="produit" value=""/>
         <div style="display:none;" class="autocomplete" id="produit_livret_auto_complete"></div>
       </form>
    </td>
  </tr>
</table>

<ul id="tabs-livret" class="control_tabs">
  <li><a href="#livret">Par ordre alphabétique</a></li>
  <li><a href="#ATC">Par classe ATC</a></li>
</ul>
<hr class="control_tabs" />

<!-- Affichage des produits du livret en fonction de la lettre -->
<div id="livret" style="display: none;">
  {{include file="inc_vw_livret.tpl"}}
</div>

<!-- Affichage des produits du livret en fonction de la classe ATC -->
<div id="ATC" style="display: none;">
  {{include file="inc_vw_livret_arbre_ATC.tpl"}}
</div>