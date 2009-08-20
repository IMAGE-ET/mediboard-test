{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line value=$_line_element}}
<table class="tbl elt {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}}line_stopped{{/if}}" id="line_element_{{$line->_id}}">
<tbody class="hoverable">
  <!-- Header de la ligne d'element -->
  <tr>    
    <th id="th_line_CPrescriptionLineElement_{{$line->_id}}" colspan="8"
        class="element {{if $line->_fin_reelle && $line->_fin_reelle < $now && !$line->_protocole}} arretee{{/if}}">
      <script type="text/javascript">
         Main.add( function(){
           moveTbodyElt($('line_element_{{$line->_id}}'),'{{$category->_id}}');
         });
      </script>
      <div style="float: left;">
        {{if $line->conditionnel}}{{mb_label object=$line field="conditionnel"}}&nbsp;{{/if}}
        {{if $line->ald}}{{mb_label object=$line field="ald"}}&nbsp;{{/if}}
      </div>
		
		
      <div class="mediuser" style="float: right; {{if !$line->_protocole}}border-color: #{{$line->_ref_praticien->_ref_function->color}};{{/if}}">
        {{if !$line->_protocole}}
				<!-- Affichage de la signature du praticien -->
        {{if $line->_can_view_signature_praticien}}
          {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
        {{else if !$line->traitement_personnel && !$line->_protocole}}
          {{$line->_ref_praticien->_view}}
        {{/if}}
				{{/if}}
        <button class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', '{{$category->chapitre}}', '', '{{$mode_pharma}}', null, true, false,'{{$line->_guid}}');"></button>
      </div>
		
      {{$line->_ref_element_prescription->_view}}
    </th>
  </tr>
  <tr>
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
      
      <!-- Duree de la ligne -->
      {{if $line->duree}}
       Durée de {{mb_value object=$line field=duree}} {{mb_value object=$line field=unite_duree}} 
      {{/if}}
      
      <!-- Date de debut -->
      {{if $line->debut}}
        {{if $line->duree}} - {{/if}}
        Début: {{mb_value object=$line field=debut}}
        <!-- Heure de debut -->
        {{if $line->time_debut}}
          à {{mb_value object=$line field=time_debut}}
        {{/if}}
      {{/if}}

      
      <!-- Date de fin -->
      {{if $line->fin}}
        Fin: {{mb_value object=$line field=fin}}
        <!-- Heure de fin -->
        {{if $line->time_fin}}
          à {{mb_value object=$line field=time_fin}}
        {{/if}}
      {{/if}}
      
      {{if !$line->duree && !$line->debut && !$line->fin}}
        Aucune date
      {{/if}}
      {{if $line->commentaire}}
        , {{mb_value object=$line field="commentaire"}}
      {{/if}}
      {{if $line->emplacement}}
        ({{mb_value object=$line field="emplacement"}})
      {{/if}}
    </td>
  </tr>
  <tr>
    <td style="width: 1%;">Posologie:</td>
    <td>
      <div style="float: right;"><b>Exécutant</b>: {{if $line->executant_prescription_line_id || $line->user_executant_id}}{{$line->_ref_executant->_view}}{{else}}aucun{{/if}}</div>
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