<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Livret Thérapeutique ({{$date|date_format:"%d %b %Y"}})
      </a>
    </th>
  </tr>
  <tr>
    <td>
			<table class="tbl">
			  <tr>
			    <th>Code CIP</th>
			    <th>Code UCD</th>
			    <th>Libelle produit</th>
			    <th>Laboratoire</th>
			    <th>Prix hôpital</th>
			    <th>Prix ville</th>
			    <th>Date prix hôpital</th>
			    <th>Date prix ville</th>
			    <th>Code interne</th>
			  </tr>
			{{foreach from=$produits_livret item="produit_livret"}}
			  {{assign var="produit" value=$produit_livret->_ref_produit}}
			  <tr>
			    <td>   
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
			      {{$produit->libelle}}
			    </td>
			    <td>
			      {{$produit->nom_laboratoire}}
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
			    
			    
			  </tr>
			  {{/foreach}}
			</table>
	  </td>
	</tr>
</table>