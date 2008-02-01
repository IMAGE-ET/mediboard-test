<script type="text/javascript">

  if(MedSelector.oUrl) {
    MedSelector.close();
  }
  if(Livret.urlEditProd) {
    Livret.urlEditProd.close();
  }
  // UpdateFields de l'autocomplete
  function updateFields(selected) {
    Element.cleanWhitespace(selected);
    dn = selected.childNodes;
    Livret.addProduit(dn[0].firstChild.nodeValue);
    $('searchProd_produit').value = "";
  }

</script>

<form action="?m=dPmedicament" method="post" name="addProduit" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPmedicament" />
  <input type="hidden" name="dosql" value="do_produit_livret_aed" />
  <input type="hidden" name="produit_livret_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="group_id" value="{{$g}}"/>
  <input type="hidden" name="code_cip" value=""/>
</form>


<table class="tbl">
  <tr>
    <th colspan="9">{{$produits_livret|@count}} produits dans le livret</th>
  </tr>
  <tr>
    <th>Actions</th>
    <th>Libelle</th>
    <th>Code CIP</th>
    <th>Prix Hôpital</th>
    <th>Prix Ville</th>
    <th>Date Prix Hôpital</th>
    <th>Date Prix Ville</th>
    <th>Code Interne</th>
    <th>Commentaire</th>
  </tr>
  {{foreach from=$produits_livret item=produit_livret}}
  <tr>
    <td>
      <button type="button" class="trash notext" onclick="Livret.delProduit({{$produit_livret->_id}})">
        {{tr}}Delete{{/tr}}
      </button>
      <button type="button" class="edit notext" onclick="Livret.editProduit({{$produit_livret->_id}})">
        {{tr}}Modify{{/tr}} 
      </button>
    </td>  
    <td>
      <a href="#produit{{$produit_livret->_id}}" onclick="viewProduit({{$produit_livret->code_cip}})">
        {{$produit_livret->_ref_produit->libelle}}
      </a>
    </td>
    <td>
      {{$produit_livret->code_cip}}
    </td>
    <td>
      {{$produit_livret->prix_hopital}}
    </td>
    <td>
      {{$produit_livret->prix_ville}}
    </td>
    <td>
      {{$produit_livret->date_prix_hopital}}
    </td>
    <td>
      {{$produit_livret->date_prix_ville}}
    </td>
    <td>
      {{$produit_livret->code_interne}}
    </td> 
    <td>
      {{$produit_livret->commentaire}}
    </td> 
  </tr>
  {{/foreach}}
</table>


<script type="text/javascript">
  // Preparation du formulaire
  prepareForm(document.addProduit);
  prepareForm(document.searchProd);
  // Autocomplete
  urlAuto = new Url();
  urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
  urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
      minChars: 3,
      updateElement: updateFields
  } );
</script>