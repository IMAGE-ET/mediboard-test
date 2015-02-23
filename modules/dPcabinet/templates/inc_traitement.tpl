{{mb_default var=vw_traitement_texte_libre value=1}}
{{mb_default var=addform                   value=""}}
{{mb_default var=callback                  value=""}}
{{mb_default var=gestion_tp                value=""}}
{{mb_default var=sejour_id                 value=""}}
{{mb_default var=reload                    value=""}}
{{mb_default var=type_see                  value=""}}
{{mb_default var=dossier_anesth_id         value=""}}

{{mb_script module="prescription" script="prescription" ajax=1}}
{{mb_script module="dPmedicament" script="medicament_selector" ajax=1}}

<script>
  onSubmitTraitement = function (form) {
    var trait = $(form.traitement);
    if (!trait.present()) {
      return false;
    }

    onSubmitFormAjax(form, {
      onComplete : function() {
        {{if $type_see}}
          DossierMedical.reloadDossierPatient(null, '{{$type_see}}');
        {{else}}
          DossierMedical.reloadDossiersMedicaux();
        {{/if}}
      }
    } );

    trait.clear().focus();

    return false;
  };

  // UpdateFields de l'autocomplete de medicaments
  updateFieldsMedicamentTP{{$addform}} = function(selected) {
    var oFormTP = getForm("editLineTP{{$addform}}");
    // Submit du formulaire avant de faire le selection d'un nouveau produit
    if ($V(oFormTP._code)) {
      onSubmitFormAjax(oFormTP, function() {
        updateTP{{$addform}}(selected);
        DossierMedical.reloadDossiersMedicaux();
      });
    }
    else {
      updateTP{{$addform}}(selected);
    }
  };

  updateTP{{$addform}} = function(selected) {
    var oFormTP = getForm("editLineTP{{$addform}}");
    resetEditLineTP{{$addform}}();
    Element.cleanWhitespace(selected);
    var dn = selected.childElements();
    dn = dn[0].innerHTML;

    // On peut saisir un traitement personnel seulement le code CIP est valide
    if (isNaN(parseInt(dn))) {
      return
    }
    $V(oFormTP._code, dn);
    $("_libelle{{$addform}}").insert("<button type='button' class='cancel notext' onclick='resetEditLineTP{{$addform}}(); resetFormTP{{$addform}}();'></button>" +
      "<a href=\"#nothing\" onclick=\"Prescription.viewProduit('','','"+selected.down(".code-cis").getText()+"')\">"+
      selected.down(".libelle").getText()+"</a>");

    if (selected.down(".alias")) {
      $("_libelle{{$addform}}").insert(selected.down(".alias").getText());
    }

    if (selected.down(".forme")) {
      $("_libelle{{$addform}}").insert("<br /><span class='compact'>"+selected.down(".forme").getText()+"</span>");
    }

    $V(oFormTP.produit, '');
    $('button_submit_traitement{{$addform}}').focus();
  };

  resetEditLineTP{{$addform}} = function() {
    var oFormTP = getForm("editLineTP{{$addform}}");
    $("_libelle{{$addform}}").update("");
    oFormTP._code.value = '';
  };

  resetFormTP{{$addform}} = function() {
    var oFormTP = getForm("editLineTP{{$addform}}");
    $V(oFormTP.commentaire, '');
    $V(oFormTP.token_poso, '');
    $('addPosoLine{{$addform}}').update('');

    $V(oFormTP.long_cours, 1);
    $V(oFormTP.__long_cours, true);
  }

  refreshAddPoso{{$addform}} = function(code){
    var url = new Url("dPprescription", "httpreq_vw_select_poso");
    url.addParam("_code", code);
    url.addParam("addform", "{{$addform}}");
    url.requestUpdate("addPosoLine{{$addform}}");
  };

  submitAndCallback = function(form, callback) {
    $V(form.callback, callback);
    onSubmitFormAjax(form, function() {
      resetEditLineTP{{$addform}}();
      resetFormTP{{$addform}}();
    });
  }

  checkPosos = function() {
    var div = $("list_posogestion_tp");
    if (div.select("button").length == 0) {
      alert($T('CPrisePosologie-_poso_missing'));
      return false;
    }
    return true;
  }

  Main.add(function() {
    if (!DossierMedical.patient_id) {
      DossierMedical.sejour_id  = '{{$sejour_id}}';
      {{if isset($_is_anesth|smarty:nodefaults)}}
        DossierMedical._is_anesth = '{{$_is_anesth}}';
      {{/if}}
      DossierMedical.patient_id = '{{$patient->_id}}';
      DossierMedical.dossier_anesth_id = '{{$dossier_anesth_id}}';
    }
    {{if $reload}}
    DossierMedical.reloadDossierPatient('{{$reload}}', '{{$type_see}}');
    {{/if}}

    if ($('tab_traitements_perso{{$addform}}')) {
      Control.Tabs.create('tab_traitements_perso{{$addform}}', false);
    }

    // Autocomplete des medicaments
    var urlAuto = new Url("medicament", "httpreq_do_medicament_autocomplete");
    urlAuto.autoComplete(getForm('editLineTP{{$addform}}').produit, "_produit_auto_complete{{$addform}}", {
      minChars: 3,
      updateElement: updateFieldsMedicamentTP{{$addform}},
      callback: function(input, queryString) {
        var form = getForm('editLineTP{{$addform}}');
        return (queryString + "&produit_max=40&only_prescriptible_sf=0&with_alias=1&mask_generique="+($V(form.mask_generique)?'1':'0'));
      }
    } );
  });
</script>

<div id="legend_actions_tp" style="display: none;">
  <table class="form">
    <tr>
      <th colspan="2" class="title">
        Légende
      </th>
    </tr>
    <tr>
      <td style="height: 40px"><button class="stop">Arrêter</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>Prescription de son arrêt</li>
      </td>
    </tr>
    <tr>
      <td class="text" style="height: 40px"><button class="edit">Represcrire</button></td>
      <td>
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>ouverture de la ligne pour modification</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="height: 40px"><button class="right">Poursuivre</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel sans modification</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="height: 50px"><button class="hslip">Relai</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>Prescription de son arrêt</li>
          <li>Prescription d'un autre produit pour relai</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td style="height: 40px"><button class="pause">Pause</button></td>
      <td class="text">
        <ul>
          <li>Represcription du traitement personnel</li>
          <li>Prescription de son arrêt</li>
          <li>Represcription du même produit pour une date ultérieure</li>
        </ul>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="cancel" onclick="Control.Modal.close()">{{tr}}Close{{/tr}}</button>
      </td>
    </tr>
  </table>
</div>

{{assign var=traitement_enabled value="CAppUI::conf"|static_call:"dPpatients CTraitement enabled":"CGroups-$g"}}

<fieldset id="inc_ant_consult_fieldset_trt_perso{{$addform}}">
  <legend>Traitements personnels</legend>
  <table class="layout main">
    <tr>
      <td class="text">
        <ul id="tab_traitements_perso{{$addform}}" class="control_tabs small">
          {{if "dPprescription"|module_active && "dPprescription show_chapters med"|conf:"CGroups-$g"}}
            <li><a href="#tp_base_med{{$addform}}">Base de données de médicaments</a></li>
          {{/if}}
          {{if $traitement_enabled && $vw_traitement_texte_libre}}
            <li><a href="#tp_texte_simple{{$addform}}">Texte libre</a></li>
          {{/if}}
          {{if "dPprescription"|module_active && "dPprescription CPrescription show_element_tp"|conf:"CGroups-$g"}}
            <li><a href="#tp_nomenclature{{$addform}}">Nomenclature des éléments</a></li>
          {{/if}}
        </ul>
      </td>
    </tr>

    {{if "dPprescription"|module_active && "dPprescription show_chapters med"|conf:"CGroups-$g"}}
      <tr id="tp_base_med{{$addform}}">
        <td class="text">
          <script>
            Main.add(function() {
              getForm('editLineTP{{$addform}}').produit.focus();
            });
          </script>
          <!-- Formulaire d'ajout de traitements -->
          <form name="editLineTP{{$addform}}" action="?m=cabinet" method="post">
            <input type="hidden" name="m" value="prescription" />
            <input type="hidden" name="dosql" value="do_add_line_tp_aed" />
            <input type="hidden" name="_code" value="" onchange="refreshAddPoso{{$addform}}(this.value);"/>
            <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
            <input type="hidden" name="praticien_id" value="{{$userSel->_id}}" />
            <input type="hidden" name="callback" value="" />
            <table class="layout">
              <col style="width: 70px;" />
              <col class="narrow" />

              <tr>
                <th>Recherche</th>
                <td>
                  <div class="dropdown">
                    <input type="text" name="produit" value="" size="12" class="autocomplete" />
                    <div style="display:none; width: 350px;" class="autocomplete" id="_produit_auto_complete{{$addform}}"></div>
                  </div>
                  <button type="button" class="search notext" onclick="MedSelector.init('produit');"></button>
                  <script>
                    MedSelector.init = function(onglet) {
                      this.sForm = "editLineTP{{$addform}}";
                      this.sView = "produit";
                      this.sCode = "_code";
                      this.sSearch = document.editLineTP{{$addform}}.produit.value;
                      this.sSearchByCIS = 1;
                      this.selfClose = true;
                      this._DCI = 0;
                      this.sOnglet = onglet;
                      this.traitement_perso = true;
                      this.only_prescriptible_sf = 0;
                      this.addForm = '{{$addform}}';
                      this.pop();
                    }
                  </script>
                </td>
                <td>
                  <strong><div id="_libelle{{$addform}}"></div></strong>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <input name="mask_generique" value="{{$app->user_prefs.check_default_generique}}" title="Masquer les génériques"
                         {{if $app->user_prefs.check_default_generique}}checked="checked"{{/if}}
                         type="{{if "dPprescription general see_generique"|conf:"CGroups-$g"}}checkbox{{else}}hidden{{/if}}"/>
                  {{if "dPprescription general see_generique"|conf:"CGroups-$g"}}
                    <label for="mask_generique">Masquer les génériques</label>
                  {{/if}}
                </td>
              </tr>
              <tr>
                {{if $app->user_prefs.showDatesAntecedents}}
                  <th>{{mb_label object=$line field="debut"}}</th>
                  <td>{{mb_field object=$line field="debut" register=true form=editLineTP$addform}}</td>
                {{else}}
                  <td colspan="2"></td>
                {{/if}}
                <td rowspan="3" id="addPosoLine{{$addform}}"></td>
              </tr>

              {{if $app->user_prefs.showDatesAntecedents}}
                <tr>
                  <th>{{mb_label object=$line field="fin"}}</th>
                  <td>{{mb_field object=$line field="fin" register=true form=editLineTP$addform}}</td>
                </tr>
              {{/if}}

              <tr>
                <th>{{mb_label object=$line field="commentaire"}}</th>
                <td>{{mb_field object=$line field="commentaire" size=20 form=editLineTP$addform}}</td>
              </tr>

              <tr>
                <th>{{mb_label object=$line field="long_cours"}}</th>
                <td>{{mb_field object=$line field="long_cours" typeEnum=checkbox value=1}}</td>
              </tr>

              <tr>
                <td colspan="3" {{if !$gestion_tp}}class="button"{{/if}}>
                  <button id="button_submit_traitement{{$addform}}" class="tick" type="button" onclick="addToTokenPoso{{$addform}}(0);onSubmitFormAjax(this.form, function() {
                    {{if $callback}}
                      {{$callback}}();
                    {{elseif $reload}}
                      DossierMedical.reloadDossierPatient('{{$reload}}', '{{$type_see}}');
                    {{else}}
                      DossierMedical.reloadDossiersMedicaux();
                    {{/if}}
                      resetEditLineTP{{$addform}}();
                      resetFormTP{{$addform}}();
                    } ); this.form.produit.focus();">
                    {{tr}}Add{{/tr}} le traitement
                  </button>
                  {{if $gestion_tp}}
                    <fieldset style="display: inline-block">
                      <legend>Ajouter et ...  <button type="button" class="search notext" onclick="modal('legend_actions_tp')">Légende</button></legend>
                      <button type="button" class="stop" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'stopLineTP');">
                        Arrêter
                      </button>
                      <button type="button" class="edit" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'modifyLineTP');">
                        Represcrire
                      </button>
                      {{if $sejour_id}}
                        <button type="button" class="right" onclick="addToTokenPoso{{$addform}}(0); if (checkPosos()) { submitAndCallback(this.form, 'poursuivreLineTP'); }">
                          Poursuivre
                        </button>
                        <button type="button" class="hslip" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'relaiLineDialog');">
                          Relai
                        </button>
                        <button type="button" class="pause" onclick="addToTokenPoso{{$addform}}(0);submitAndCallback(this.form, 'pauseLineDialog')">
                          Pause
                        </button>
                      {{/if}}
                    </fieldset>

                  {{/if}}
                </td>
              </tr>
            </table>
          </form>
        </td>
      </tr>
    {{/if}}

    <!-- Traitements -->
    {{if $traitement_enabled && $vw_traitement_texte_libre}}
      <tr id="tp_texte_simple{{$addform}}">
        <td class="text">
          <form name="editTrmtFrm{{$addform}}" action="?m=dPcabinet" method="post" onsubmit="return onSubmitTraitement(this);">
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_traitement_aed" />
            <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />

            {{if $_is_anesth}}
              <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
              <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
            {{/if}}

            <table class="layout">
              <tr>
                {{if $app->user_prefs.showDatesAntecedents}}
                  <th style="height: 100%;">{{mb_label object=$traitement field=debut}}</th>
                  <td>{{mb_field object=$traitement field=debut form=editTrmtFrm$addform register=true}}</td>
                {{else}}
                  <td colspan="2"></td>
                {{/if}}
                <td rowspan="2" style="width: 100%">
                  {{mb_field object=$traitement field=traitement rows=4 form=editTrmtFrm$addform
                  aidesaisie="validateOnBlur: 0"}}
                </td>
              </tr>
              <tr>
                {{if $app->user_prefs.showDatesAntecedents}}
                  <th>{{mb_label object=$traitement field=fin}}</th>
                  <td>{{mb_field object=$traitement field=fin form=editTrmtFrm$addform register=true}}</td>
                {{else}}
                  <td colspan="2"></td>
                {{/if}}
              </tr>

              <tr>
                <td class="button" colspan="3">
                  <button class="tick">
                    {{tr}}Add{{/tr}} le traitement
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </td>
      </tr>
    {{/if}}

    {{if "dPprescription"|module_active && "dPprescription CPrescription show_element_tp"|conf:"CGroups-$g"}}
      {{mb_include module=prescription template=vw_add_line_element_tp}}
    {{/if}}
  </table>
</fieldset>