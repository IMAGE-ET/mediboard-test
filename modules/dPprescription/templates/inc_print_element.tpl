<li>
  {{if $elt->_ref_element_prescription->_ref_category_prescription->chapitre != "dmi"}}Faire pratiquer {{/if}}
  <strong>{{$elt->_ref_element_prescription->_view}}</strong>
  ({{$elt->_ref_element_prescription->_ref_category_prescription->_view}})
  {{if $elt->commentaire}}
  <em>{{$elt->commentaire}}</em>
  {{/if}}
  {{if $elt->_ref_element_prescription->_ref_category_prescription->chapitre != "dmi" && $elt->_ref_prises|@count}}
	  <ul>
	    <li>
			  <!-- Affichage des prises s'il y en a -->
			    {{foreach from=$elt->_ref_prises item=prise name=foreach_prise}}
				    {{if $prise->quantite}}
				        {{$prise->_view}}
				      {{if !$smarty.foreach.foreach_prise.last}},{{/if}} 
				    {{/if}}
				  {{/foreach}}
			    <!-- Duree de la prise --> 
					{{$elt->_duree_prise}}
					{{if $elt->date_arret}}
					  (Element arrêté le {{$elt->date_arret|date_format:"%d/%m/%Y"}}) 
					{{/if}}
			</li>
	  </ul>
  {{/if}}    
</li>