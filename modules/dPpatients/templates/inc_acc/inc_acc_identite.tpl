<script type="text/javascript">

togglePrenomsList = function(element) {
  var list = $("patient_identite").select('.prenoms_list').invoke('toggle');
  Element.classNames(element).flip('up', 'down');
};

toggleNomNaissance = function(element) {
{{if $nom_jeune_fille_mandatory}}
  var nom_jeune_fille = element.form.nom_jeune_fille;
  var nom_jeune_fille_label = nom_jeune_fille.up().previous().down();
  // Si on choisit Féminin
  if (element.value == 'f') {

      // Rajout de la classe notnull sur Le label et l'input
      nom_jeune_fille.addClassName('notNull');
      nom_jeune_fille_label.addClassName('notNull');

      // Ajout des observeurs sur l'input
      nom_jeune_fille.observe("change", notNullOK)
      .observe("keyup",  notNullOK)
      .observe("ui:change", notNullOK);

      // Si l'input contient déjà du texte, le déclenchement de l'événement ui:change
      // permet de le passer en classe notNullOK
      nom_jeune_fille.fire("ui:change");
  } else {
      // On retire les classes
      nom_jeune_fille.removeClassName('notNull');
      nom_jeune_fille_label.removeClassName('notNull');
      nom_jeune_fille_label.removeClassName('notNullOK');
      nom_jeune_fille_label.removeClassName('error');

      // On n'observe plus l'input
      nom_jeune_fille.stopObserving("change", notNullOK)
      .stopObserving("keyup",  notNullOK)
      .stopObserving("ui:change", notNullOK);
  }
{{/if}}
};

selectFirstEnabled = function(select){
  var found = false;
  $A(select.options).each(function (o,i) {
    if (!found && !o.disabled && o.value != '') {
      $V(select, o.value);
      found = true;
    }
  });
};

disableOptions = function (select, list) {
  $A(select.options).each(function (o) {
    o.disabled = list.include(o.value);
  });

  if (select.value == '' || select.options[select.selectedIndex].disabled) {
    selectFirstEnabled(select);
  }
};

changeCiviliteForSexe = function(element, assure) {
  var form = document.editFrm.elements;
  var valueSexe = $V(element);
  var civilite = (assure ? 'assure_' : '') + 'civilite';

  switch (valueSexe) {
    case 'm':
    disableOptions($(form[civilite]), $w('mme mlle vve'));
    break;

    case 'f':
    disableOptions($(form[civilite]), $w('m'));
    break;

    default:
    disableOptions($(form[civilite]), $w('m mme mlle enf dr pr me vve'));
    break;
  }
};

var adult_age = {{$conf.dPpatients.CPatient.adult_age}};

changeCiviliteForDate = function(element, assure) {
  var oForm = document.editFrm.elements;
  if ($V(element)) {
    var date = new Date();
    var naissance = $V(element).split('/')[2];
    if (((date.getFullYear()- adult_age) <= naissance) && (naissance <= (date.getFullYear()))) {
      $V($(oForm[(assure ? 'assure_' : '')+'civilite']), "enf");
    } else {
      changeCiviliteForSexe(element.form.sexe);
    }
  }
};

anonymous = function() {
  $V("editFrm_nom"   , "anonyme");
  $V("editFrm_prenom", "anonyme");
};

checkDoublon = function() {
  var oform = getForm("editFrm");
  if ($V(oform.nom) && $V(oform.prenom) && $V(oform.naissance)) {
    SiblingsChecker.request(oform);
  }
};

refreshInfoTutelle = function(tutelle) {
  var url = new Url("dPpatients", "ajax_check_correspondant_tutelle");
  {{if $patient->_id}}
    url.addParam("patient_id", '{{$patient->_id}}');
  {{/if}}
  url.addParam("tutelle", tutelle);
  url.requestUpdate('alert_tutelle');
};

Main.add(function() {
  var i,
      list = $("patient_identite").select(".prenoms_list input"),
      button = $("patient_identite").select("button.down.notext");
  for (i = 0; i < list.length; i++) {
    if ($V(list[i])) {
      togglePrenomsList(button[0]);
      break;
    }
  }
  changeCiviliteForSexe(document.forms.editFrm.elements.sexe);
  changeCiviliteForSexe(document.forms.editFrm.elements.assure_sexe, true);
  {{if $patient->_id}}
    refreshInfoTutelle('{{$patient->tutelle}}');
  {{/if}}
});
</script>
{{assign var=identity_status value="CAppUI::conf"|static_call:"dPpatients CPatient manage_identity_status":"CGroups-$g"}}
{{assign var=naissance_obligatoire value="CAppUI::conf"|static_call:"dPpatients CPatient nom_jeune_fille_mandatory":"CGroups-$g"}}
{{assign var=allowed_modify value="CAppUI::pref"|static_call:"allowed_modify_identity_status"}}
<table style="width: 100%">
  <tr>
    <td colspan="2" id="alert_tutelle"></td>
  </tr>
  <tr>
    <td style="width: 50%">
      <table class="form" id="patient_identite">
        <tr>
          <th class="category" colspan="3">Identité Patient</th>
        </tr>
        <tr>
          <td colspan="3" class="text">
            <div id="doublon-patient">
            </div>
          </td>
        </tr>
        <tr>
          <th style="width:30%">{{mb_label object=$patient field="nom"}}</th>
          <td>
            {{if $identity_status && $patient->_id && !$allowed_modify && $naissance_obligatoire == "0" && $patient->status == "VALI"}}
              {{mb_value object=$patient field="nom"}}
            {{else}}
              {{mb_field object=$patient field="nom" onchange="checkDoublon(); copyIdentiteAssureValues(this)"}}
              {{if !$patient->_id}}
                <button type="button" style="padding: 0" onclick="anonymous()" tabIndex="1000">
                  <img src="modules/dPpatients/images/anonyme.png" alt="Anonyme" />
                </button>
              {{/if}}
            {{/if}}
          </td>
          {{if $patient->_id}}
          <td rowspan="14"  class="narrow" style="text-align: center;" id="{{$patient->_guid}}-identity">
            {{mb_include template=inc_vw_photo_identite mode="edit"}}
          </td>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="prenom"}}</th>
          <td>
            {{if $identity_status && $patient->_id && $patient->status == "VALI" && !$allowed_modify}}
              {{mb_value object=$patient field="prenom"}}
            {{else}}
              {{mb_field object=$patient field="prenom" onchange="checkDoublon(); copyIdentiteAssureValues(this)"}}
            {{/if}}
            <button type="button" class="down notext" onclick="togglePrenomsList(this)" tabIndex="1000">{{tr}}Add{{/tr}}</button>

          </td>
        </tr>

        <tr class="prenoms_list" style="display: none;">
          <th>{{mb_label object=$patient field="prenom_2"}}</th>
          <td>{{mb_field object=$patient field="prenom_2" onchange="checkDoublon(); copyIdentiteAssureValues(this)"}} </td>
        </tr>

        <tr class="prenoms_list" style="display: none;">
          <th>{{mb_label object=$patient field="prenom_3"}}</th>
          <td>{{mb_field object=$patient field="prenom_3" onchange="checkDoublon(); copyIdentiteAssureValues(this)"}}</td>
        </tr>

        <tr class="prenoms_list" style="display: none;">
          <th>{{mb_label object=$patient field="prenom_4"}}</th>
          <td>{{mb_field object=$patient field="prenom_4" onchange="checkDoublon(); copyIdentiteAssureValues(this)"}} </td>
        </tr>

        <tr>
          <th>{{mb_label object=$patient field="nom_jeune_fille"}}</th>
          <td>
            {{if $identity_status && $patient->_id && $patient->status == "VALI" && !$allowed_modify}}
              {{mb_value object=$patient field="nom_jeune_fille"}}
            {{else}}
              {{mb_field object=$patient field="nom_jeune_fille" onchange="checkDoublon(); copyIdentiteAssureValues(this)"}}
              <button type="button" class="carriage_return notext" title="{{tr}}CPatient.name_recopy{{/tr}}"
                onclick="$V(getForm('editFrm').nom_jeune_fille, $V(getForm('editFrm').nom));" tabIndex="1000"></button>
              {{if $patient->_id && $patient->sexe == "f" && $nom_jeune_fille_mandatory}}
                <script type="text/javascript">
                  document.editFrm.nom_jeune_fille.addClassName("notNull");
                </script>
              {{/if}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="sexe"}}</th>
          <td>
            {{if $identity_status && $patient->_id && $patient->status == "VALI" && !$allowed_modify}}
              {{mb_value object=$patient field="sexe"}}
            {{else}}
              {{mb_field object=$patient field="sexe" canNull=false typeEnum=radio onchange="toggleNomNaissance(this); copyIdentiteAssureValues(this); changeCiviliteForSexe(this);"}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="naissance"}}</th>
          <td>
            {{if $identity_status && $patient->_id && $patient->status == "VALI" && !$allowed_modify}}
              {{mb_value object=$patient field="naissance"}}
            {{else}}
              {{mb_field object=$patient field="naissance" onchange="checkDoublon();copyIdentiteAssureValues(this); changeCiviliteForDate(this);"}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="civilite"}}</th>
          <td>
            {{assign var=civilite_locales value=$patient->_specs.civilite}}
            <select name="civilite" onchange="copyIdentiteAssureValues(this);">
              <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
              {{foreach from=$civilite_locales->_locales key=key item=_civilite}}
              <option value="{{$key}}" {{if $key == $patient->civilite}} selected="selected" {{/if}}>
                {{tr}}CPatient.civilite.{{$key}}-long{{/tr}} - ({{$_civilite}})
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="situation_famille"}}</th>
          <td>{{mb_field object=$patient field="situation_famille"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="rang_naissance"}}</th>
          <td>{{mb_field object=$patient field="rang_naissance"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="cp_naissance"}}</th>
          <td>{{mb_field object=$patient field="cp_naissance" onchange="copyIdentiteAssureValues(this)"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="lieu_naissance"}}</th>
          <td>{{mb_field object=$patient field="lieu_naissance" onchange="copyIdentiteAssureValues(this)"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="_pays_naissance_insee"}}</th>
          <td>
            {{mb_field object=$patient field="_pays_naissance_insee" onchange="copyIdentiteAssureValues(this)" class="autocomplete"}}
            <div style="display:none;" class="autocomplete" id="_pays_naissance_insee_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="profession"}}</th>
          <td>{{mb_field object=$patient field="profession" form=editFrm onchange="copyIdentiteAssureValues(this)" autocomplete="true,2,30,true,true,2"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="csp"}}</th>
          <td>
            <input type="text" name="_csp_view" size="25" value="{{$patient->_csp_view}}"/>
            {{mb_field object=$patient field="csp" hidden=true}}
          </td>
        </tr>
        <tr>
         {{if $conf.ref_pays == 1}}
            <th>{{mb_label object=$patient field="matricule"}}</th>
            <td>{{mb_field object=$patient field="matricule" onchange="copyIdentiteAssureValues(this)"}}</td>
          </tr>
        {{else}}
            <th>{{mb_label object=$patient field="avs"}}</th>
            <td>{{mb_field object=$patient field="avs"}}</td>
       {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="qual_beneficiaire"}}</th>
          <td>{{mb_field object=$patient field="qual_beneficiaire" style="width:20em;"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="tutelle"}}</th>
          <td colspan="2">{{mb_field object=$patient field="tutelle" typeEnum=radio onchange="refreshInfoTutelle(this.value);"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="vip"}}</th>
          <td colspan="2">{{mb_field object=$patient field="vip" typeEnum="checkbox"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="deces"}}</th>
          <td colspan="2">{{mb_field object=$patient field="deces" register=true form=editFrm}}</td>
        </tr>
      </table>
    </td>
    <td>
      <table class="form">
        <tr>
          <th class="category" colspan="2">Coordonnées Patient</th>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="adresse"}}</th>
          <td>{{mb_field object=$patient field="adresse" onchange="copyAssureValues(this)"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="cp"}}</th>
          <td>{{mb_field object=$patient field="cp" onchange="copyAssureValues(this)"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="ville"}}</th>
          <td>{{mb_field object=$patient field="ville" onchange="copyAssureValues(this)"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="pays"}}</th>
          <td>
            {{mb_field object=$patient field="pays" size="31" onchange="copyAssureValues(this)" class="autocomplete"}}
            <div style="display:none;" class="autocomplete" id="pays_auto_complete"></div>
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="tel"}}</th>
          <td>{{mb_field object=$patient field="tel" onchange="copyAssureValues(this)"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="tel2"}}</th>
          <td>{{mb_field object=$patient field="tel2" onchange="copyAssureValues(this)"}} {{mb_field object=$patient field="allow_sms_notification" typeEnum='checkbox'}}{{mb_label object=$patient field="allow_sms_notification"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="tel_autre"}}</th>
          <td>{{mb_field object=$patient field="tel_autre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="email"}}</th>
          <td>{{mb_field object=$patient field="email"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$patient field="rques"}}</th>
          <td>{{mb_field object=$patient field="rques" onblur="this.form.qual_beneficiaire.value == '0' ?
                 tabs.changeTabAndFocus('beneficiaire', this.form.regime_sante) :
                 tabs.changeTabAndFocus('assure', this.form.assure_nom);"}}</td>
        </tr>
        {{if "sisra"|module_active}}
          <tr>
            <th>{{mb_label object=$patient field="allow_sisra_send"}}</th>
            <td>{{mb_field object=$patient field="allow_sisra_send"}}</td>
          </tr>
        {{/if}}
        {{if "covercard"|module_active}}
          <tr style="display:none">
            <th>{{mb_label object=$patient field="_assureCC_id"}}</th>
            <td>{{mb_field object=$patient field="_assureCC_id"}}</td>
          </tr>
          <tr style="display:none">
            <th>{{mb_label object=$patient field="_assurance_assure_id"}}</th>
            <td>{{mb_field object=$patient field="_assurance_assure_id"}}</td>
          </tr>
          <tr style="display:none">
            <th>{{mb_label object=$patient field="_assure_end_date"}}</th>
            <td>{{mb_field object=$patient field="_assure_end_date"}}</td>
          </tr>
          <tr style="display:none">
            <th>{{mb_label object=$patient field="_assuranceCC_name"}}</th>
            <td>{{mb_field object=$patient field="_assuranceCC_name"}}</td>
          </tr>
          <tr style="display:none;">
            <th>{{mb_label object=$patient field="_assuranceCC_id"}}</th>
            <td>{{mb_field object=$patient field="_assuranceCC_id"}}</td>
          </tr>
          <tr style="display:none;">
            <th>{{mb_label object=$patient field="_invalid_assurance"}}</th>
            <td>{{mb_field object=$patient field="_invalid_assurance"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$patient field="_assuranceCC_ean"}}</th>
            <td>{{mb_field object=$patient field="_assuranceCC_ean"}}</td>
          </tr>
        {{/if}}
      </table>
    </td>
  </tr>
</table>