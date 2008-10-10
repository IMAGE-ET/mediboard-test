<li>
  <strong>{{$med->_ucd_view}}</strong>
  <ul>
    {{if $med->_ref_prises|@count || $med->_duree_prise || $med->date_arret || $med->_specif_prise}}
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
		  
		  {{if $med->date_arret}}
		    (Médicament arrêté le {{$med->date_arret|date_format:"%d/%m/%Y"}}) 
		  {{/if}}
		  
		  <!-- Commentaire -->
		  {{if $med->commentaire}}
		  <em>, {{$med->commentaire}}</em>
		  {{/if}}
		  
		  <!-- Remarque sur la prise -->
		  {{if $med->_specif_prise && $med->_ref_prises|@count}}
		    <br />({{$med->_specif_prise}})
		  {{/if}}
    </li>
    {{/if}}
  </ul>
</li>