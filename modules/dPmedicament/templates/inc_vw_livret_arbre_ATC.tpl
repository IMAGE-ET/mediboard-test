<script type="text/javascript">

// Fermeture de la popup de modification de produit a la fin du 2eme refresh (reloadATC)
if(Livret.urlEditProd) {
  Livret.urlEditProd.close();
}
if(MedSelector.oUrl) {
  MedSelector.close();
}
	
</script>

<table class="main">
  {{if !$codeATC}}
  <div class="big-info">
	  Veuillez sélectionner la classe ATC dans laquelle vous désirez rechercher des produits
	</div>
  {{/if}}
  <tr>
    <td>
      <!-- Affichage du nom du chapitre et de son code -->
			{{if $chapitreATC}}
			<table class="tbl">
			  <tr>
			    <td style="width: 20px; text-align: center">
			      <a href="#" onclick="loadLivretArbreATC('{{$codeNiveauSup}}')">
			        <img src="./images/icons/uparrow.png" alt="Retour" title="Retour" />
            </a>
          </td> 
			    <th>{{$chapitreATC}} ({{$codeATC}})</th>
			  </tr>
			</table>
      {{/if}}
			<!-- Affichage de l'arborescence -->
			<table class="tbl">
			  {{foreach from=$arbreATC item=elementATC}}
			  <tr>
			    <td>{{$elementATC->Code}}</td>
			    <td class="text"><a href="#" onclick="loadLivretArbreATC('{{$elementATC->Code}}')">{{$elementATC->Libelle}}</a></td>
			  </tr>
			  {{/foreach}}
			</table>
    </td>
  </tr>
</table>

{{if $codeATC}}
<table class="tbl">
  <tr>
    <th colspan="10">{{$listProduits|@count}} produits dans le livret</th>
  </tr>
  
  <tr>
    <th>Actions</th>
    <th>Libelle</th>
    <th>Code CIP</th>
    <th>Code UCD</th>
    <th>Prix Hôpital</th>
    <th>Prix Ville</th>
    <th>Date Prix Hôpital</th>
    <th>Date Prix Ville</th>
    <th>Code Interne</th>
    <th>Commentaire</th>
  </tr>
  {{foreach from=$listProduits item=produit_livret}}
  <tr>
    <td>
      <button type="button" class="trash notext" onclick="Livret.delProduit('{{$produit_livret->_id}}','','{{$codeATC}}')">
        {{tr}}Delete{{/tr}}
      </button>
      <button type="button" class="edit notext" onclick="Livret.editProduit('{{$produit_livret->_id}}','','{{$codeATC}}')">
        {{tr}}Modify{{/tr}} 
      </button>
    </td>  
    <td>
      <a href="#produit{{$produit_livret->_id}}" onclick="viewProduit({{$produit_livret->code_cip}})">
        {{$produit_livret->libelle}}
      </a>
    </td>
    <td>
      {{$produit_livret->code_cip}}
    </td>
    <td>
      {{$produit_livret->_ref_produit->code_ucd}}
    </td>
    <td>
      {{if $produit_livret->prix_hopital}}
        {{$produit_livret->prix_hopital}}&euro;
      {{/if}}
    </td>
    <td>
      {{if $produit_livret->prix_ville}}
        {{$produit_livret->prix_ville}}&euro;
      {{/if}}
    </td>
    <td>
      {{$produit_livret->date_prix_hopital|date_format:"%d/%m/%Y"}}
    </td>
    <td>
      {{$produit_livret->date_prix_ville|date_format:"%d/%m/%Y"}}
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
{{/if}}