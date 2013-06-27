{{mb_default var=addform value=""}}

<script>
updateTokenCim10 = function() {
  var oForm = getForm("editDiagFrm");
  onSubmitFormAjax(oForm, { onComplete : DossierMedical.reloadDossierPatient });
}

updateTokenCim10Anesth = function(){
  var oForm = getForm("editDiagAnesthFrm");
  onSubmitFormAjax(oForm, { onComplete : DossierMedical.reloadDossierSejour });
}
Main.add(function () {
  if($('tab_traitements_perso{{$addform}}')){
    Control.Tabs.create('tab_traitements_perso{{$addform}}', false);
  }
});
</script>
<tr>
  <td>
    <fieldset>
      <legend>Antécédents et allergies</legend>

      <form name="editAntFrm{{$addform}}" action="?m=dPcabinet" method="post" onsubmit="return onSubmitAnt(this);">
        <input type="hidden" name="m" value="patients" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_antecedent_aed" />
        <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />

        <!-- dossier_medical_id du sejour si c'est une consultation_anesth -->

        {{if $sejour_id}}
        <!-- On passe _sejour_id seulement s'il y a un sejour_id -->
        <input type="hidden" name="_sejour_id" value="{{$sejour_id}}" />
        {{/if}}

        <table class="layout main">
          <tr>
            {{if $app->user_prefs.showDatesAntecedents}}
              <th style="height: 1%">{{mb_label object=$antecedent field=date}}</th>
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
            <th style="height: 100%">{{mb_label object=$antecedent field="type"}}</th>
            <td>{{mb_field object=$antecedent field="type" emptyLabel="None" alphabet="1" style="width: 9em;" onchange=""}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$antecedent field="appareil"}}</th>
            <td>{{mb_field object=$antecedent field="appareil" emptyLabel="None" alphabet="1" style="width: 9em;"}}</td>
          </tr>
          <tr>
            <td class="button" colspan="3">
              <button class="tick" type="button" onclick="this.form.onsubmit();">
                {{tr}}Add{{/tr}} l'antécédent
              </button>
            </td>
          </tr>
        </table>
      </form>
    </fieldset>
    
    {{if $isPrescriptionInstalled || $conf.dPpatients.CTraitement.enabled}}
    <fieldset>
      <legend>Traitements personnels</legend>
      <table class="layout main">
        <tr>
          <td class="text">
            <ul id="tab_traitements_perso{{$addform}}" class="control_tabs small">
              {{if $isPrescriptionInstalled}}
                <li><a href="#tp_base_med{{$addform}}">Base de données de médicaments</a></li>
              {{/if}}
              {{if $conf.dPpatients.CTraitement.enabled}}
                <li><a href="#tp_texte_simple{{$addform}}">Texte simple</a></li>
              {{/if}}
            </ul>
            <hr class="control_tabs" /> 
          </td>
        </tr>
        {{/if}}
        
        {{if $isPrescriptionInstalled}}
        <tr id="tp_base_med{{$addform}}">
          <td class="text">
            <!-- Formulaire d'ajout de traitements -->
            <form name="editLineTP{{$addform}}" action="?m=dPcabinet" method="post">
              <input type="hidden" name="m" value="dPprescription" />
              <input type="hidden" name="dosql" value="do_add_line_tp_aed" />
              <input type="hidden" name="_code" value="" onchange="{{if $addform}}refreshAddPosotri(this.value);{{else}}refreshAddPoso(this.value);{{/if}}"/>
              <input type="hidden" name="_patient_id" value="{{$patient->_id}}" />
              <input type="hidden" name="praticien_id" value="{{$userSel->_id}}" />

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
                        MedSelector.init = function(onglet){
                          this.sForm = "editLineTP{{$addform}}";
                          this.sView = "produit";
                          this.sCode = "_code";
                          this.sSearch = document.editLineTP{{$addform}}.produit.value;
                          this.sSearchByCIS = 1;
                          this.selfClose = true;
                          this._DCI = 0;
                          this.sOnglet = onglet;
                          this.traitement_perso = true;
                          this.pop();
                        }
                    </script>
                    {{if $isPrescriptionInstalled && $addform}}
                      {{mb_script module="dPcabinet" script="traitement"}}
                    {{/if}}
                  </td>
                  <td>
                    <strong><div id="_libelle{{$addform}}"></div></strong>
                  </td>
                </tr>

                <tr>
                  {{if $app->user_prefs.showDatesAntecedents}}
                  <th>{{mb_label object=$line field="debut"}}</th>
                  <td>{{mb_field object=$line field="debut" register=true form=editLineTP$addform}}</td>
                  {{else}}
                  <td colspan="2"></td>
                  {{/if}}
                  <td rowspan="2" id="addPosoLine{{$addform}}"></td>
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
                  <td colspan="3" class="button">
                    <button id="button_submit_traitement{{$addform}}" class="tick" type="button" onclick="onSubmitFormAjax(this.form, {
                      onComplete: function(){
                        DossierMedical.reloadDossiersMedicaux();
                        {{if $addform}}
                          resetEditLineTPtri();
                          resetFormTPtri();
                        {{else}}
                          resetEditLineTP();
                          resetFormTP();
                        {{/if}}
                      }
                     } ); this.form.produit.focus();">
                      {{tr}}Add{{/tr}} le traitement
                    </button>
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
        {{/if}}
            
        <!-- Traitements -->
        {{if $conf.dPpatients.CTraitement.enabled}}
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
                  <button class="tick" type="button" onclick="this.form.onsubmit()">
                    {{tr}}Add{{/tr}} le traitement
                  </button>
                </td>
              </tr>
            </table>
            </form>
          </td>
        </tr>
        {{/if}}
      </table>
    </fieldset>
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