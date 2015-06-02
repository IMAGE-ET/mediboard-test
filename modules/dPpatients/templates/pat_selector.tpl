{{* $Id$ *}}

{{if $app->user_prefs.LogicielLectureVitale == 'vitaleVision'}}
    {{mb_include module=dPpatients template=inc_vitalevision debug=false keepFiles=true}}
{{/if}}

{{assign var=modFSE value="fse"|module_active}}
<script type="text/javascript">
var PatientFromSelector = {
  create: function(useVitale) {
    this.edit(0, useVitale);
  },

  edit: function(patient_id, useVitale) {
    var url = new Url("dPpatients", "vw_edit_patients");
    url.addParam("patient_id", patient_id);
    url.addParam("dialog", 1);

    var oForm;
    if (oForm = document.patientSearch) {
      url.addElement(oForm.name);
      url.addElement(oForm.firstName);
      url.addElement(oForm.Date_Day, "naissance_day");
      url.addElement(oForm.Date_Month, "naissance_month");
      url.addElement(oForm.Date_Year, "naissance_year");
    }
    
    if (useVitale || (oForm == document.patientEdit)) {
      url.addParam("useVitale", 1);
    }

    url.redirect();
  },

  selectAndUpdate: function(patient_id) {
    var oForm = document.patientEdit;
    oForm.patient_id.value = patient_id;
    onSubmitFormAjax(oForm);
    PatientFromSelector.select(patient_id, oForm.nom.value);
  },
  
  select: function(patient_id, patient_view, sexe) {
    window.launcher.PatSelector.set(patient_id, patient_view, sexe);
    window._close();
  },

  updateFromVitale: function(patient_id, view, sexe) {
    var url = new Url("patients", "ajax_update_patient_from_vitale");
    url.addParam("patient_id", patient_id);
    url.requestUpdate("systemMsg", this.select.curry(patient_id, view, sexe));
  }
};

{{if $modFSE && $modFSE->canRead() && $app->user_prefs.LogicielLectureVitale == 'none'}}
  var urlFSE = new Url("dPpatients", "pat_selector");
  urlFSE.addParam("useVitale", 1);
  urlFSE.addParam("dialog", 1);
{{/if}}

{{if $patient}}
  Main.add(function () {
    PatientFromSelector.select('{{$patient->_id}}', '{{$patient->_view}}', '{{$patient->sexe}}');
  });
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
    <button type="button" class="tick" onclick="VitaleVision.search(getForm('patientSearch'), $V($('modal-beneficiaire-select'))); VitaleVision.modalWindow.close();">{{tr}}Choose{{/tr}}</button>
    <button type="button" class="cancel" onclick="VitaleVision.modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
  </div>
</div>

<div class="small-info">
  Vous devez faire une recherche avant de créer un patient
</div>

{{if $patVitale}}

<!-- Formulaire de mise à jour Vitale -->
<form name="patientEdit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="dosql" value="do_patients_aed" />
  <input type="hidden" name="_bind_vitale" value="do" />
  {{mb_field object=$patVitale field="patient_id" hidden="true"}}
  
  <table class="form">
    <tr>
      <th class="category" colspan="4">Valeurs SESAM Vitale</th>
    </tr>
    
    <tr>
      <th>{{mb_label object=$patVitale field="nom"}}</th>
      <td>
        {{mb_value object=$patVitale field="nom"}}
        {{mb_field object=$patVitale field="nom" hidden="true"}}
      </td>
      <th rowspan="2">{{mb_label object=$patVitale field="adresse"}}</th>
      <td rowspan="2">
        {{mb_value object=$patVitale field="adresse"}}
        {{mb_field object=$patVitale field="adresse" hidden="true"}}
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$patVitale field="prenom"}}</th>
      <td colspan="3">
        {{mb_value object=$patVitale field="prenom"}}
        {{mb_field object=$patVitale field="prenom" hidden="true"}}
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$patVitale field="naissance"}}</th>
      <td>
        {{mb_value object=$patVitale field="naissance"}}
        {{mb_field object=$patVitale field="naissance" hidden="true"}}
        {{mb_field object=$patVitale field="rang_naissance" hidden="true"}}
      </td>
      <th>{{mb_label object=$patVitale field="cp"}}</th>
      <td>
        {{mb_value object=$patVitale field="cp"}}
        {{mb_field object=$patVitale field="cp" hidden="true"}}
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$patVitale field="matricule"}}</th>
      <td>
        {{mb_value object=$patVitale field="matricule"}}
        {{mb_field object=$patVitale field="matricule" hidden="true"}}
        {{mb_field object=$patVitale field="assure_matricule" hidden="true"}}
        {{mb_field object=$patVitale field="rang_beneficiaire" hidden="true"}}
        {{mb_field object=$patVitale field="qual_beneficiaire" hidden="true"}}
      </td>
      <th>{{mb_label object=$patVitale field="ville"}}</th>
      <td>
        {{mb_value object=$patVitale field="ville"}}
        {{mb_field object=$patVitale field="ville" hidden="true"}}
      </td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$patVitale field="regime_sante"}}</th>
      <td colspan="3">
        {{mb_value object=$patVitale field="regime_sante"}}
        {{mb_field object=$patVitale field="code_regime" hidden="true"}}
        {{mb_field object=$patVitale field="caisse_gest" hidden="true"}}
        {{mb_field object=$patVitale field="centre_gest" hidden="true"}}
        {{mb_field object=$patVitale field="regime_sante" hidden="true"}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $can->edit}}
          <button class="new" type="button" onclick="PatientFromSelector.create({{$useVitale}});">
            {{tr}}Create{{/tr}} avec Vitale
          </button>
        {{/if}}
      </td>
      <td colspan="2" class="button">
        <button class="cancel" type="button" onclick="window.close()">{{tr}}Cancel{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

{{else}}

<!-- Formulaire de recherche -->
<form action="?" name="patientSearch" method="get">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="a" value="pat_selector" />
  <input type="hidden" name="dialog" value="1" />
  <input type="hidden" name="useVitale" value="" />
  
  <table class="form">
    <tr>
      <th class="title" colspan="4">Critères de sélection</th>
    </tr>
    
    <tr>
      <th><label for="name" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
      <td><input type="text" name="name" value="{{$name|stripslashes}}" size="30" tabindex="1" /></td>
      
      <th><label for="naissance" title="Date de naissance">Date de naissance</label></th>
      <td>
        {{mb_include module=patients template=inc_select_date date=$datePat tabindex=3}}
      </td>
    </tr>
    
    <tr>
      <th><label for="firstName" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
      <td><input type="text" name="firstName" value="{{$firstName|stripslashes}}" size="30" tabindex="2" /></td>

      {{if $conf.dPpatients.CPatient.tag_ipp && $dPsanteInstalled}}
        <th>IPP</th>
        <td>
          <input tabindex="6" type="text" name="patient_ipp" value="{{$patient_ipp}}"/>
        </td>
      {{else}}
        <td colspan="2"></td>
      {{/if}}
    </tr>
      
    <tr>
      <td>
        {{if $app->user_prefs.LogicielLectureVitale == 'vitaleVision'}}
          <button class="search singleclick" type="button" onclick="$V(this.form.useVitale, 1); VitaleVision.read();">
            Lire Vitale
          </button>
        {{elseif $app->user_prefs.LogicielLectureVitale == 'mbHost'}}
          {{mb_include module=mbHost template=inc_vitale operation='search'}}
        {{elseif $modFSE && $modFSE->canRead()}}
          {{mb_include module=fse template=inc_button_vitale}}
        {{/if}}
      </td>
      <td class="button">
        <button class="search" id="pat_selector_search_pat_button" type="submit">{{tr}}Search{{/tr}}</button>
      </td>
      <td class="button">
        {{if $can->edit}}
          {{if $name || $firstName || $patient_ipp || ($datePat && $datePat != "--")}}
          <button class="new" id="pat_selector_create_pat_button" type="button" onclick="PatientFromSelector.create({{$useVitale}});">
            {{tr}}Create{{/tr}}
          </button>
          {{/if}}
        {{/if}}
      </td>
      <td class="button">
        <button class="cancel" type="button" onclick="window.close()">{{tr}}Cancel{{/tr}}</button>
      </td>
    </tr>    
  </table>
</form>
{{/if}}

{{if $conf.dPpatients.CPatient.limit_char_search && ($name != $name_search || $firstName != $firstName_search)}}
<div class="small-info">
  La recherche est volontairement limitée aux {{$conf.dPpatients.CPatient.limit_char_search}} premiers caractères 
  <ul>
    {{if $name != $name_search}}
    <li>pour le <strong>nom</strong> : '{{$name_search}}'</li>
    {{/if}}   
    {{if $firstName != $firstName_search}}
    <li>pour le <strong>prénom</strong> : '{{$firstName_search}}'</li>
    {{/if}}   
  </ul>
</div>
{{/if}}

<!-- Liste de patients -->
<table class="tbl">
  <tr>
    <th class="title" colspan="5">Choisissez un patient dans la liste</th>
  </tr>
  <tr>
    <th>{{tr}}CPatient{{/tr}}</th>
    <th>{{mb_title class=CPatient field=naissance}}</th>
    {{if $patVitale}}
    <th>{{mb_label object=$patVitale field=matricule}}</th>
    {{else}}
    <th>
      {{mb_label class=CPatient field=tel}}
      <br />
      {{mb_label class=CPatient field=tel2}}
    </th>
    {{/if}}
    <th>{{mb_label class=CPatient field=adresse}}</th>
    <th style="white-space: nowrap;" class="narrow">{{tr}}Actions{{/tr}}</th>
  </tr>
  
  <!-- Recherche exacte -->
  <tr>
    <th colspan="5" class="section">
      {{tr}}dPpatients-CPatient-exact-results{{/tr}} 
      {{if ($patients|@count >= 30)}}({{tr}}thirty-first-results{{/tr}}){{/if}}
    </th>
  </tr>
  {{foreach from=$patients item=_patient}}
    {{mb_include template="inc_line_pat_selector"}}
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="5">
        {{if $name || $firstName}}
          {{tr}}dPpatients-CPatient-no-exact-results{{/tr}}
        {{else}}
          Veuillez saisir au moins le nom ou le prénom
        {{/if}}
      </td>
    </tr>
  {{/foreach}}

  <!-- recherche limitée -->
  <tr>
    <th colspan="5" class="section">
      {{tr}}dPpatients-CPatient-limited-results{{/tr}}
      {{if ($patientsLimited|@count >= 30)}}({{tr}}thirty-first-results{{/tr}}){{/if}}
    </th>
  </tr>
  {{foreach from=$patientsLimited item=_patient}}
    {{mb_include template="inc_line_pat_selector"}}
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
    {{mb_include template="inc_line_pat_selector"}}
  {{/foreach}}
</table>
