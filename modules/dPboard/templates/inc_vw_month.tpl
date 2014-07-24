{{*
 * $Id$
 *  
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<style>
  .modal_month {
    width:600px;
    cursor: pointer;
  }
</style>

{{mb_include module=system template=calendars/vw_month calendar=$calendar}}

<script>
  modal_resume = function(stype, event) {
    var sid = event.get('id');

    if (stype == 'CPlageOp') {
      var url = new Url("bloc", "ajax_vw_plageop");
      url.addParam('plage_id', sid);
      url.requestModal();
    }

    if (stype == 'CPlageconsult') {
      var url = new Url("cabinet", "ajax_vw_plage_consult");
      url.addParam('plage_id', sid);
      url.requestModal();
    }

    if (stype == 'CIntervHorsPlage') {
      var sdate = event.get('date');
      EditPlanning.showAlerte(sdate, '', 'day');
    }
  };

  $$('.event').each(function(event) {
    event.onclick = function() {
      var stype = event.get('type');
      if (stype) {
        modal_resume(stype, event);
      }
    };
  });
</script>

{{* plages consult *}}
{{foreach from=$plages_consult item=_pcons}}
  <div id="{{$_pcons->_guid}}_view" style="display: none;" class="modal_month">
    <table class="tbl">
      <tr>
        <th colspan="4" class="title" style="border-left: solid 4px #{{$_pcons->color}}">{{$_pcons}}</th>
      </tr>
      <tr>
        <th class="narrow">Praticien</th>
        <td>
          {{if $_pcons->chir_id}}
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_pcons->_ref_chir}}
          {{else}}
            &mdash;
          {{/if}}
        </td>
        <th class="narrow">Fréquence</th>
        <td>{{$_pcons->freq}}</td>
      </tr>
      <tr>
        <th>horaires</th>
        <td colspan="3">{{$_pcons->debut}} &rarr; {{$_pcons->fin}}</td>
      </tr>
    </table>
    <table class="tbl" style="max-height: 400px; overflow-y: auto;">
      <tr>
        <th colspan="4" class="title">{{tr}}CConsultation{{/tr}}s</th>
      </tr>
      <tr>
        <th class="narrow">Date</th>
        <th>Patient</th>
        <th>Libellé</th>
        <th class="narrow">Durée</th>
      </tr>
      {{foreach from=$_pcons->_ref_consultations item=_cons}}
        <tr>
          <td>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_cons->_guid}}');">{{$_cons->heure|date_format:$conf.time}}</span>
          </td>
          <td>
            {{if $_cons->patient_id}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_cons->_ref_patient->_guid}}');">{{$_cons->_ref_patient}}</span>
            {{else}}
              [PAUSE]
            {{/if}}
          </td>
          <td class="text compact">{{$_cons->motif}}</td>
          <td>{{math equation="a*b" a=$_pcons->_freq_minutes b=$_cons->duree}}min</td>
        </tr>
        {{foreachelse}}
        <tr>
          <td class="empty" colspan="5">{{tr}}CConsultation.none{{/tr}}</td>
        </tr>
      {{/foreach}}
    </table>
  </div>
{{/foreach}}