{{mb_default var=addform value=""}}
{{mb_default var=type_see value=""}}
{{mb_default var=dossier_anesth_id value=""}}

<script>
  updateFieldsComposant = function(selected) {
    var composant = selected.down('.view').getText();
    var code_composant = selected.get("code");

    var oFormAllergie = getForm("editAntFrm{{$addform}}");
    $V(oFormAllergie.type, "alle");
    $V(oFormAllergie.appareil, "");
    $V(oFormAllergie.rques, composant);

    $V(oFormAllergie._idex_code, code_composant);
    $V(oFormAllergie._idex_tag, "BCB_COMPOSANT");

    return onSubmitAnt(oFormAllergie, '{{$type_see}}');
  };

  Main.add(function() {
    if (!DossierMedical.patient_id) {
      DossierMedical.sejour_id  = '{{$sejour_id}}';
      DossierMedical._is_anesth = '{{$_is_anesth}}';
      DossierMedical.patient_id = '{{$patient->_id}}';
      DossierMedical.dossier_anesth_id = '{{$dossier_anesth_id}}';
    }
    {{if $type_see}}
      DossierMedical.reloadDossierPatient(null, '{{$type_see}}');
    {{else}}
      DossierMedical.reloadDossiersMedicaux();
    {{/if}}

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
<fieldset>
  <legend>Antécédents et allergies</legend>
  <form name="editAntFrm{{$addform}}" action="?m=cabinet" method="post" onsubmit="return onSubmitAnt(this, '{{$type_see}}');">
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
        {{if in_array($conf.dPmedicament.base, array("bcb", "vidal"))}}
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
        <td rowspan="4" style="width: 100%">
          {{mb_field object=$antecedent field="rques" rows="4" form="editAntFrm$addform"
          aidesaisie="filterWithDependFields: false, validateOnBlur: 0"}}
        </td>
      </tr>
      <tr>
        <th style="height: 20px">{{mb_label object=$antecedent field="type"}}</th>
        <td>{{mb_field object=$antecedent field="type" emptyLabel="None" alphabet="1" style="width: 9em;" onchange=""}}</td>
      </tr>
      <tr>
        <th style="height: 20px">{{mb_label object=$antecedent field="appareil"}}</th>
        <td>{{mb_field object=$antecedent field="appareil" emptyLabel="None" alphabet="1" style="width: 9em;"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$antecedent field="majeur"}}</th>
        <td>{{mb_field object=$antecedent field="majeur" typeEnum="checkbox"}}</td>
      </tr>
      <tr>
        <td class="button" colspan="3">
          <button id="inc_ant_consult_trait_button_add_atcd" class="tick" type="button" onclick="this.form.onsubmit();">
            {{tr}}Add{{/tr}} l'antécédent
          </button>
        </td>
      </tr>
    </table>

    {{if in_array($conf.dPmedicament.base, array("bcb", "vidal"))}}
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