<ul>
  {{foreach from=$produits item=produit}}
    <li>
      <small style="display: none;">{{$produit->CodeCIP}}</small>
      <strong>{{$produit->Libelle}}</strong>
    </li>
  {{/foreach}}
</ul>
