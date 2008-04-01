<li>
  <strong>{{$med->_ref_produit->libelle}}</strong>:
  <ul>
    <li>
  <!-- Affichage des prises s'il y en a -->
  {{foreach from=$med->_ref_prises item=prise}}
    {{if $prise->quantite}}
    {{$prise->_view}}, 
    {{/if}}
  {{foreachelse}}
  <!-- Sinon, affichage de la posologie selectionnée -->
    {{$med->_ref_posologie->_view}}
  {{/foreach}}
  
  <!-- Duree de la prise --> 
  {{$med->_duree_prise}}
  
  <!-- Commentaire -->
  {{if $med->commentaire}}
  <em>{{$med->commentaire}}</em>
  {{/if}}
  
  <!-- Remarque sur la prise -->
  {{if $med->_specif_prise}}
  ({{$med->_specif_prise}})
  
{{/if}}
</li>
  </ul>
</li>