{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line value=$curr_line}}
<table class="tbl {{if $line->traitement_personnel}}traitement{{else}}med{{/if}}
                  {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} line_stopped{{/if}}" 
      {{if !$mode_induction_perop}}id="line_medicament_{{$line->_id}}"{{/if}}>
  <!-- Header de la ligne -->
  <tr  class="hoverable">
    <td style="text-align: center; width: 5%;">
      {{if $line->_can_vw_livret_therapeutique}}
      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
      {{/if}}  
      {{if !$line->_ref_produit->inT2A}}
        <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
      {{/if}}
      {{if $line->_can_vw_hospi}}
      <img src="images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      {{/if}}
      {{if $line->_can_vw_generique}}
      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
      {{/if}}
      {{if $line->_ref_produit->_supprime}}
      <br /><img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
      {{/if}}
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}     
    </td>
    <td style="width: 25%" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="text {{if $line->traitement_personnel}}traitement{{/if}}
               {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}arretee{{/if}}">
      <script type="text/javascript">
        {{if !$line->_protocole && !$mode_induction_perop}}
         Main.add( function(){
             moveTbody($('line_medicament_{{$line->_id}}'));
         });
         {{/if}}
      </script>
      {{if $line->_ref_parent_line->_id}}
        {{assign var=parent_line value=$line->_ref_parent_line}}
        <img style="float: right" src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique" 
             class="tooltip-trigger" 
             onmouseover="ObjectTooltip.createEx(this, '{{$parent_line->_guid}}')"/>
      {{/if}}
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit({{$line->_ref_produit->code_cip}})" style="font-weight: bold;">
        {{$line->_ucd_view}}
      </a>
      {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
      {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
      {{if $line->traitement_personnel}}Traitement personnel&nbsp;{{/if}}
    </td>
    <td class="text" style="width: 20%" >
        <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
        {{if $line->_can_view_signature_praticien}}
					{{if @$modules.messagerie}}
					<a class="action" href="#nothing" onclick="MbMail.create({{$line->_ref_praticien->_id}}, '{{$line->_view}}')">
					  <img src="images/icons/mbmail.png" alt="message" title="Envoyer un message" />
					</a>
					{{/if}}
					{{if $line->signee}}
					 <img src="images/icons/tick.png" alt="Ligne signée par le praticien" title="Ligne signée par le praticien" />
					{{else}}
						 <img src="images/icons/cross.png" alt="Ligne non signée par le praticien"title="Ligne non signée par le praticien" />
					{{/if}}
          {{if $prescription_reelle->type != "externe"}}
	          {{if $line->valide_pharma}}
					    <img src="images/icons/signature_pharma.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
					  {{else}}
						  <img src="images/icons/signature_pharma_barre.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
				  	{{/if}}
			  	{{/if}}
			  	{{$line->_ref_praticien->_view}}
        {{else if !$line->traitement_personnel && !$line->_protocole}}
          {{$line->_ref_praticien->_view}}
        {{/if}}
       </div>
    </td>
    <td style="width: 15%">
      <!-- Date de debut -->
      {{if $line->debut}}
        {{mb_value object=$line field=debut}}
        {{if $line->time_debut}}
          à {{mb_value object=$line field=time_debut}}
        {{/if}}
      {{/if}}
    </td>
    <td style="width: 10%">
      <!-- Duree de la ligne -->
      {{if $line->duree && $line->unite_duree}}
        {{mb_value object=$line field=duree}}  
        {{mb_value object=$line field=unite_duree}}
      {{/if}}
    </td>

    <td style="width: 25%;" class="text">
    {{if !$mode_induction_perop}}   
      <button style="float: right;" class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, true,'{{$line->_guid}}');"></button>
    {{/if}}
	  
	    {{if $line->_ref_prises|@count}}
	      {{foreach from=$line->_ref_prises item=_prise name=prises}}
	        {{$_prise->_view}} {{if !$smarty.foreach.prises.last}}, {{/if}}
	      {{/foreach}}
	    {{else}}
	      Aucune posologie
	    {{/if}}
	    
 
    </td>
  </tr>
</table>