{{*
 * $Id$
 *  
 * @category 
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  function openResume(patient_id) {
    var fiche = $('resume_'+patient_id);
    if (fiche) {
      Modal.open(fiche, {showClose: true, width:600});
    }
  }

  function openPlage(plage_id) {
    var plage = $('plage_'+plage_id);
    if (plage) {
      Modal.open(plage, {showClose: true, width:600});
    }
  }
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="6">{{tr}}CConsultation{{/tr}}s du {{$date|date_format:$conf.longdate}}</th>
  </tr>
  <tr>
    <th style="width:100px;">Début</th>
    <th style="width:100px;">Fin</th>
    <th style="width:200px;">Plages de consultations</th>
    <th>Motifs</th>
    <th>Taux d'occupation</th>
    <th>Consultations</th>
  </tr>
  {{foreach from=$consultations item=_plage_consultation}}
    {{assign var=nbConsult value=$_plage_consultation->_ref_consultations|@count}}
    <tr>
      <td>{{$_plage_consultation->debut|date_format:"%H:%M"}}</td>
      <td>{{$_plage_consultation->fin|date_format:"%H:%M"}}</td>
      <td>{{$_plage_consultation->_ref_chir}}</td>
      <td>{{$_plage_consultation->libelle}}</td>
      <td>
        <div class="progressBar">
          <div class="bar" style="width: {{$_plage_consultation->_fill_rate}}%; background: #abe;" ><div class="text" style="color: black; text-shadow: 1px 1px 2px white;">{{$_plage_consultation->_fill_rate}}%</div></div>
        </div>
      </td>
      <td {{if !$nbConsult}}class="empty"{{/if}}>
        {{if $nbConsult}}
          <button class="pagelayout button" onclick="openPlage('{{$_plage_consultation->_id}}')">Voir la liste</button>
          <table class="tbl" id="plage_{{$_plage_consultation->_id}}" style="display: none;">
            <tr>
              <th rowspan="{{$nbConsult+1}}">{{$nbConsult}} {{tr}}CConsultation{{/tr}}{{if $nbConsult>1}}s{{/if}}</th>
              <th>Entrée</th>
              <th>Patient</th>
              <th>Age</th>
              <th>Remarques</th>
            </tr>
            {{foreach from=$_plage_consultation->_ref_consultations item=_consultation}}
              <tr>
                <td>{{$_consultation->heure|date_format:"%H:%M"}}</td>
                <td><a href="#" onclick="openResume('{{$_consultation->_ref_patient->_id}}')">{{$_consultation->_ref_patient}}</a> </td>
                <td>{{mb_value object=$_consultation->_ref_patient field=_age}}</td>
                <td>{{$_consultation->rques}}</td>
              </tr>
            {{/foreach}}
          </table>
        {{else}}
          {{tr}}CConsultation.none{{/tr}}
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
</table>


<!-- dossiers patients résumés -->
{{foreach from=$resumes_patient key=k item=_patient}}
  <div id="resume_{{$k}}" style="display: none;">{{$_patient|smarty:nodefaults}}</div>
{{/foreach}}
