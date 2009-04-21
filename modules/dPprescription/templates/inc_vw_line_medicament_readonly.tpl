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
       id="line_medicament_{{$line->_id}}">
<tbody class="hoverable">
  <!-- Header de la ligne -->
  <tr>
    <th colspan="5" id="th_line_CPrescriptionLineMedicament_{{$line->_id}}" 
        class="element {{if $line->traitement_personnel}}traitement{{/if}}
               {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}arretee{{/if}}">
      <script type="text/javascript">
        {{if !$line->_protocole}}
         Main.add( function(){
           moveTbody($('line_medicament_{{$line->_id}}'));
         });
         {{/if}}
      </script>
      <div style="float: left;">
          {{if $line->_ref_parent_line->_id}}
            {{assign var=parent_line value=$line->_ref_parent_line}}
            <img src="images/icons/history.gif" alt="Ligne possédant un historique" title="Ligne possédant un historique" 
                 class="tooltip-trigger" 
                 onmouseover="ObjectTooltip.createEx(this, '{{$parent_line->_guid}}')"/>
          {{/if}}
          {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
          {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
          {{if $line->traitement_personnel}}{{mb_label object=$line field="traitement_personnel"}}&nbsp;{{/if}}
      </div>
      <div style="float: right;">
        <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
        {{if $line->_can_view_signature_praticien}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
          {{if $prescription_reelle->type != "externe"}}
          {{if $line->valide_pharma}}
				    <img src="images/icons/signature_pharma.png" alt="Signée par le pharmacien" title="Signée par le pharmacien" />
				  {{else}}
					  <img src="images/icons/signature_pharma_barre.png" alt="Non signée par le pharmacien" title="Non signée par le pharmacien" />
			  	{{/if}}
			  	{{/if}}
        {{else if !$line->traitement_personnel && !$line->_protocole}}
          {{$line->_ref_praticien->_view}}
        {{/if}}
        <button class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', 'medicament', '', '{{$mode_pharma}}', null, true, false,'{{$line->_guid}}');"></button>
        </div>
      </div>
            
      <a href="#produit{{$line->_id}}" onclick="Prescription.viewProduit({{$line->_ref_produit->code_cip}})" style="font-weight: bold;">
        {{$line->_ucd_view}}
      </a>
    </th>
  </tr>
  <tr>
    <td style="text-align: center; width: 0.1%;">
      {{if $line->_can_vw_livret_therapeutique}}
      <img src="images/icons/livret_therapeutique_barre.gif" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
      <br />
      {{/if}}  
      {{if !$line->_ref_produit->inT2A}}
        <img src="images/icons/T2A_barre.gif" alt="Produit hors T2A" title="Produit hors T2A" />
        <br />
      {{/if}}
      {{if $line->_can_vw_hospi}}
      <img src="images/icons/hopital.gif" alt="Produit Hospitalier" title="Produit Hospitalier" />
      <br />
      {{/if}}
      {{if $line->_can_vw_generique}}
      <img src="images/icons/generiques.gif" alt="Produit générique" title="Produit générique" />
      <br />
      {{/if}}
      {{if $line->_ref_produit->_supprime}}
      <img src="images/icons/medicament_barre.gif" alt="Produit supprimé" title="Produit supprimé" />
      {{/if}}
    </td>
    <td colspan="2">
      <!-- Date d'arret de la ligne -->
      <div style="float: right;">
      {{if $line->date_arret}}
        Arretée le {{mb_value object=$line field=date_arret}}
        {{if $line->time_arret}}
          à {{mb_value object=$line field=time_arret}}
        {{/if}}
      {{else}}
        Aucune date d'arrêt
      {{/if}}
      </div>
      
      <!-- Date de debut -->
      {{if $line->debut}}
        {{mb_label object=$line field=debut}}: 
        {{mb_value object=$line field=debut}}
        {{if $line->time_debut}}
          à {{mb_value object=$line field=time_debut}}
        {{/if}}
        -
      {{/if}}
      
      <!-- Duree de la ligne -->
      {{if $line->duree && $line->unite_duree}}
        {{mb_label object=$line field=duree}}: 
        {{mb_value object=$line field=duree}}  
        {{mb_value object=$line field=unite_duree}}
        -
      {{/if}}
      
      <!-- Date de fin -->
      {{if $line->fin}}
        {{mb_label object=$line field=fin}}: 
        {{mb_value object=$line field=fin}}
        {{if $line->time_fin}}
          à {{mb_value object=$line field=time_fin}}
        {{/if}}
      {{/if}}
      
      <!-- Date de fin prévue -->
      {{if $line->_fin}}
        {{mb_label object=$line field=_fin}}: 
        {{mb_value object=$line field=_fin}}
      {{/if}}
      
      {{if !$line->duree && !$line->debut && !$line->fin}}Aucune date{{/if}}
      {{if $line->commentaire}}, {{mb_value object=$line field="commentaire"}}{{/if}}
    </td>
  </tr>
  <tr>
    <!-- Affichage des alertes -->
    <td style="text-align: left;">
      {{include file="../../dPprescription/templates/line/inc_vw_alertes.tpl"}}
    </td>
    <td style="width: 1%;">Posologie:</td>
    <td>
    {{if $line->_ref_prises|@count}}
      <ul>
      {{foreach from=$line->_ref_prises item=_prise}}
        <li>{{$_prise->_view}}</li>
      {{/foreach}}
      </ul>
    {{else}}
      Aucune posologie
    {{/if}}
    </td>
  </tr>
</tbody>
</table>