{{*
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}
<script>
  emptyPat = function() {
    $('vwPatient').update('<div class="small-info">Veuillez sélectionner un patient sur la gauche pour pouvoir le visualiser</div>');
  };
</script>

{{if $conf.dPpatients.CPatient.limit_char_search && ($nom != $nom_search || $prenom != $prenom_search)}}
  <div class="small-info">
    La recherche est volontairement limitée aux {{$conf.dPpatients.CPatient.limit_char_search}} premiers caractères
    <ul>
      {{if $nom != $nom_search}}
        <li>pour le <strong>nom</strong> : '{{$nom_search}}'</li>
      {{/if}}
      {{if $prenom != $prenom_search}}
        <li>pour le <strong>prénom</strong> : '{{$prenom_search}}'</li>
      {{/if}}
    </ul>
  </div>
{{/if}}

{{assign var=merge_only_admin value="CAppUI::conf"|static_call:"dPpatients identitovigilance merge_only_admin":"CGroups-$g"}}
{{assign var=show_patient_link value="CAppUI::conf"|static_call:"dPpatients identitovigilance show_patient_link":"CGroups-$g"}}

<form name="fusion" action="?" method="get" onsubmit="return false;">
  <table class="tbl" id="list_patients">
    <tr>
      {{if (((!$merge_only_admin || $can->admin)) && $can->edit) || $show_patient_link}}
        <th class="narrow">
          {{if ((!$merge_only_admin || $can->admin)) && $can->edit}}
            <button type="button" class="merge notext compact" title="{{tr}}Merge{{/tr}}" onclick="doMerge(this.form);">
              {{tr}}Merge{{/tr}}
            </button>
          {{/if}}
          {{if $show_patient_link}}
            <button type="button" class="link notext compact" title="{{tr}}Link{{/tr}}" onclick="Patient.doLink(this.form);">
              {{tr}}Link{{/tr}}
            </button>
          {{/if}}
        </th>
      {{/if}}
      <th id="inc_list_patient_th_patient">{{tr}}CPatient{{/tr}}</th>
      <th class="narrow">{{tr}}CPatient-naissance-court{{/tr}}</th>
      {{if !$board}}
        <th>{{tr}}CPatient-adresse{{/tr}}</th>
      {{/if}}
      <th class="narrow"></th>
    </tr>

    {{mb_ternary var="tabPatient" test=$board
    value="vw_full_patients&patient_id="
    other="vw_idx_patients&patient_id="}}

    <!-- Recherche exacte -->
    <tr>
      <th colspan="5" class="section">
        {{tr}}dPpatients-CPatient-exact-results{{/tr}}
        {{if ($patients|@count >= 30)}}({{tr}}thirty-first-results{{/tr}}){{/if}}
      </th>
    </tr>
    {{foreach from=$patients item=_patient}}
      {{mb_include module=patients template=inc_list_patient_line}}
    {{foreachelse}}
      <tr>
        <td colspan="100" class="empty">{{tr}}dPpatients-CPatient-no-exact-results{{/tr}}</td>
      </tr>
    {{/foreach}}

    <!-- JS -->
    <script>
      {{if $patients|@count == 1 && !$patient->_id && !$board}}
        reloadPatient('{{$_patient->_id}}', 0);
      {{/if}}

      {{if ($patients|@count > 1 || $patients|@count == 0) && !$patient->_id && !$board}}
        emptyPat();
      {{/if}}

      {{if $patient->_id && !$board}}
        reloadPatient('{{$patient->_id}}', 0);
      {{/if}}

      var button_create = $("vw_idx_patient_button_create");
      if (button_create) {
        {{if $nom != '' || $prenom != ''}}
          button_create.show();
        {{else}}
          button_create.hide();
        {{/if}}
      }
    </script>

    {{if $patientsLimited|@count}}
      <tr>
        <th colspan="5" class="section">
          {{tr}}dPpatients-CPatient-limited-results{{/tr}}
          {{if ($patientsLimited|@count >= 30)}}({{tr}}thirty-first-results{{/tr}}){{/if}}
        </th>
      </tr>
    {{/if}}
    {{foreach from=$patientsLimited item=_patient}}
      {{mb_include module=patients template=inc_list_patient_line}}
    {{/foreach}}

    <!-- Recherche phonétique -->
    {{if $patientsSoundex|@count}}
      <tr>
        <th colspan="5" class="section">
          {{tr}}dPpatients-CPatient-close-results{{/tr}}
          {{if ($patientsSoundex|@count >= 30)}}({{tr}}thirty-first-results{{/tr}}){{/if}}
        </th>
      </tr>
    {{/if}}
    {{foreach from=$patientsSoundex item=_patient}}
      {{mb_include module=patients template=inc_list_patient_line}}
    {{/foreach}}
  </table>
</form>