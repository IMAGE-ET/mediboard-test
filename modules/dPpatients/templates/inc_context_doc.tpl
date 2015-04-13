{{*
 * $Id$
 *  
 * @category Dossier Patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  applyContext = function(context_guid) {
    Control.Modal.close();
    Control.Modal.close();
    addDocument(context_guid);
  }
</script>

<table class="tbl">
  <tr>
    <th colspan="{{$colspan}}">
      <a class="button undo" style="float: left;" onclick="applyContext('{{$patient->_guid}}')">
        Retour au contexte patient
      </a>
      <h3><strong>Choix du contexte</strong></h3>
    </th>
  </tr>
  <tr>
    {{if $patient->_ref_sejours|@count}}
      <th class="section" style="width: 33%">Séjours</th>
    {{/if}}
    {{if $patient->_ref_operations|@count}}
      <th class="section" style="width: 33%">Interventions</th>
    {{/if}}
    {{if $patient->_ref_consultations|@count}}
      <th class="section">Consultations</th>
    {{/if}}
  </tr>
  <tr>
    {{if $patient->_ref_sejours|@count}}
    <td style="vertical-align: top;">
      {{foreach from=$patient->_ref_sejours item=_sejour}}
        <div>
          <button class="tick notext" onclick="applyContext('{{$_sejour->_guid}}')"></button>
          {{$_sejour}}
          &mdash;
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
        </div>
      {{/foreach}}
    </td>
    {{/if}}
    {{if $patient->_ref_operations|@count}}
    <td style="vertical-align: top;">
      {{foreach from=$patient->_ref_operations item=_operation}}
        <div>
          <button class="tick notext" onclick="applyContext('{{$_operation->_guid}}')"></button>
          {{$_operation}}
          &mdash;
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
        </div>
      {{/foreach}}
    </td>
    {{/if}}
    {{if $patient->_ref_consultations|@count}}
    <td style="vertical-align: top;">
      {{foreach from=$patient->_ref_consultations item=_consult}}
        <div>
          <button class="tick notext" onclick="applyContext('{{$_consult->_guid}}')"></button>
          {{tr}}CConsultation{{/tr}} du {{$_consult->_date|date_format:$conf.date}} à {{$_consult->heure|date_format:$conf.time}}
          &mdash;
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
        </div>
      {{/foreach}}
    </td>
    {{/if}}
  </tr>
</table>