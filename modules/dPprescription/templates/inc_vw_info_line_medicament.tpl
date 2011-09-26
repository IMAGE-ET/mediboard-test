{{if $line->_can_vw_livret_therapeutique}}
  <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non présent dans le livret Thérapeutique" />
{{/if}}  
{{if $line->stupefiant}}
  <img src="images/icons/stup.png" title="Produit stupéfiant" />
{{/if}}
{{if !$line->_ref_produit->inT2A}}
  <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
{{/if}}
{{if $line->_can_vw_hospi}}
  <img src="images/icons/hopital.gif" title="Produit Hospitalier" />
{{/if}}
{{if $line->_can_vw_generique}}
  <img src="images/icons/generiques.gif" title="Produit générique" />
{{/if}}
{{if $line->_ref_produit->_supprime}}
  <img src="images/icons/medicament_barre.gif" title="Produit supprimé" />
{{/if}}

{{if $line->countBackRefs("prev_hist_line")}}
	<img src="images/icons/subst.png" title="Substitution" />
{{/if}}

{{if $line->date_arret && $line->time_arret}}      
  <img src="style/mediboard/images/buttons/stop.png" title="{{tr}}CPrescriptionLineElement-date_arret{{/tr}} : {{$line->date_arret|date_format:$conf.date}} {{$line->time_arret|date_format:$conf.time}}"/>
{{/if}}