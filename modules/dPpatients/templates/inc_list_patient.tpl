
{{assign var=modFSE value="fse"|module_active}}

{{if !$board}}
  {{if $app->user_prefs.VitaleVision}}
    {{include file="../../dPpatients/templates/inc_vitalevision.tpl" debug=false keepFiles=true}}
  {{elseif $modFSE && $modFSE->canRead()}}
     <script type="text/javascript">
       var urlFSE = new Url;
       urlFSE.setModuleTab("dPpatients", "vw_idx_patients");
       urlFSE.addParam("useVitale", 1);
     </script>
  {{/if}}
  <script type="text/javascript">
    var Patient = {
      create : function(form) {
        var url = new Url;
        url.setModuleTab("dPpatients", "vw_edit_patients");
        url.addParam("patient_id", 0);
        url.addParam("useVitale", $V(form.useVitale));
        url.addParam("name",      $V(form.nom));
        url.addParam("firstName", $V(form.prenom));
        url.addParam("naissance_day",  $V(form.Date_Day));
        url.addParam("naissance_month",$V(form.Date_Month));
        url.addParam("naissance_year", $V(form.Date_Year));
        {{if "covercard"|module_active}}url.addParam("covercard", $V(form.covercard));{{/if}}
        url.redirect();
      },
      search : function(from) {
        $("useVitale").value = 0;
        return true;
      }
    };

    doMerge = function(oForm) {
      var url = new Url();
      url.setModuleAction("system", "object_merger");
      url.addParam("objects_class", "CPatient");
      url.addParam("objects_id", $V(oForm["objects_id[]"]).join("-"));
      url.popup(800, 600, "merge_patients");
    };

    onMergeComplete = function() {
      location.reload();
    };

    window.checkedMerge = [];
    checkOnlyTwoSelected = function(checkbox) {
      checkedMerge = checkedMerge.without(checkbox);

      if (checkbox.checked)
        checkedMerge.push(checkbox);

      if (checkedMerge.length > 2)
        checkedMerge.shift().checked = false;
    };

    doLink = function(oForm) {
      var url = new Url();
      url.addParam("m", "dPpatients");
      url.addParam("dosql", "do_link");
      url.addParam("objects_id", $V(oForm["objects_id[]"]).join("-"));
      url.requestUpdate("systemMsg", {
        method: 'post'
      });
    }
  </script>
{{/if}}

<script type="text/javascript">

reloadPatient = function(patient_id, link){
  {{if $board}}
    var url = new Url('dPpatients', 'vw_full_patients');
    url.addParam("patient_id", patient_id);
    url.redirect();
  {{else}}
    var url = new Url('dPpatients', 'httpreq_vw_patient');
    url.addParam('patient_id', patient_id);
    url.requestUpdate('vwPatient', { onComplete: markAsSelected.curry(link) } );
  {{/if}}
};

toggleSearch = function() {
  $$(".field_advanced").each(function(elt){ elt.toggle();});
  $$(".field_basic").each(function(elt){ elt.toggle();});
};

emptyForm = function() {
  var form = getForm("find");
  $V(form.Date_Day, '');
  $V(form.Date_Month, '');
  $V(form.Date_Year, '');
  $V(form.prat_id, '');
  form.select("input[type=text]").each(function(elt) {
    $V(elt, '');
  });
  form.nom.focus();
};
{{if $cp || $ville || ($conf.dPpatients.CPatient.tag_ipp && $patient_ipp) || $prat_id || $sexe || ($conf.dPplanningOp.CSejour.tag_dossier && $patient_nda) }}
  Main.add(toggleSearch);
{{/if}}
</script>

<div id="modal-beneficiaire" style="display:none; text-align:center;">
  <p id="msg-multiple-benef">
    Cette carte vitale semble contenir plusieurs bénéficiaires, merci de sélectionner la personne voulue :
  </p>
  <p id="msg-confirm-benef" style="display: none;"></p>
  <p id="benef-nom">
    <select id="modal-beneficiaire-select"></select>
    <span></span>
  </p>
  <div>
    <button type="button" class="tick" onclick="VitaleVision.search(getForm('find'), $V($('modal-beneficiaire-select'))); VitaleVision.modalWindow.close();">{{tr}}Choose{{/tr}}</button>
    <button type="button" class="cancel" onclick="VitaleVision.modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
  </div>
</div>

<form name="find" action="?" method="get" {{if $board}}onsubmit="return updateListPatients()"{{/if}}>

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="new" value="1" />
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
    {{*
    <th style="display: none;" class="field_advanced">
      <label for="prat" title="Praticien concerné">
        Praticien
      </label>
    </th>
    <td colspan="3" class="field_advanced" style="display: none;">
      <select name="prat_id" tabindex="5" style="width: 13em;">
        <option value="">&mdash; Choisir un praticien</option>
        {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$prat_id}}
      </select>
    </td>
    *}}
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
      <button type="button" class="cancel" onclick="emptyForm()"
          title="Vider les champs du formulaire">{{tr}}Empty{{/tr}}</button>
      <button class="search" tabindex="10" type="submit" {{if !$board}}onclick="Patient.search(this.form);"{{/if}}>{{tr}}Search{{/tr}}</button>

      {{if !$board}}
        {{if $app->user_prefs.VitaleVision}}
          <button class="search singleclick" type="button" tabindex="11" onclick="VitaleVision.read();">
            Lire Vitale
          </button>
        {{elseif $modFSE && $modFSE->canRead()}}
          {{mb_include module=fse template=inc_button_vitale}}
        {{/if}}

        {{if $can->edit}}
          {{if $nom || $prenom || $patient_ipp || $naissance}}
          <button class="new" type="button" tabindex="15" onclick="Patient.create(this.form);">
            {{tr}}Create{{/tr}}
            {{if $useVitale}}avec Vitale{{/if}}
            {{if $useCoverCard}}avec Covercard{{/if}}
          </button>
          {{/if}}
        {{/if}}
      {{/if}}
    </td>
  </tr>
</table>
</form>

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

<form name="fusion" action="?" method="get" onsubmit="return false;">
  <table class="tbl" id="list_patients">
    <tr>
      {{if (((!$conf.dPpatients.CPatient.merge_only_admin || $can->admin)) && $can->edit) ||
               $conf.dPpatients.CPatient.show_patient_link == 1}}
        <th class="narrow">
        {{if ((!$conf.dPpatients.CPatient.merge_only_admin || $can->admin)) && $can->edit}}
          <button type="button" class="merge notext compact" title="{{tr}}Merge{{/tr}}" onclick="doMerge(this.form);">
            {{tr}}Merge{{/tr}}
          </button>
        {{/if}}
        {{if $conf.dPpatients.CPatient.show_patient_link}}
          <button type="button" class="link notext compact" title="{{tr}}Link{{/tr}}" onclick="doLink(this.form);">
            {{tr}}Link{{/tr}}
          </button>
        {{/if}}
        </th>
      {{/if}}
      <th>{{tr}}CPatient{{/tr}}</th>
      <th class="narrow">{{tr}}CPatient-naissance-court{{/tr}}</th>
      <th>{{tr}}CPatient-adresse{{/tr}}</th>
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