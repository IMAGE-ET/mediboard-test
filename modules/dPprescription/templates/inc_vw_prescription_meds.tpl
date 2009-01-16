{{if $prescription->_id}}
	<table class="tbl">
	  <tr>
	    <th style="width: 5%;">Alertes</th>
	    <th style="width: 25%;">Médicament</th>
	    <th style="width: 20%;">Praticien</th>
	    <th style="width: 15%;">Début</th>
	    <th style="width: 10%;">Durée</th>
	    <th style="width: 25%;">Posologie</th>
	  </tr>
	  {{foreach from=$prescription->_ref_prescription_lines item=curr_line}}
	    {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" prescription_reelle=$prescription mode_induction_perop=true}}
    {{/foreach}}
    
    {{foreach from=$prescription_traitement->_ref_prescription_lines item=curr_line_tp}}
	    {{include file="../../dPprescription/templates/inc_vw_line_medicament_lite.tpl" 
	    					prescription_reelle=$prescription 
	    					mode_induction_perop=true
	    					curr_line=$curr_line_tp}}
    {{/foreach}}
    
{{else}}
  <div class="small-info">
  Aucune prescription de séjour
  </div>
{{/if}}