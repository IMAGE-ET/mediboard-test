{{mb_default var=addform value=""}}

<script>
  updateTokenCim10 = function() {
    onSubmitFormAjax(getForm("editDiagFrm"), DossierMedical.reloadDossierPatient);
  };

  updateTokenCim10Anesth = function(){
    onSubmitFormAjax(getForm("editDiagAnesthFrm"), DossierMedical.reloadDossierSejour);
  };

  updateFieldsComposant = function(selected) {
    var composant = selected.down('.view').getText();
    var code_composant = selected.get("code");

    var oFormAllergie = getForm("editAntFrm{{$addform}}");
    $V(oFormAllergie.type, "alle");
    $V(oFormAllergie.appareil, "");
    $V(oFormAllergie.rques, composant);

    $V(oFormAllergie._idex_code, code_composant);
    $V(oFormAllergie._idex_tag, "BCB_COMPOSANT");

    return onSubmitAnt(oFormAllergie);
  }

  updateFieldsCDS = function(selected) {
    var cds_text = selected.down('.view').getText();
    var cds_type = selected.get("type");
    var cds_code = selected.get("code");

    var oFormAllergie = getForm("editAntFrm{{$addform}}");
    if (cds_type == "CHA") {
      $V(oFormAllergie.type, "alle");
    }
    else {
      $V(oFormAllergie.type, "");
    }

    $V(oFormAllergie.appareil, "");
    $V(oFormAllergie.rques, cds_text);
    $V(oFormAllergie._idex_code, cds_code);
    $V(oFormAllergie._idex_tag, "COMPENDIUM_CDS");

    return onSubmitAnt(oFormAllergie);
  }

  Main.add(function() {
    if ($('tab_atcd{{$addform}}')) {
      Control.Tabs.create('tab_atcd{{$addform}}', false);
    }

    // Autocomplete des composants
    if ($("composant_autocomplete{{$addform}}")) {
      var urlAuto = new Url("medicament", "ajax_composant_autocomplete");
      urlAuto.autoComplete(getForm('editAntFrm{{$addform}}').keywords_composant, "composant_autocomplete{{$addform}}", {
        minChars: 3,
        updateElement: updateFieldsComposant
      } );
    }

    // Autocomplete des CDS
    if ($("cds_autocomplete{{$addform}}")) {
      var urlAuto = new Url("compendium", "ajax_list_cds");
      urlAuto.autoComplete(getForm('editAntFrm{{$addform}}').cds, "cds_autocomplete{{$addform}}", {
        minChars: 3,
        updateElement: updateFieldsCDS,
        dropdown: true,
        callback: function(input, queryString){
          return (queryString + "&type="+$V(getForm('editAntFrm{{$addform}}').type));
        }
      } );
    }
  });
</script>

<tr>
  <td>
    <fieldset>
      <legend>Antécédents et allergies</legend>
      <form name="editAntFrm{{$addform}}" action="?m=cabinet" method="post" onsubmit="return onSubmitAnt(this);">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_antecedent_aed" />
        <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />

        <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->
        {{if $sejour_id}}
        <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
        <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
        {{/if}}

        <ul id="tab_atcd{{$addform}}" class="control_tabs small">
          <li><a href="#atcd_texte_simple{{$addform}}">Texte libre</a></li>
          {{if "dPprescription"|module_active && "dPprescription show_chapters med"|conf:"CGroups-$g"}}
            {{if $conf.dPmedicament.base == "bcb"}}
              <li><a href="#atcd_base_med{{$addform}}">Allergie à un composant</a></li>
            {{/if}}
            {{if $conf.dPmedicament.base == "compendium"}}
              <li><a href="#atcd_cds{{$addform}}">CDS</a></li>
            {{/if}}
          {{/if}}
        </ul>

        <table class="layout main" id="atcd_texte_simple{{$addform}}">
          <tr>
            {{if $app->user_prefs.showDatesAntecedents}}
              <th style="height: 20px">{{mb_label object=$antecedent field=date}}</th>
              <td>{{mb_field object=$antecedent field=date form=editAntFrm$addform register=true}}</td>
            {{else}}
              <td colspan="2"></td>
            {{/if}}
            <td rowspan="3" style="width: 100%">
              {{mb_field object=$antecedent field="rques" rows="4" form="editAntFrm$addform"
                aidesaisie="filterWithDependFields: false, validateOnBlur: 0"}}
            </td>
          </tr>
          <tr>
            <th style="height: 20px">{{mb_label object=$antecedent field="type"}}</th>
            <td>{{mb_field object=$antecedent field="type" emptyLabel="None" alphabet="1" style="width: 9em;" onchange=""}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$antecedent field="appareil"}}</th>
            <td>{{mb_field object=$antecedent field="appareil" emptyLabel="None" alphabet="1" style="width: 9em;"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="3">
              <button id="inc_ant_consult_trait_button_add_atcd" class="tick" type="button" onclick="this.form.onsubmit();">
                {{tr}}Add{{/tr}} l'antécédent
              </button>
            </td>
          </tr>
        </table>

        {{if $conf.dPmedicament.base == "bcb"}}
        <table class="layout" id="atcd_base_med{{$addform}}" style="display: none;">
          <tr>
            <th>
              Composant
            </th>
            <td>
              <input type="text" name="keywords_composant" value="" size="50" class="autocomplete" />
              <div style="display:none; width: 350px;" class="autocomplete" id="composant_autocomplete{{$addform}}"></div>
              <input type="hidden" name="_idex_code" value="" />
              <input type="hidden" name="_idex_tag" value="" />
            </td>
          </tr>
        </table>
        {{/if}}

        {{if $conf.dPmedicament.base == "compendium"}}
        <table class="layout" id="atcd_cds{{$addform}}" style="display: none;">
          <tr>
            <th>
              CDS
            </th>
            <td>
              <input type="text" name="cds" value="" size="50" class="autocomplete" />
              <div style="display:none; width: 350px;" class="autocomplete" id="cds_autocomplete{{$addform}}"></div>
              <input type="hidden" name="_idex_code" value="" />
              <input type="hidden" name="_idex_tag" value="" />
            </td>
          </tr>
        </table>
        {{/if}}
      </form>
    </fieldset>

    {{if "dPprescription"|module_active || $conf.dPpatients.CTraitement.enabled}}
      {{mb_include module=cabinet template=inc_traitement}}
    {{/if}}

    {{if $conf.ref_pays == 1}}
      <fieldset>
        <legend>Base de données CIM</legend>
        {{main}}
          var url = new Url("dPcim10", "ajax_code_cim10_autocomplete");
          url.autoComplete(getForm("addDiagFrm").keywords_code, '', {
            minChars: 1,
            dropdown: true,
            width: "250px",
            select: "code",
            afterUpdateElement: function(oHidden) {
              oForm = getForm("addDiagFrm");
              $V(oForm.code_diag, oHidden.value);
              reloadCim10($V(oForm.code_diag));
            }
          });
        {{/main}}
        <form name="addDiagFrm" action="?m=dPcabinet" method="post" onsubmit="return false;">
          <strong>Ajouter un diagnostic</strong>
          <input type="hidden" name="chir" value="{{$userSel->_id}}" />
          <input type="text" name="keywords_code" class="autocomplete str code cim10" value="" size="10"/>
          <input type="hidden" name="code_diag" onchange="$V(this.form.keywords_code, this.value)"/>
          <button class="search" type="button" onclick="CIM10Selector.init()">{{tr}}Search{{/tr}}</button>
          <button class="tick notext" type="button" onclick="reloadCim10(this.form.code_diag.value)">{{tr}}Validate{{/tr}}</button>
          <script>
            CIM10Selector.init = function(){
              this.sForm = "addDiagFrm";
              this.sView = "code_diag";
              this.sChir = "chir";
              this.options.mode = "favoris";
              this.pop();
            }
          </script>
        </form>
      </fieldset>
    {{/if}}
  </td>
</tr>