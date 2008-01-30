<ul>
  {{foreach from=$produits item=produit}}
    <li>
      <small style="display: none;">{{$produit->code_cip}}</small>
      <strong>{{$produit->libelle}}</strong>
    </li>
  {{/foreach}}
</ul>