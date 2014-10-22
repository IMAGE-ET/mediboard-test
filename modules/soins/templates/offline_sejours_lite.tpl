{{*
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>

</script>

<style>
  @media print {
    div.modal_view {
      display: block !important;
      height: auto !important;
      width: 100% !important;
      font-size: 8pt !important;
      left: auto !important;
      top: auto !important;
      position: static !important;
    }

    table {
      width: 100% !important;
      font-size: inherit; !important
    }
  }
</style>

{{assign var=print_gemsa value="dPurgences Print gemsa"|conf:"CGroups-$g"}}

<table class="tbl">
  <tr>
    <th class="title" colspan="{{if $service_id == "urgence"}}8{{else}}6{{/if}}">
      <button type="button" class="not-printable print" style="float: right;" onclick="window.print()">{{tr}}Print{{/tr}}</button>
      {{$date|date_format:$conf.date}} - {{if $service->_id}}{{$service}}{{else}}Non placés{{/if}} - {{$patients_offline|@count}} patient(s)
    </th>
  </tr>

  {{if $service_id == "urgence"}}
    {{mb_include module=urgences template=inc_print_header_main_courante}}
  {{else}}
    <tr>
      <th>Patient</th>
      <th>Lit</th>
      <th>Prat.</th>
      <th>Motif</th>
      <th>Durée du séjour</th>
      <th>J. opératoire <br /> Intervention</th>
    </tr>
  {{/if}}

  {{foreach from=$patients_offline item=_patient_data}}
    {{if $service_id == "urgence"}}
      {{assign var=sejour value=$_patient_data.sejour}}
      {{mb_include module=urgences template=inc_print_main_courante offline=0 offline_lite=1}}
    {{else}}
      {{assign var=sejour value=$_patient_data.sejour}}
      {{assign var=curr_aff value=$sejour->_ref_curr_affectation}}
      {{assign var=patient value=$sejour->_ref_patient}}
      {{assign var=curr_prat value=$sejour->_ref_praticien}}
      <tr>
        <td class="text">
          <button type="button" class="search compact notext not-printable" onclick="Modal.open('content_{{$patient->_guid}}', {showClose: true});">Voir le dossier</button>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
            {{$patient}}
          </span>
        </td>
        <td class="text">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_aff->_guid}}')">
            {{$curr_aff}}
          </span>
        </td>
        <td class="text">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_prat}}</td>
        <td class="text">{{mb_value object=$sejour field=libelle}}</td>
        <td class="text">{{$sejour->_duree}} jour(s)</td>
        <td class="text">
          {{foreach from=$sejour->_jour_op item=_jour_op key=op_id name=jour_op}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_jour_op.operation_guid}}')">{{$sejour->_ref_operations.$op_id}} (J{{$_jour_op.jour_op}})</span>
            {{if !$smarty.foreach.jour_op.last}}&mdash;{{/if}}
          {{/foreach}}
        </td>
      </tr>
    {{/if}}
  {{foreachelse}}
    <tr>
      <td colspan="7" class="empty">{{tr}}CSejour.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

{{foreach from=$patients_offline item=_patient_data key=patient_guid}}
  {{assign var=sejour value=$_patient_data.sejour}}
  {{assign var=patient value=$sejour->_ref_patient}}

  <div id="content_{{$patient_guid}}" style="display: none; page-break-before: always;" class="modal_view">

    {{* Plan de soins *}}
    {{$_patient_data.plan_soins|smarty:nodefaults}}

    <hr style="page-break-after: always; border: 0;" />

    {{* Transmissions *}}
    {{if $_patient_data.transmissions|@count}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="9">
            Transmissions - {{$patient}}
          </th>
        </tr>
        {{foreach from=$_patient_data.transmissions item=_suivi}}
          <tr>
            {{mb_include module=hospi template=inc_line_suivi readonly=1}}
          </tr>
        {{/foreach}}
      </table>

      <hr style="page-break-after: always; border: 0;" />
    {{/if}}

    {{* Observations *}}
    {{if $_patient_data.observations|@count || $sejour->_ref_obs_entree->_id}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="7">
            Observations - {{$patient}}
          </th>
        </tr>
        {{if $sejour->_ref_obs_entree->_id}}
          <tr>
            {{mb_include module=hospi template=inc_line_suivi _suivi=$sejour->_ref_obs_entree readonly=1}}
          </tr>
        {{/if}}

        {{foreach from=$_patient_data.observations item=_suivi}}
          <tr>
            {{mb_include module=hospi template=inc_line_suivi readonly=1}}
          </tr>
        {{/foreach}}
      </table>

      <hr style="page-break-after: always; border: 0;" />
    {{/if}}

    {{* Consultations *}}
    {{if $_patient_data.consultations|@count}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="7">
            Consultations - {{$patient}}
          </th>
        </tr>
        {{foreach from=$_patient_data.consultations item=_suivi}}
          <tr>
            {{mb_include module=hospi template=inc_line_suivi readonly=1}}
          </tr>
        {{/foreach}}
      </table>

      <hr style="page-break-after: always; border: 0;" />
    {{/if}}

    {{* Constantes *}}
    {{$_patient_data.constantes|smarty:nodefaults}}
  </div>
{{/foreach}}