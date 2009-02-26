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
			    <th rowspan="2">Code CIP</th>
			    <th rowspan="2">Code UCD</th>
			    <th rowspan="2">Libelle produit</th>
			    <th rowspan="2">Laboratoire</th>
			    <th colspan="2">Hôpital</th>
			    <th colspan="2">Ville</th>
			    <th rowspan="2">Code interne</th>
			  </tr>
        <tr>
          <th>Date</th>
          <th>Prix</th>
          <th>Date</th>
          <th>Prix</th>
        </tr>
			{{foreach from=$produits_livret item="produit_livret"}}
			  {{assign var="produit" value=$produit_livret->_ref_produit}}
        {{if $produit->code_cip}}
			  <tr>
			    <td>   
			      {{$produit->code_cip}}
			      {{if $produit->hospitalier}}
			        <img src="./images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
			      {{/if}}
			      {{if !$produit->inT2A}}
			        <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
			      {{/if}}
			      {{if $produit->_generique}}
			        <img src="./images/icons/generiques.gif" alt="Produit Générique" title="Produit Générique" />
			      {{/if}}
			      {{if $produit->_referent}}
			        <img src="./images/icons/referents.gif" alt="Produit Référent" title="Produit Référent" />
			      {{/if}}
			    </td>
			    <td>{{$produit->code_ucd}}</td>
			    <td>{{$produit->libelle}}</td>
			    <td>{{$produit->nom_laboratoire}}</td>
          <td>{{$produit_livret->date_prix_hopital|date_format:"%d/%m/%Y"}}</td>
			    <td>
			      {{if $produit_livret->prix_hopital}}
			        {{$produit_livret->prix_hopital}}&euro;
			      {{/if}}
			    </td>
          <td>{{$produit_livret->date_prix_ville|date_format:"%d/%m/%Y"}}</td>
			    <td>
			      {{if $produit_livret->prix_ville}}
			        {{$produit_livret->prix_ville}}&euro;
			      {{/if}}
			    </td>
			    <td>{{$produit_livret->code_interne}}</td>
			  </tr>
        {{/if}}
			  {{/foreach}}
			</table>
	  </td>
	</tr>
</table>