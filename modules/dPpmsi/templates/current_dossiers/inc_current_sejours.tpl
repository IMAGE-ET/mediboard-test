{{*
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org
 *}}

<!-- Onglet dossiers en cours volet Séjour -->

{{mb_include module=system template=inc_pagination total=$count current=$page change_page="PMSI.changePageHospi" step=$step}}
<table class="tbl">
  <tr>
    <th class="title" colspan="9">
      Liste des {{$count}} personne(s) hospitalisée(s) au {{$date|date_format:$conf.longdate}}
    </th>
  </tr>
  <tr>
    <th>{{mb_title class=CTraitementDossier field=traitement}}</th>
    <th>{{mb_title class=CTraitementDossier field=validate}}</th>
    <th>{{mb_title class=CSejour field=facture}}</th>
    <th>{{mb_title class=CSejour field=_NDA}}</th>
    <th>{{mb_label class=CSejour field=praticien_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
    <th>
      {{mb_title class=CSejour field=_entree}}
      {{mb_title class=CSejour field=_sortie}}
    </th>
    <th>DP</th>
    <th>Actes</th>
  </tr>
  {{foreach from=$listSejours item=_sejour}}
    <tr>
      <td class="text {{if $_sejour->_count_actes < 1}}empty{{/if}}">
        {{if $_sejour->_ref_traitement_dossier->traitement}}
          <img src="images/icons/tick.png" alt="Dossier traité par le PMSI" />
        {{else}}
          <img src="images/icons/cross.png" alt="Dossier non traité par le PMSI" />
        {{/if}}
      </td>

      <td class="text {{if $_sejour->_count_actes < 1}}empty{{/if}}">
        {{if $_sejour->_ref_traitement_dossier->validate}}
          <span>
            <img src="images/icons/tick.png" alt="Dossier validé par le PMSI" />
          </span>
        {{else}}
          <span>
            <img src="images/icons/cross.png" alt="Dossier non validé par le PMSI" />
          </span>
        {{/if}}
      </td>

      <td {{if !$_sejour->facture}}class="empty"{{/if}}>
        {{if $_sejour->facture}}
          <img src="images/icons/tick.png" alt="ok" />
        {{else}}
          <img src="images/icons/cross.png" alt="alerte" />
        {{/if}}
      </td>
      <td class="text">
        <strong onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$_sejour}}
        </strong>
      </td>

      <td class="text">
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
      </td>

      <td class="text">
        {{assign var=patient value=$_sejour->_ref_patient}}
        <a href="?m=dPpmsi&amp;tab=vw_dossier_pmsi&amp;patient_id={{$patient->_id}}&amp;sejour_id={{$_sejour->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{mb_include module=patients template=inc_vw_ipp ipp=$patient->_IPP}}
          {{$patient}}
        </span>
        </a>
      </td>

      <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
    	  {{mb_include module=system template=inc_interval_datetime from=$_sejour->_entree to=$_sejour->_sortie}}
    	</span>
      </td>

      <td class="text {{if !$_sejour->DP}}empty{{/if}}">
        {{if !$_sejour->DP}}
          <img src="images/icons/cross.png" alt="alerte" /> Aucun DP
        {{else}}
          <img src="images/icons/tick.png" alt="ok" /> {{$_sejour->DP}}
        {{/if}}
      </td>

      <td class="text {{if $_sejour->_count_actes < 1}}empty{{/if}}">
        {{if $_sejour->_count_actes > 0}}
          <img src="images/icons/tick.png" alt="{{$_sejour->_count_actes}} actes sur le séjour" />
          {{$_sejour->_count_actes}} actes
        {{else}}
          <img src="images/icons/cross.png" alt="Aucun acte sur le séjour" />
          Aucun acte
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>