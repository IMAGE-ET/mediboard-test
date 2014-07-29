{{mb_script module="dPpmsi" script="PMSI" ajax=$ajax}}

<script>
  changeCodeToDel = function(subject_id, code_ccam, actes_ids){
    var oForm = getForm("manageCodes");
    $V(oForm._selCode, code_ccam);
    $V(oForm._actes, actes_ids);
    ActesCCAM.remove(subject_id);
  };

  editCodage = function(codage_id) {
    var url = new Url("salleOp", "ajax_edit_codages_ccam");
    url.addParam("codage_id", codage_id);
    url.requestModal(
      null, null,
      {onClose: function() {ActesCCAM.refreshList('{{$subject->_id}}','{{$subject->_praticien_id}}')}}
    );
    window.urlCodage = url;
  };
</script>

<!-- Pas d'affichage de inc_manage_codes si la consultation est deja validée -->
 {{*if $subject instanceof CConsultation && !$subject->_coded*}}  
  <table class="main layout">
    <tr>
      <td class="halfPane">
        <fieldset id="didac_inc_manage_codes_fieldset_code">
          <legend id="didac_actes_ccam_execution">Ajouter un code</legend>
          <table class="layout">
            <tr>
              <td class="halfPane">
                <form name="manageCodes" action="?m={{$module}}" method="post">
                  <input type="hidden" name="m" value="{{$subject->_ref_module->mod_name}}" />
                  <input type="hidden" name="dosql" value="{{$do_subject_aed}}" />
                  <input type="hidden" name="{{$subject->_spec->key}}" value="{{$subject->_id}}" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="codes_ccam" value="{{$subject->codes_ccam}}" />
                  <input type="submit" disabled="disabled" style="display:none;"/>
                  <input type="hidden" name="_chir" value="{{$subject->_praticien_id}}" />
                  {{if ($subject->_class=="COperation")}}
                    <input type="hidden" name="_anesth" value="{{$subject->_ref_plageop->anesth_id}}" />
                  {{/if}}
                  <input type="hidden" name="_class" value="{{$subject->_class}}" />
                  <span id="didac_actes_ccam_executant"></span>
                  <span id="didac_actes_ccam_button_comment" ></span>
                  <input name="_actes" type="hidden" value="" />
                  <input name="_selCode" type="hidden" value="" />
                  <button id="didac_actes_ccam_tr_modificateurs" class="search" type="button" onclick="CCAMSelector.init()">
                    {{tr}}Search{{/tr}}
                  </button>
                  <span id="didac_actes_ccam_ext_doc"></span>
                  <input type="text" size="10" name="_codes_ccam" />

                  <script type="text/javascript">
                    CCAMSelector.init = function(){
                      this.sForm = "manageCodes";
                      this.sClass = "_class";
                      this.sChir = "_chir";
                      {{if ($subject->_class=="COperation")}}
                      this.sAnesth = "_anesth";
                      {{/if}}
                      this.sView = "_codes_ccam";
                      this.pop();
                    };

                    var oForm = getForm("manageCodes");
                    Main.add(function() {
                      var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
                      url.autoComplete(oForm._codes_ccam, '', {
                        minChars: 1,
                        dropdown: true,
                        width: "250px",
                        updateElement: function(selected) {
                          $V(oForm._codes_ccam, selected.down("strong").innerHTML);
                          ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');
                        }
                      });
                    });
                  </script>

                  <button class="add" name="addCode" type="button" onclick="ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}')">
                    {{tr}}Add{{/tr}}
                  </button>
                </form>
              </td>
              <td class="text">
                <table class="tbl">
                  <tr>
                    <th colspan="3">Actes disponibles</th>
                  </tr>

                  {{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
                    {{assign var=actes_ids value=$subject->_associationCodesActes.$_key.ids}}
                    {{unique_id var=uid_autocomplete_asso}}
                    {{assign var=can_delete value=1}}
                    {{foreach from=$_code->activites item=_activite}}
                      {{foreach from=$_activite->phases item=_phase}}
                        {{if $can_delete && $_phase->_connected_acte->signe && !$can->admin}}
                          {{assign var=can_delete value=0}}
                        {{/if}}
                      {{/foreach}}
                    {{/foreach}}
                    <tr>
                      <td>
                        <a href="#" onclick="CodeCCAM.show('{{$_code->code}}', '{{$subject->_class}}')">
                          {{$_code->code}}
                        </a>
                      </td>
                      <td>
                        <!-- Actes complémentaires -->
                        {{if count($_code->assos) > 0}}
                          <div class="small" style="float:right;">
                            <form name="addAssoCode{{$uid_autocomplete_asso}}" method="get">
                              <input type="text" size="13em" name="keywords" value="&mdash; {{$_code->assos|@count}} comp./supp." onclick="$V(this, '');"/>
                            </form>
                          </div>
                          <script>
                            Main.add(function() {
                              var form = getForm("addAssoCode{{$uid_autocomplete_asso}}");
                              var url = new Url("dPccam", "ajax_autocomplete_ccam_asso");
                              url.addParam("code", "{{$_code->code}}");
                              url.autoComplete(form.keywords, null, {
                                minChars: 2,
                                dropdown: true,
                                width: "250px",
                                updateElement: function(selected) {
                                  setCodeTemp(selected.down("strong").innerHTML);
                                }
                              });
                            });
                          </script>
                        {{/if}}
                      </td>
                      <td>
                        {{if $can_delete}}
                          <button type="button" class="notext trash" onclick="changeCodeToDel('{{$subject->_id}}', '{{$_code->code}}', '{{$actes_ids}}')">
                            {{tr}}Delete{{/tr}}
                          </button>
                        {{/if}}
                      </td>
                    </tr>
                  {{/foreach}}
                </table>
              </td>
            </tr>
          </table>
        </fieldset>
        <fieldset>
          <legend>Récapitulatif du codage</legend>
          <table class="form">
            <tr>
              <th class="category">Praticien</th>
              <th class="category">Actes</th>
              <th class="category">Actions</th>
            </tr>
            {{foreach from=$subject->_ref_codages_ccam item=_codage}}
            <tr>
              <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_codage->_ref_praticien}}</td>
              <td class="text">
                {{foreach from=$_codage->_ref_actes_ccam item=_acte}}
                  {{$_acte->code_acte}}({{$_acte->code_activite}}) :
                  {{mb_value object=$_acte field = _tarif}}
                  <br />
                  {{foreachelse}}
                  {{tr}}CActeCCAM.none{{/tr}}
                {{/foreach}}
              </td>
              <td class="button">
                <button type="button" class="notext edit" onclick="editCodage({{$_codage->_id}})"
                  title="{{$_codage->association_rule}} ({{mb_value object=$_codage field=association_mode}})">
                  {{tr}}Edit{{/tr}}
                </button>
                <form name="formCodage-{{$_codage->_id}}" action="?" method="post"
                      onsubmit="return onSubmitFormAjax(this.form, {
                  onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) })">
                  <input type="hidden" name="m" value="ccam" />
                  <input type="hidden" name="dosql" value="do_codageccam_aed" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="codage_ccam_id" value="{{$_codage->_id}}" />
                  {{if $_codage->locked}}
                    <input type="hidden" name="locked" value="0" />
                    <button type="button" class="notext unlock" onclick="this.onsubmit()">
                      {{tr}}Unlock{{/tr}}
                    </button>
                  {{else}}
                    <input type="hidden" name="locked" value="1" />
                    <button type="button" class="notext lock" onclick="this.onsubmit()">
                      {{tr}}Lock{{/tr}}
                    </button>
                  {{/if}}
                  {{if !$_codage->_ref_actes_ccam|@count}}
                    <button type="button" class="notext trash"
                            onclick="confirmDeletion(this.form,{typeName:'le codage',objName:'{{$_codage->_view|smarty:nodefaults|JSAttribute}}', ajax: '1'},
                              {onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) })">
                      {{tr}}Delete{{/tr}}
                    </button>
                  {{/if}}
                </form>
              </td>
            </tr>
            {{foreachelse}}
            <tr>
              <td class="empty" colspan="10">{{tr}}CCodageCCAM-none{{/tr}}</td>
            </tr>
            {{/foreach}}
            <tr>
              <td colspan="3">
                <form name="newCodage" action="?" method="post"
                      onsubmit="return onSubmitFormAjax(this, {
                  onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) })">
                  <input type="hidden" name="m" value="ccam" />
                  <input type="hidden" name="dosql" value="do_codageccam_aed" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="codage_ccam_id" value="" />
                  <input type="hidden" name="codable_class" value="{{$subject->_class}}" />
                  <input type="hidden" name="codable_id" value="{{$subject->_id}}" />
                  Ajouter un executant
                  <select name="praticien_id" style="width: 20em;" onchange="this.form.onsubmit();">
                    <option value="">&mdash; Choisir un professionnel de santé</option>
                    {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs}}
                  </select>
                </form>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
      {{if !$subject instanceof CConsultation}}
      <td class="halfPane">
        <fieldset>
          <legend>Validation du codage</legend>
          {{if $conf.dPsalleOp.CActeCCAM.envoi_actes_salle || $m == "dPpmsi" && $subject instanceof COperation}}
            {{if !$subject->facture || $m == "dPpmsi" || $can->admin}}
            <script>
              Main.add(function () {
                PMSI.loadExportActes('{{$subject->_id}}', '{{$subject->_class}}', 1, 'dPsalleOp');
              });
            </script>
            {{/if}}
            <table class="main layout">
              <tr>
                <td id="export_{{$subject->_class}}_{{$subject->_id}}">

                </td>
              </tr>
            </table>
          {{/if}}
          {{if ($module == "dPsalleOp" || $module == "dPhospi") && $conf.dPsalleOp.CActeCCAM.signature}}
            {{if $subject instanceof COperation && $subject->cloture_activite_1 && $subject->cloture_activite_4}}
              <button class="tick" disabled="disabled">Signer les actes</button>
            {{else}}
              <button class="tick" onclick="signerActes('{{$subject->_id}}', '{{$subject->_class}}')">
                Signer les actes
              </button>
            {{/if}}
            {{if $subject instanceof COperation || $subject instanceof CSejour}}
              {{if $subject->cloture_activite_1 && $subject->cloture_activite_4}}
                <button class="tick" disabled="disabled">Clôturer les activités</button>
              {{else}}
                <button class="tick" onclick="clotureActivite('{{$subject->_id}}', '{{$subject->_class}}')">Clôturer les activités</button>
              {{/if}}
            {{/if}}
          {{/if}}
        </fieldset>
      </td>
      {{/if}}
    </tr>
  </table>
{{*/if*}}

{{if $ajax}}
  <script type="text/javascript">
    oCodesManagerForm = document.manageCodes;
    prepareForm(oCodesManagerForm);
  </script>
{{/if}}