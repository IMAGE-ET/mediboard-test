{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <th>
      <a href="#" onclick="window.print()">
        Admissions {{tr}}CSejour.type.{{$type}}{{/tr}}
        du {{$date|date_format:$conf.longdate}} ({{$total}} admissions)
        {{if $service->_id}}
          &mdash; {{$service->_view}}
        {{/if}}
      </a>
    </th>
  </tr>
  {{foreach from=$listByPrat key=key_prat item=curr_prat}}
  {{assign var="praticien" value=$curr_prat.praticien}}
  <tr>
    <td>
      <strong>
        Dr {{$praticien->_view}} : {{$curr_prat.sejours|@count}} admission(s)
      </strong>
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th colspan="4"><strong>Patient</strong></th>
          <th colspan="7"><strong>Sejour</strong></th>
        </tr>
        <tr>
          <th>Nom / Prenom</th>
          <th>Naissance (Age)</th>
          <th>Sexe</th>
          <th>Remarques</th>
		      <th>Entrée</th>
		      <th>Type</th>
          <th>Dur.</th>
          <th>Conv.</th>
          <th>Chambre</th>
          <th>Prest.</th>
          <th>Remarques</th>
        </tr>
        {{foreach from=$curr_prat.sejours item=curr_sejour}}
        <tr>
          <td>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_ref_patient->_guid}}');">
              {{$curr_sejour->_ref_patient->_view}}
            </span>
          </td>
          <td>
            {{mb_value object=$curr_sejour->_ref_patient field="naissance"}} ({{$curr_sejour->_ref_patient->_age}} ans)
          </td>
          <td>
            {{$curr_sejour->_ref_patient->sexe}}
          </td>
          <td class="text">
            {{$curr_sejour->_ref_patient->rques}}
          </td>
          <td>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_sejour->_guid}}');">
              {{$curr_sejour->entree|date_format:$conf.time}}
            </span>
          </td>
          <td>
            {{if !$curr_sejour->facturable}}
            <strong>NF</strong>
            {{/if}}
            
            {{$curr_sejour->type|truncate:1:""|capitalize}}
          </td>
          <td>{{$curr_sejour->_duree_prevue}} j</td>
          <td class="text">{{$curr_sejour->convalescence|nl2br}}</td>
          <td class="text">
            {{assign var="affectation" value=$curr_sejour->_ref_first_affectation}}
            {{if $affectation->affectation_id}}
              {{$affectation->_ref_lit->_view}}
            {{else}}
              Non placé
            {{/if}}
            ({{tr}}chambre_seule.{{$curr_sejour->chambre_seule}}{{/tr}})
          </td>
          <td class="text">{{$curr_sejour->_ref_prestation->_view}}</td>
          <td class="text">{{$curr_sejour->rques}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{/foreach}}
</table>