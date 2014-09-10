{{*
 * $Id$
 *
 * @category DPPatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=identitovigilence value=$conf.dPpatients.CPatient.identitovigilence}}
{{assign var=identity_status value="CAppUI::conf"|static_call:"dPpatients CPatient manage_identity_status":"CGroups-$g"}}

<script>
  SiblingsChecker.running = false;
  SiblingsChecker.submit  = 0;
  $V(SiblingsChecker.form._reason_state, "");
  {{if $identitovigilence == "doublons" && !$identity_status && $doubloon}}
    $("submit-patient").disabled = true;
  {{else}}
    $("submit-patient").disabled = false;
  {{/if}}
  {{if $submit && !$doubloon && !$siblings && $similar}}
    Main.add(function() {
      SiblingsChecker.confirmCreate();
    });
  {{/if}}
</script>

{{if !$similar}}
  <div class="small-warning">
    Le nom et/ou le prénom sont très différents de {{$old_patient->_view}}<br/>
  </div>
{{/if}}

{{if $doubloon}}
  <div class="small-error">
    Un doublon <span onmouseover="ObjectTooltip.createEx(this, '{{$patient_match->_guid}}')">{{$patient_match->_view}}</span>
    a été détecté.
    <br/>
    {{if $identitovigilence == "doublons" && !$identity_status}}
      Vous ne pouvez pas sauvegarder le patient.
    {{elseif $submit}}
      Voulez-vous tout de même sauvegarder ?
    {{/if}}
  </div>
  <input type="hidden" name="_doubloon_ids" value="{{$doubloon}}">
{{/if}}

{{if $siblings}}
  <div class="small-warning">
    Risque de doublons :
    <ul>
      {{foreach from=$siblings item=_sibling}}
        <li>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sibling->_guid}}')">
            {{$_sibling->nom}} {{if $_sibling->nom_jeune_fille}}({{$_sibling->nom_jeune_fille}}){{/if}} {{$_sibling->prenom}}
          </span>
          né(e) le {{$_sibling->naissance}}<br/>
          {{if $_sibling->cp || $_sibling->ville || $_sibling->adresse}}
            <span class="compact" style="white-space: normal">
              <span style="white-space: nowrap">
                {{$_sibling->cp}} {{$_sibling->ville}}
              </span>
              <span style="white-space: nowrap">{{$_sibling->adresse|spancate:50}}</span>
            </span>
          {{/if}}
        </li>
      {{/foreach}}
    </ul>
  </div>
{{/if}}

{{if $submit && $doubloon && $identity_status}}
  <label>Raison de la création du doublon :
    <textarea name="doubloon_reason" onchange="$V(SiblingsChecker.form._reason_state, this.value)"></textarea>
  </label>
{{/if}}

{{if $submit && ($doubloon || $siblings || !$similar)}}
  <div style="text-align: center">
    <button type="button" class="tick" onclick="SiblingsChecker.confirmCreate()">{{tr}}Confirm{{/tr}}</button>
    <button type="button" class="cancel" onclick="Control.Modal.close()">{{tr}}Cancel{{/tr}}</button>
  </div>
{{/if}}