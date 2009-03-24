<ul>
  {{foreach from=$produits item=produit}}
    <li>
      <small style="display: none;">{{$produit->CodeCIP}}</small>
      <strong>{{if $search_libelle_long}}{{$produit->LibelleLong}}{{else}}{{$produit->Libelle}}{{/if}}</strong>
    </li>
  {{/foreach}}
</ul>
