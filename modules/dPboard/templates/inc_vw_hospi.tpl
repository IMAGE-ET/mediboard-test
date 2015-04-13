{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="10">Hospitalisations</th>
  </tr>
  <tr>
    <th>{{mb_title class=CAffectation field=lit_id}}</th>
    <th>{{mb_title class=CSejour      field=entree}}</th>
    <th>{{mb_title class=CSejour      field=sortie}}</th>
    <th>{{mb_title class=CSejour      field=patient_id}}</th>
    <th>{{mb_title class=CSejour      field=libelle}}</th>
    {{if $praticien->_is_anesth}}
      <th>{{mb_title class=CSejour    field=praticien_id}}</th>
    {{/if}}
  </tr>
  {{foreach from=$listSejours item=curr_sejour}}
  <tr>
    <td class="text {{if $curr_sejour->sortie_reelle}}hatching{{/if}}">
      {{if $curr_sejour->_ref_curr_affectation->_id}}
        {{if $curr_sejour->_ref_curr_affectation->_ref_lit && $curr_sejour->_ref_curr_affectation->_ref_lit->_id}}
          {{$curr_sejour->_ref_curr_affectation->_ref_lit}}
        {{else}}
          {{$curr_sejour->_ref_curr_affectation->_ref_service}}
        {{/if}}
      {{else}}
        Non placé
      {{/if}}
    </td>
    <td
      {{if $date == $curr_sejour->entree|iso_date}}style="background-color: #afa"{{/if}}>
      {{$curr_sejour->entree|date_format:"%d/%m %Hh%M"}}
    </td>
    
    <td {{if $date == $curr_sejour->sortie|iso_date}}style="background-color: #afa"{{/if}}>
      {{$curr_sejour->sortie|date_format:"%d/%m %Hh%M"}}
      {{if $curr_sejour->confirme}}
        <span title="Sortie autorisée">
           <img src="images/icons/tick.png" alt="Sortie autorisée"/>
        </span>
      {{/if}}
    </td>
    <td class="text">
      <a href="{{$curr_sejour->_ref_patient->_dossier_cabinet_url}}">
      	 <strong class="{{if !$curr_sejour->entree_reelle}}patient-not-arrived{{/if}}"
      	   onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_ref_patient->_guid}}');">
      	   {{$curr_sejour->_ref_patient}}
      	 </strong>
      </a>
    </td>
    <td class="text">
      <a href="#1"
        onclick="showDossierSoins('{{$curr_sejour->_id}}', '', 'suivi_clinique'); return false;">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}');">
          {{$curr_sejour->_motif_complet}}
        </span>
      </a>
    </td>
    {{if $praticien->_is_anesth}}
      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_sejour->_ref_praticien}}
      </td>
    {{/if}}
  </tr>
  {{/foreach}}        
</table>
