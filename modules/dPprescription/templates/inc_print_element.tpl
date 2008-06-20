{{assign var=chapitre_category value=$elt->_ref_element_prescription->_ref_category_prescription->chapitre}}

<li>
  <strong>{{$elt->_ref_element_prescription->_view}}</strong>

  {{if $elt->commentaire}}
  <em>({{$elt->commentaire}})</em>
  {{/if}}
  {{if  $chapitre_category != "dmi" && ($elt->_ref_prises|@count || $elt->_duree_prise || $elt->date_arret) }}
	  <ul>
	    <li>
			  <!-- Affichage des prises s'il y en a -->
			    {{foreach from=$elt->_ref_prises item=prise name=foreach_prise}}
				    {{if $prise->quantite}}
				        {{$prise->_view}}
				        {{if !$smarty.foreach.foreach_prise.last}},{{/if}} 
				    {{/if}}
				  {{/foreach}}
				   
				  {{$elt->_duree_prise}}
									
				  <!-- Affichage de la date d'arret -->
					{{if $elt->date_arret}}
					  (Element arrêté le {{$elt->date_arret|date_format:"%d/%m/%Y"}}) 
					{{/if}}
			</li>
	  </ul>
  {{/if}}    
</li>