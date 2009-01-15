<table class="tbl" id="perfusion-{{$_perfusion->_id}}">
<tbody class="hoverable {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}line_stopped{{/if}}">
{{assign var=perfusion_id value=$_perfusion->_id}}
  <tr>
    <th colspan="8" id="th-perf-{{$_perfusion->_id}}" class="text element {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}arretee{{/if}}">
      <!--  Validation infirmiere -->
		  {{if $_perfusion->_can_vw_form_signature_infirmiere}}
        <div style="float: right">
					{{if $_perfusion->validation_infir}}
					  (Validée par l'infirmiere) 
					{{/if}}
				</div>
		  {{/if}}
		  
		  <div style="float: right">
		    {{if $mode_pharma && $_perfusion->signature_pharma}}
		      (Validé par le pharmacien)
		    {{/if}}
        <!-- Siganture du praticien -->
        {{if $_perfusion->_can_vw_signature_praticien}}
          {{$_perfusion->_ref_praticien->_view}}
					{{if $_perfusion->signature_prat}}
					   <img src="images/icons/tick.png" alt="Ligne signée par le praticien" title="Ligne signée par le praticien" /> 
					{{else}}
					   <img src="images/icons/cross.png" alt="Ligne non signée par le praticien"title="Ligne non signée par le praticien" /> 
					{{/if}}
        {{/if}} 
       <button class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, false,'{{$_perfusion->_guid}}');"></button>
      </div>
        
      <strong>
				Perfusion :
				{{foreach from=$_perfusion->_ref_lines item=_line name=perf_line}}
				 {{$_line->_ucd_view}}{{if !$smarty.foreach.perf_line.last}},{{/if}}
				{{/foreach}}         
      </strong>
    </th>
  </tr>
  <tr>
    <td>
      <strong>{{mb_label object=$_perfusion field="type"}}</strong>:
      {{if $_perfusion->type}}
        {{mb_value object=$_perfusion field="type"}}
      {{else}}
        -
      {{/if}}
    </td>
    <td>
      <strong>{{mb_label object=$_perfusion field="vitesse"}}</strong>:
        {{if $_perfusion->vitesse}}
      {{mb_value object=$_perfusion field="vitesse"}} ml/h
      {{else}}
       -
      {{/if}}
    </td>
    <td>
      <strong>{{mb_value object=$_perfusion field="voie"}}</strong>
    </td>
    <td>
      <strong>{{mb_label object=$_perfusion field="date_debut"}}</strong>:
      {{if $_perfusion->date_debut}}
        {{mb_value object=$_perfusion field=date_debut}}
      {{/if}}
      {{if $_perfusion->time_debut}}
	      à 
		    {{mb_value object=$_perfusion field=time_debut}}
	    {{/if}}
	  </td>
    <td>
		  <strong>{{mb_label object=$_perfusion field=duree}}</strong>:
			{{mb_value object=$_perfusion field=duree}}heures
	  </td>	    
  </tr>
  <tr>
    <td colspan="8">
      <table class="form">
	      {{foreach from=$_perfusion->_ref_lines item=line}}
	        <tr>
	          <td style="border: none; width:30%" class="text">
	            {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
	            {{if $line->_can_vw_livret_therapeutique}}
					      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
					    {{/if}}
					    {{if $line->_can_vw_generique}}
					      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
					    {{/if}}
              {{if $line->_ref_produit->_supprime}}
                <img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
              {{/if}}
	            <strong>{{$line->_ucd_view}}</strong>
	          </td>
	          <td style="border: none; width:20%">
	            <strong>{{mb_label object=$line field=quantite}}</strong>:
	            {{mb_value object=$line field=quantite size=4}}
	            {{mb_value object=$line field=unite size=4}}
	          </td>
	          <td class="date" style="border: none; width:20%">
	            <strong>{{mb_label object=$line field=date_debut}}</strong>:
	            {{mb_value object=$line field=date_debut}}
	            {{if $line->time_debut}}
	              à {{mb_value object=$line field=time_debut}} 
	            {{/if}}
	          </td>
	        </tr>
	      {{foreachelse}}
	      	<div class="small-info">
		        Aucun produit n'est associé à la perfusion
		      </div>
	      {{/foreach}}
      </table>
    </td>
  </tr>
</tbody>
</table>