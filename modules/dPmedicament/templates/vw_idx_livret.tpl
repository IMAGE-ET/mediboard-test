{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<script type="text/javascript">

var Livret = {
  // Ajout d'un produit dans le livret
  addProduit: function(code) {
    var oForm = document.addProduit;
    oForm.code_cip.value = code;
    submitFormAjax(oForm, 'systemMsg', { onComplete : Livret.reload });
  },
  // Suppression d'un produit du livret
  delProduit: function(produit_id) {
    var oForm = document.addProduit;
    oForm.produit_livret_id.value = produit_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { onComplete : Livret.reload });
  },
  editProduit: function(produit_id) {
    this.urlEditProd = new Url;
    this.urlEditProd.setModuleAction("dPmedicament", "edit_produit_livret");
    this.urlEditProd.addParam("produit_id", produit_id);
    this.urlEditProd.popup(385, 230, "Modification d'un élément");
  },
  // Refresh de la liste des produits dans le livret
  reload: function() {
    var url = new Url;
    url.setModuleAction("dPmedicament", "httpreq_vw_livret");
    url.requestUpdate("livret", { waitingText : null });
  }
};


// Visualisation du produit
function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(700, 620, "Descriptif produit");
}


</script>




<!-- Ajout d'un produit dans le livret -->
<table class="form">
   <tr>
     <th class="title">
       Livret Thérapeutique
     </th>
   </tr>
   <tr>
     <td>
       <form action="?" method="get" name="searchProd" onsubmit="return false;">
         <input type="text" name="produit" value=""/>
         <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
         <button type="button" class="search" onclick="MedSelector.init('produit');">Produits</button>
         <button type="button" class="search" onclick="MedSelector.init('classe');">Classes</button>
         <button type="button" class="search" onclick="MedSelector.init('composant');">Composants</button>
         <button type="button" class="search" onclick="MedSelector.init('DC_search');">DCI</button>
         <script type="text/javascript">
          MedSelector.init = function(onglet){
            this.sForm = "searchProd";
            this.sView = "produit";
            this.sSearch = document.searchProd.produit.value;
            this.sOnglet = onglet;
            this.selfClose = false;
            this.pop();
          }
          MedSelector.set = function(nom, code){
            Livret.addProduit(code);
          }
        </script>
      </form>
    </td>
  </tr>
</table>


<!-- Affichage des produits du livret -->
<div id="livret">
  {{include file="inc_vw_livret.tpl"}}
</div>


