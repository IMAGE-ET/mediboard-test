<script type="text/javascript">

ObjectTooltip.modes.substitutions = {
  module: 'dPprescription',
  action: 'ajax_vw_substitutions',
  sClass: 'tooltip'
};

ObjectTooltip.createSubst = function (element, line_id, can_select_equivalent, mode_protocole, mode_pharma) {
  ObjectTooltip.createEx(element, null, 'substitutions', { 
    line_id : line_id,
		can_select_equivalent: can_select_equivalent,
		mode_protocole: mode_protocole,
		mode_pharma: mode_pharma
  } );
};

</script>

{{if !@$mode_pharma}}
  {{assign var=mode_pharma value=0}}
{{/if}}

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

{{if $line instanceof CPrescriptionLineMixItem}}
  {{if $line->_ref_prescription_line_mix->date_arret && $line->_ref_prescription_line_mix->time_arret}}
    <img src="style/mediboard/images/buttons/stop.png" title="{{tr}}CPrescriptionLineElement-date_arret{{/tr}} : {{$line->_ref_prescription_line_mix->date_arret|date_format:$conf.date}} {{$line->_ref_prescription_line_mix->time_arret|date_format:$conf.time}}"/>
  {{/if}}
{{else}}
  {{if $line->substitute_for_id}}
	  <img src="images/icons/subst.png" onmouseover="ObjectTooltip.createSubst(this, '{{$line->_id}}','{{$line->_can_select_equivalent}}','0','{{$mode_pharma}}');"/>
 {{/if}}
  {{if $line->date_arret && $line->time_arret}}
    <img src="style/mediboard/images/buttons/stop.png" title="{{tr}}CPrescriptionLineElement-date_arret{{/tr}} : {{$line->date_arret|date_format:$conf.date}} {{$line->time_arret|date_format:$conf.time}}"/>
  {{/if}}
{{/if}}