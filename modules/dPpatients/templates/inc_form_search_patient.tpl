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
  Main.add(function() {
    var oform = getForm('find');
    oform.onsubmit();
    $V(oform.elements['new'], 1);
  });
</script>

<form name="find" action="?" method="get" onsubmit="return onSubmitFormAjax(this, null, 'search_result_patient');">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="a" value="ajax_search_patient" />
  <input type="hidden" name="new" value="0" />
  <input type="hidden" name="board" value="{{$board}}" />
  <input type="hidden" id="useVitale" name="useVitale" value="{{$useVitale}}" />

  <table class="form">
    <tr>
      <th class="title" colspan="4">Recherche d'un dossier patient</th>
    </tr>

    <tr>
      <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
      <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
      <td class="field_basic" colspan="2">
        <button type="button" style="float: right;" class="search" title="{{tr}}CPatient.other_fields{{/tr}}"
                onclick="toggleSearch();">{{tr}}CPatient.other_fields{{/tr}}</button>
      </td>
      <th style="display: none;" class="field_advanced"><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
      <td style="display: none;" class="field_advanced"><input tabindex="6" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
    </tr>

    <tr>
      <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
      <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
      <td class="field_basic" colspan="2"></td>
      <th style="display: none;" class="field_advanced"><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
      <td style="display: none;" class="field_advanced"><input tabindex="7" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
    </tr>

    <tr>
      <th>
        <label for="Date_Day" title="Date de naissance du patient à rechercher">
          Date de naissance
        </label>
      </th>
      <td>
        {{mb_include module=patients template=inc_select_date date=$naissance tabindex=3}}
      </td>

      {{if $conf.dPpatients.CPatient.tag_ipp && $dPsanteInstalled}}
        <td colspan="2" class="field_basic"></td>
        <th style="display: none;" class="field_advanced">IPP</th>
        <td style="display: none;" class="field_advanced">
          <input tabindex="8" type="text" name="patient_ipp" value="{{$patient_ipp}}" />
        </td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
    </tr>

    {{if "covercard"|module_active}}
      <input type="hidden" name="covercard" value="{{$covercard}}"/>
    {{else}}
      <input type="hidden" name="covercard" value=""/>
    {{/if}}

    <tr>
      <th class="field_advanced" style="display: none;">
        {{mb_label class=CPatient field=sexe}}
      </th>
      <td class="field_advanced" style="display: none;">
        <select name="sexe">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          <option value="m" {{if $sexe == "m"}}selected{{/if}}>
            {{tr}}CPatient.sexe.m{{/tr}}
          </option>
          <option value="f" {{if $sexe == "f"}}selected{{/if}}>
            {{tr}}CPatient.sexe.f{{/tr}}
          </option>
        </select>
      </td>

      <td class="field_advanced" colspan="2"></td>
      <th style="display: none;" class="field_advanced">
        <label for="prat" title="Praticien concerné">
          Praticien
        </label>
      </th>
      <td colspan="3" class="field_advanced text" style="display: none;">
        <div class="small-info" id="prat_id_message">
          Afin de pouvoir faire une recherche par praticien, veuillez spécifier un trait du patient.
        </div>
        <select name="prat_id" tabindex="5" style="width: 13em; display: none;">
          <option value="">&mdash; Choisir un praticien</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$prat_id}}
        </select>
      </td>
    </tr>

    <tr>
      {{if $conf.dPplanningOp.CSejour.tag_dossier && $dPsanteInstalled}}
        <th style="display: none;" class="field_advanced">NDA</th>
        <td style="display: none;" class="field_advanced">
          <input tabindex="8" type="text" name="patient_nda" value="{{$patient_nda}}" />
        </td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
      <td colspan="2"></td>
    </tr>

    <tr>
      <td class="button" colspan="4">
        <button type="button" class="erase" onclick="emptyForm();$('vw_idx_patient_button_create').hide();"
                title="Vider les champs du formulaire">
          {{tr}}Empty{{/tr}}
        </button>
        <button id="ins_list_patient_button_search" class="search" tabindex="10"
                type="submit" onclick="$('useVitale').value = 0;">
          {{tr}}Search{{/tr}}
        </button>

        {{if $app->user_prefs.LogicielLectureVitale == 'vitaleVision'}}
          <button class="search singleclick" type="button" tabindex="11" onclick="VitaleVision.read();">
            Lire Vitale
          </button>
        {{elseif $app->user_prefs.LogicielLectureVitale == 'mbHost'}}
          {{mb_include module=mbHost template=inc_vitale operation='search'}}
        {{elseif $modFSE && $modFSE->canRead()}}
          {{mb_include module=fse template=inc_button_vitale}}
        {{/if}}

        {{if $can->edit}}
          <button id="vw_idx_patient_button_create" class="new" type="button" tabindex="15" onclick="Patient.createModal(this.form, null, function() {getForm('find').onsubmit()});" style="display:none;">
            {{tr}}Create{{/tr}}
            {{if $useVitale}}avec Vitale{{/if}}
            {{if $useCoverCard}}avec Covercard{{/if}}
          </button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>