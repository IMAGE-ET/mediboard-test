<ul>
  {{foreach from=$produits item=produit}}
    <li>
      <small style="display: none;">{{$produit->CodeCIP}}</small>
			{{if $search_libelle_long}}
			  {{$produit->LibelleLong|replace:$needle:"<strong>$needle</strong>"}}
			{{else}}
			  {{$produit->Libelle|replace:$needle:"<strong>$needle</strong>"}}
			{{/if}}
			{{if $produit->Commentaire}}
				<br />
			  ({{$produit->Commentaire|replace:$needle:"<strong>$needle</strong>"}})
			{{/if}}
    </li>
  {{/foreach}}
</ul>