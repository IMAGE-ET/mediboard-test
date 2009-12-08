{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl" id="perfusion-{{$_perfusion->_id}}">
<tbody class="hoverable {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}line_stopped{{/if}}">
{{assign var=perfusion_id value=$_perfusion->_id}}
  <tr>
    <th colspan="8" id="th-perf-{{$_perfusion->_id}}" class="text element {{if $_perfusion->_fin < $now && !$_perfusion->_protocole}}arretee{{/if}}">
      
		{{if $_perfusion->_ref_parent_line->_id}}
      <div style="float: left">
        {{assign var=parent_perf value=$_perfusion->_ref_parent_line}}
        <img src="images/icons/history.gif" title="Ligne possédant un historique" 
             onmouseover="ObjectTooltip.createEx(this, '{{$parent_perf->_guid}}')"/>
      </div>
      {{/if}}
      
      <div class="mediuser" style="float: right; {{if !$_perfusion->_protocole}}border-color: #{{$_perfusion->_ref_praticien->_ref_function->color}};{{/if}}">	  
        {{if !$_perfusion->_protocole}}
				<!-- Siganture du praticien -->
        {{if $_perfusion->_can_vw_signature_praticien}}
          {{$_perfusion->_ref_praticien->_view}}
					{{if $_perfusion->signature_prat}}
					  <img src="images/icons/tick.png" title="Ligne signée par le praticien" /> 
					{{else}}
					  <img src="images/icons/cross.png" title="Ligne non signée par le praticien" /> 
					{{/if}}
          {{if $prescription_reelle->type != "externe"}}
						{{if $_perfusion->signature_pharma}}
					    <img src="images/icons/signature_pharma.png" title="Signée par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" title="Non signée par le pharmacien" />
				  	{{/if}}
			  	{{/if}}
        {{/if}}
				{{/if}} 
       <button class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, false,'{{$_perfusion->_guid}}');"></button>
      </div>
        
      <strong>
				Perfusion :
				{{foreach from=$_perfusion->_ref_lines item=_line name=perf_line}}
				 {{$_line->_ref_produit->libelle_abrege}}{{if !$smarty.foreach.perf_line.last}},{{/if}}
				{{/foreach}}         
      </strong>
    </th>
  </tr>
  <tr>
    <td>
    	
			     {{if $_perfusion->_can_delete_perfusion}}
					 <form name="editPerf-{{$_perfusion->_id}}" action="" method="post">
				        <input type="hidden" name="m" value="dPprescription" />
				        <input type="hidden" name="dosql" value="do_perfusion_aed" />
				        <input type="hidden" name="perfusion_id" value="{{$_perfusion->_id}}" />
				        <input type="hidden" name="del" value="1" />
								<button type="button" class="trash notext" onclick="return onSubmitFormAjax(this.form, { 
                  onComplete: function(){
                      Prescription.reloadPrescPerf('{{$_perfusion->prescription_id}}','{{$_perfusion->_protocole}}','{{$mode_pharma}}');
                  }        
                } );"></button>
							</form>
              {{/if}}
							
      <strong>{{mb_label object=$_perfusion field="type"}}</strong>:
      {{if $_perfusion->type}}
        {{mb_value object=$_perfusion field="type"}}
      {{else}}
        -
      {{/if}}
    </td>
    <td>
      {{if $_perfusion->vitesse}}
	      <strong>{{mb_label object=$_perfusion field="vitesse"}}</strong>:
	      {{mb_value object=$_perfusion field="vitesse"}} ml/h
      {{elseif $_perfusion->nb_tous_les}}
        <strong>Fréquence</strong>: toutes les {{mb_value object=$_perfusion field="nb_tous_les"}} h
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
  {{if $_perfusion->type == "PCA"}}
    <tr>
      <td>
				<strong>{{mb_label object=$_perfusion field=mode_bolus}}</strong>:
				{{mb_value object=$_perfusion field=mode_bolus}}
      </td>
      {{if $_perfusion->mode_bolus != "sans_bolus"}}
      <td>
				<strong>{{mb_label object=$_perfusion field=dose_bolus}}</strong>:
				{{mb_value object=$_perfusion field=dose_bolus}} mg
      </td>
      <td>
				<strong>{{mb_label object=$_perfusion field=periode_interdite}}</strong>:
				{{mb_value object=$_perfusion field=periode_interdite}} min
      </td>
      {{else}}
      <td colspan="2" />
      {{/if}}
      <td />
      <td />
    </tr>
  {{/if}}
  <tr>
    <td colspan="8">
      <table class="form">
	      {{foreach from=$_perfusion->_ref_lines item=line}}
	        <tr>
	          <td style="border: none; width:30%" class="text">
	            {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
	            {{if $line->_can_vw_livret_therapeutique}}
					      <img src="images/icons/livret_therapeutique_barre.gif" title="Produit non présent dans le livret Thérapeutique" />
					    {{/if}}
					    {{if !$line->_ref_produit->inT2A}}
				        <img src="images/icons/T2A_barre.gif" title="Produit hors T2A" />
				      {{/if}}
					    {{if $line->_can_vw_generique}}
					      <img src="images/icons/generiques.gif" title="Produit générique" />
					    {{/if}}
              {{if $line->_ref_produit->_supprime}}
                <img src="images/icons/medicament_barre.gif" title="Produit supprimé" />
              {{/if}}
		          <strong>{{$line->_ucd_view}}</strong>
		          <span style="font-size:0.8em; opacity:0.7">
		            {{$line->_forme_galenique}}
		          </span>
	          </td>
	          <td style="border: none; width:20%">
	            <strong>{{mb_label object=$line field=quantite}}</strong>:
	            {{mb_value object=$line field=quantite size=4}}
	            {{mb_value object=$line field=unite size=4}}
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