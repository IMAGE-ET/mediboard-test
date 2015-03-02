{{mb_script module="dPpmsi" script="PMSI" ajax=$ajax}}
{{mb_script module="dPccam" script="code_ccam" ajax=$ajax}}

<script>
  changeCodeToDel = function(subject_id, code_ccam, actes_ids){
    var oForm = getForm("manageCodes");
    $V(oForm._selCode, code_ccam);
    $V(oForm._actes, actes_ids);
    ActesCCAM.remove(subject_id);
  };

  editCodages = function(codable_class, codable_id, praticien_id) {
    var url = new Url("salleOp", "ajax_edit_codages_ccam");
    url.addParam('codable_class', codable_class);
    url.addParam('codable_id', codable_id);
    url.addParam('praticien_id', praticien_id);
    url.requestModal(
      -10, -50,
      {onClose: function() {ActesCCAM.refreshList('{{$subject->_id}}','{{$subject->_praticien_id}}')}}
    );
    window.urlCodage = url;
  };

  lockCodages = function(praticien_id, codable_class, codable_id) {
    var url = new Url('ccam', 'ajax_check_lock_codage');
    url.addParam('praticien_id', praticien_id);
    url.addParam('codable_class', codable_class);
    url.addParam('codable_id', codable_id);
    url.addParam('lock', 1);
    {{if $conf.dPccam.CCodable.lock_codage_ccam == 'password'}}
      url.requestModal(null, null, {onClose: ActesCCAM.notifyChange.curry(codable_id, praticien_id)});
    {{else}}
      url.requestUpdate('systemMsg', {onComplete: ActesCCAM.notifyChange.curry(codable_id, praticien_id)})
    {{/if}}
  };

  unlockCodages = function(praticien_id, codable_class, codable_id) {
    var url = new Url('ccam', 'ajax_check_lock_codage');
    url.addParam('praticien_id', praticien_id);
    url.addParam('codable_class', codable_class);
    url.addParam('codable_id', codable_id);
    url.addParam('lock', 0);
    {{if $conf.dPccam.CCodable.lock_codage_ccam == 'password'}}
      url.requestModal(null, null, {onClose: ActesCCAM.notifyChange.curry(codable_id, praticien_id)});
    {{else}}
      url.requestUpdate('systemMsg', {onComplete: ActesCCAM.notifyChange.curry(codable_id, praticien_id)})
    {{/if}}
  };

  deleteCodages = function(praticien_id) {
    Modal.confirm('Voulez réellement supprimer les codages CCAM de ce praticien?', {
      onOK: function() {
        var forms = $$('form[data-praticien_id=' + praticien_id + ']');
        forms.each(function(form) {
          $V(form.del, 1);
          form.onsubmit();
        });
      }
    })
  };

  CCAMSelector.init = function(){
    this.sForm = "manageCodes";
    this.sClass = "_class";
    this.sChir = "_chir";
    {{if ($subject->_class=="COperation")}}
    this.sAnesth = "_anesth";
    {{/if}}
    {{if $subject->_class == 'CSejour'}}
      this.sDate = '{{$subject->_sortie}}';
    {{else}}
      this.sDate = '{{$subject->_datetime}}';
    {{/if}}
    this.sView = "_new_code_ccam";
    this.pop();
  };

  Main.add(function() {
    var oForm = getForm("manageCodes");
    var url = new Url("dPccam", "httpreq_do_ccam_autocomplete");
    {{if $subject->_class == 'CSejour'}}
      url.addParam("date", '{{$subject->_sortie}}');
    {{else}}
      url.addParam("date", '{{$subject->_datetime}}');
    {{/if}}
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

{{if $conf.dPccam.CCodeCCAM.use_new_association_rules}}
<!-- Nouvel affichage en se basant sur le codage de chaque praticien -->
<table class="main layout">
  <tr>
    <td class="halfPane">
      <fieldset id="didac_inc_manage_codes_fieldset_executant">
        <legend id="didac_actes_ccam_executant">Ajouter un executant</legend>
        <form name="newCodage" action="?" method="post"
              onsubmit="return onSubmitFormAjax(this, {
                onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) })">
          <input type="hidden" name="m" value="ccam" />
          <input type="hidden" name="dosql" value="do_codageccam_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="codage_ccam_id" value="" />
          <input type="hidden" name="codable_class" value="{{$subject->_class}}" />
          <input type="hidden" name="codable_id" value="{{$subject->_id}}" />
          {{if $subject->_class == "COperation" || $subject->_class == "CDevisCodage"}}
            {{assign var=date_codable value=$subject->date}}
          {{else}}
            {{assign var=date_codable value=$subject->_date}}
          {{/if}}
          <input type="hidden" name="date" value="{{$date_codable}}"/>
          <select name="praticien_id" style="width: 20em; float: left;" onchange="this.form.onsubmit();">
            <option value="">&mdash; Choisir un professionnel de santé</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs}}
          </select>

          {{if $user->_is_praticien && !$user->_id|@array_key_exists:$subject->_ref_codages_ccam}}
            <div style="float: right;">
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$user}}
              <button class="add notext" type="button" title="Ajouter un codage" onclick="$V(this.form.praticien_id, {{$user->_id}});"></button>
            </div>
          {{/if}}
        </form>
        <table class="tbl">
          <tr>
            <th class="category">Praticien</th>
            <th class="category">Actes cotés</th>
            <th class="category">Actions</th>
          </tr>
          {{foreach from=$subject->_ref_codages_ccam item=_codages_by_prat name=codages}}
            {{assign var=total value=0}}
            {{foreach from=$_codages_by_prat item=_codage name=codages_by_prat}}
              {{math assign=total equation="x+y" x=$total y=$_codage->_total}}
              <tr>
                {{if $smarty.foreach.codages_by_prat.first}}
                  <td rowspan="{{$_codages_by_prat|@count}}">{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_codage->_ref_praticien}}</td>
                {{/if}}

                <td {{if !$_codage->_ref_actes_ccam|@count}}class="empty"{{/if}} {{if !$smarty.foreach.codages_by_prat.last}}style="border-bottom: 1pt dotted #93917e;"{{/if}}>
                  {{if !$_codage->_ref_actes_ccam|@count}}
                    {{tr}}CActeCCAM.none{{/tr}}
                  {{else}}
                    <table class="layout">
                      {{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
                        {{foreach from=$_code->activites item=_activite}}
                          {{foreach from=$_activite->phases item=_phase}}
                            {{if $_phase->_connected_acte->_id && $_phase->_connected_acte->executant_id == $_codage->praticien_id &&
                                 (($_activite->numero != '4' && !$_codage->activite_anesth) || ($_activite->numero == '4' && $_codage->activite_anesth))}}
                              {{assign var =_acte value=$_phase->_connected_acte}}
                              <tr>
                                <td>
                                  <a href="#" onclick="CodeCCAM.show('{{$_code->code}}', '{{$subject->_class}}');">
                                    {{$_acte->code_acte}}
                                  </a>
                                </td>
                                <td>
                                  <span class="circled ok">
                                    {{$_acte->code_activite}}-{{$_acte->code_phase}}
                                  </span>
                                </td>
                                <td>
                                  {{if !$_phase->_modificateurs|@count}}
                                    <em style="color: #7d7d7d;">Aucun modif. dispo.</em>
                                  {{elseif !$_acte->modificateurs}}
                                    <strong>Aucun modif. codé</strong>
                                  {{else}}
                                    {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
                                      {{if $_mod->_checked}}
                                        <span class="circled {{if in_array($_mod->_state, array('not_recommended', 'forbidden'))}}error{{/if}}"
                                              title="{{$_mod->libelle}}">
                                          {{$_mod->code}}
                                        </span>
                                      {{/if}}
                                    {{/foreach}}
                                  {{/if}}
                                </td>
                                <td>
                                  {{if $_acte->code_association}}
                                  Asso : {{$_acte->code_association}}
                                  {{/if}}
                                </td>
                                <td>
                                  {{if $_acte->montant_depassement}}
                                    <span class="circled" style="background-color: #aaf" title="{{mb_value object=$_acte field=montant_depassement}}">
                                        DH
                                   </span>
                                  {{/if}}
                                </td>
                              </tr>
                            {{/if}}
                          {{/foreach}}
                        {{/foreach}}
                      {{/foreach}}
                    </table>
                  {{/if}}
                  <form name="formCodage-{{$_codage->_id}}" action="?" method="post" data-praticien_id="{{$_codage->praticien_id}}"
                        onsubmit="return onSubmitFormAjax(this{{if $smarty.foreach.codages_by_prat.last}}, {
                        onComplete: ActesCCAM.notifyChange.curry({{$subject->_id}},{{$subject->_praticien_id}}) }{{/if}});">
                    <input type="hidden" name="m" value="ccam" />
                    <input type="hidden" name="dosql" value="do_codageccam_aed" />
                    <input type="hidden" name="del" value="0" />
                    <input type="hidden" name="codage_ccam_id" value="{{$_codage->_id}}" />
                    <input type="hidden" name="locked" value="{{$_codage->locked}}" />
                  </form>
                </td>

                {{if $smarty.foreach.codages_by_prat.first}}
                  {{* On compte le nombre d'actes cotés pour ce praticien *}}
                  {{assign var=count_actes_by_prat value=0}}
                  {{section name=count_actes loop=$smarty.foreach.codages_by_prat.total}}
                    {{math assign=count_actes_by_prat equation="x+y" x=$count_actes_by_prat y=$_codages_by_prat[$smarty.section.count_actes.index]->_ref_actes_ccam|@count}}
                  {{/section}}

                  <td rowspan="{{$_codages_by_prat|@count}}" class="button">
                    {{if !$_codage->locked}}
                      <button type="button" class="notext edit" onclick="editCodages('{{$subject->_class}}', {{$subject->_id}}, {{$_codage->praticien_id}})"
                              title="{{$_codage->association_rule}} ({{mb_value object=$_codage field=association_mode}})">
                        {{tr}}Edit{{/tr}}
                      </button>
                    {{/if}}

                    {{if $_codage->locked}}
                      <button type="button" class="notext unlock"
                              onclick="unlockCodages({{$_codage->praticien_id}}, '{{$_codage->codable_class}}', {{$_codage->codable_id}})">
                        {{tr}}Unlock{{/tr}}
                      </button>
                    {{else}}
                      <button type="button" class="notext lock"
                              onclick="lockCodages({{$_codage->praticien_id}}, '{{$_codage->codable_class}}', {{$_codage->codable_id}})">
                        {{tr}}Lock{{/tr}}
                      </button>
                    {{/if}}
                    {{if !$count_actes_by_prat}}
                      <button type="button" class="notext trash"
                              onclick="deleteCodages({{$_codage->praticien_id}})">
                        {{tr}}Delete{{/tr}}
                      </button>
                    {{/if}}
                  </td>
                {{/if}}
              </tr>
              {{if $smarty.foreach.codages_by_prat.last && $total != 0}}
                <tr{{if !$smarty.foreach.codages.last}} style="border-bottom: 1pt dotted #93917e;"{{/if}}>
                  <td colspan="2" style="text-align: right;">
                    Montant total :
                  </td>
                  <td style="text-align: left;">
                    {{$total|number_format:2:',':' '}} {{$conf.currency_symbol|html_entity_decode}}
                  </td>
                </tr>
              {{/if}}
            {{/foreach}}
          {{foreachelse}}
            <tr>
              <td class="empty" colspan="10">{{tr}}CCodageCCAM.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </fieldset>
    </td>
    <td>
      <fieldset id="didac_inc_manage_codes_fieldset_code">
        <legend id="didac_actes_ccam_execution">Ajouter un code</legend>
        <form name="manageCodes" action="?" method="post">
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
          <input type="hidden" name="_new_code_ccam" value="" onchange="$V(this.form._codes_ccam, this.value); ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');"/>
          <span id="didac_actes_ccam_ext_doc"></span>
          <input type="text" size="10" name="_codes_ccam" />
        </form>
        <table class="tbl">
          <tr>
            <th class="category" colspan="10">Actes disponibles</th>
          </tr>

          {{foreach from=$subject->_ext_codes_ccam item=_code key=_key name=codes_ccam}}
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
            <tr {{if !$smarty.foreach.codes_ccam.last}}style="border-bottom: 1pt dotted #93917e;"{{/if}}>
              <td>
                <a href="#" onclick="CodeCCAM.show('{{$_code->code}}', '{{$subject->_class}}');">
                  {{$_code->code}}
                </a>
              </td>
              <td>
                {{foreach from=$_code->activites item=_activite}}
                  {{foreach from=$_activite->phases item=_phase}}
                    {{assign var="acte" value=$_phase->_connected_acte}}
                    {{assign var="view" value=$acte->_id|default:$acte->_view}}
                    {{assign var="key" value="$_key$view"}}
                    <form name="formActe-{{$view}}" action="?" method="post" onsubmit="return checkForm(this)">
                      <input type="hidden" name="m" value="dPsalleOp" />
                      <input type="hidden" name="dosql" value="do_acteccam_aed" />
                      <input type="hidden" name="del" value="0" />
                      <input type="hidden" name="acte_id" value="{{$acte->_id}}" />
                      <input type="hidden" name="object_id" value="{{$acte->object_id}}" />
                      <input type="hidden" name="object_class" value="{{$acte->object_class}}" />
                    </form>
                    <span class="circled {{if $_phase->_connected_acte->_id}}ok{{else}}error{{/if}}">
                      {{$_activite->numero}}-{{$acte->code_phase}}
                    </span>
                  {{/foreach}}
                {{/foreach}}
              </td>
              <td class="text">
                {{$_code->libelleLong}}
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
                          var form = getForm('manageCodes');
                          $V(form._codes_ccam, selected.down("strong").innerHTML);
                          ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');
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
      </fieldset>
    </td>
  </tr>
</table>
{{/if}}
<!-- Pas d'affichage de inc_manage_codes si la consultation est deja validée -->
 {{*if $subject instanceof CConsultation && !$subject->_coded*}}
  <table class="main layout">
    <tr>
      {{if !$conf.dPccam.CCodeCCAM.use_new_association_rules}}
      <td class="halfPane">
        <fieldset id="didac_inc_manage_codes_fieldset_code">
          <legend id="didac_actes_ccam_execution">Ajouter un code</legend>
          <form name="manageCodes" action="?" method="post">
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
            <input type="hidden" name="_new_code_ccam" value="" onchange="$V(this.form._codes_ccam, this.value); ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}');"/>
            <span id="didac_actes_ccam_ext_doc"></span>
            <input type="text" size="10" name="_codes_ccam" />
            <button class="add" name="addCode" type="button" onclick="ActesCCAM.add('{{$subject->_id}}','{{$subject->_praticien_id}}')">
              {{tr}}Add{{/tr}}
            </button>
          </form>
        </fieldset>
      </td>
      {{/if}}
      {{if !$subject instanceof CConsultation && !$subject instanceof CDevisCodage}}
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
          {{if $conf.dPsalleOp.CActeCCAM.signature}}
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