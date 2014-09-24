{{mb_script module="dPccam" script="code_ccam" ajax=$ajax}}

{{assign var="subject" value=$codage->_ref_codable}}

<script>

  changeCodageMode = function(element) {
    var codageForm = getForm("formCodageRules");
    if($V(element)) {
      $V(codageForm.association_mode, "user_choice");
    }
    else {
      $V(codageForm.association_mode, "auto");
    }
    codageForm.onsubmit();
  };

  syncCodageField = function(element, view) {
    var acteForm = getForm('codageActe-' + view);
    var fieldName = element.name;
    var fieldValue = $V(element);
    $V(acteForm[fieldName], fieldValue);
    if($V(acteForm.acte_id)) {
      acteForm.onsubmit();
    }
    else {
      checkModificateurs(element, view);
    }
  };

  checkModificateurs = function(input, acte) {
    var exclusive_modifiers = ['F', 'P', 'S', 'U'];
    var checkboxes = $$('input[data-acte="' + acte + '"].modificateur');
    var nb_checked = 0;
    var exclusive_modifier = '';
    var exclusive_modifier_checked = false;
    checkboxes.each(function(checkbox) {
      if (checkbox.checked) {
        nb_checked++;
        if (checkbox.get('double') == 2) {
          nb_checked++;
        }
        if (exclusive_modifiers.indexOf(checkbox.get('code')) != -1) {
          exclusive_modifier = checkbox.get('code');
          exclusive_modifier_checked = true;
        }
      }
    });

    checkboxes.each(function(checkbox) {
      checkbox.disabled = (!checkbox.checked && nb_checked == 4) ||
        (exclusive_modifiers.indexOf(exclusive_modifier) != -1 && exclusive_modifiers.indexOf(checkbox.get('code')) != -1 && !checkbox.checked && exclusive_modifier_checked);
    });

    var container = input.up();
    if (input.checked && container.hasClassName('warning')) {
      container.removeClassName('warning');
      container.addClassName('error');
    }
    else if (!input.checked && container.hasClassName('error')) {
      container.removeClassName('error');
      container.addClassName('warning');
    }
  };

  setRule = function(element) {
    var codageForm = getForm("formCodageRules");
    $V(codageForm.association_mode, "user_choice", false);
    var inputs = document.getElementsByName("association_rule");
    for(var i = 0; i < inputs.length; i++) {
      inputs[i].disabled = false;
    }
    $V(codageForm.association_rule, $V(element), false);
    codageForm.onsubmit();
  };

  switchViewActivite = function(value, activite) {
    if(value) {
      $$('.activite-'+activite).each(function(oElement) {oElement.show()});
    }
    else {
      $$('.activite-'+activite).each(function(oElement) {oElement.hide()});
    }
  };

  addActeAnesthComp = function(acte) {
    if (confirm("Voulez vous ajoutez l'acte d'anesthésie complémentaire " + acte + '?')) {
      var on_change = CCAMField{{$subject->_class}}{{$subject->_id}}.options.onChange;
      CCAMField{{$subject->_class}}{{$subject->_id}}.options.onChange = Prototype.emptyFunction;
      CCAMField{{$subject->_class}}{{$subject->_id}}.add(acte);
      onSubmitFormAjax(getForm('addActes-{{$subject->_guid}}'));
      CCAMField{{$subject->_class}}{{$subject->_id}}.options.onChange = on_change;
    }
  }

  Main.add(function(){
    Control.Tabs.create('rules-tab', true);
  });

</script>

<table class="tbl" style="min-width: 400px;">
  <tr>
    <th class="title" colspan="11" style="border-bottom: none;">
      <div style="float: left">
        <form name="addActes-{{$subject->_guid}}" method="post" onsubmit="return onSubmitFormAjax(this, window.urlCodage.refreshModal.bind(window.urlCodage))">
          {{if $subject instanceof CConsultation}}
            <input type="hidden" name="m" value="cabinet" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
          {{elseif $subject instanceof COperation}}
            <input type="hidden" name="m" value="planningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
          {{else}}
            <input type="hidden" name="m" value="planningOp" />
            <input type="hidden" name="dosql" value="do_sejour_aed" />
          {{/if}}
          {{mb_key object=$subject}}

            {{mb_field object=$subject field="codes_ccam" hidden=true onchange="this.form.onsubmit()"}}
            <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()" style="width: 12em" value="" class="autocomplete" placeholder="Ajouter un acte" />
            <div style="text-align: left; color: #000; display: none; width: 200px !important; font-weight: normal; font-size: 11px; text-shadow: none;"
                 class="autocomplete" id="_ccam_autocomplete_{{$subject->_guid}}"></div>
            <script>
              Main.add(function() {
                var form = getForm("addActes-{{$subject->_guid}}");
                var url = new Url("ccam", "httpreq_do_ccam_autocomplete");
                url.autoComplete(form._codes_ccam, "_ccam_autocomplete_{{$subject->_guid}}", {
                  minChars: 1,
                  dropdown: true,
                  width: "250px",
                  updateElement: function(selected) {
                    CCAMField{{$subject->_class}}{{$subject->_id}}.add(selected.down("strong").innerHTML);
                  }
                });
                CCAMField{{$subject->_class}}{{$subject->_id}} = new TokenField(form.elements["codes_ccam"], {
                  onChange : function() {
                    form.onsubmit();
                  },
                  sProps : "notNull code ccam"
                } );
              })
            </script>
        </form>
      </div>
      Actes du Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$codage->_ref_praticien}}
    </th>
  </tr>
  <tr>
    <th class="title" colspan="10" style="border-top: none;">
      {{foreach from=$subject->_ext_codes_ccam item=_code}}
        <span id="action-{{$_code->code}}" class="circled" style="background-color: #eeffee; color: black; font-weight: normal; font-size: 0.8em;">
         {{$_code->code}}

          {{if count($_code->assos) > 0}}
            {{unique_id var=uid_autocomplete_comp}}
            <form name="addAssoCode{{$uid_autocomplete_comp}}" method="get">
              <input type="text" size="8em" name="keywords" value="{{$_code->assos|@count}} cmp./sup." onclick="$V(this, '');"/>
            </form>
            <div style="text-align: left; color: #000; display: none; width: 200px !important; font-weight: normal; font-size: 11px; text-shadow: none;"
                 class="autocomplete" id="_ccam_add_comp_autocomplete_{{$_code->code}}">
            </div>
            <script>
              Main.add(function() {
                var form = getForm("addAssoCode{{$uid_autocomplete_comp}}");
                var url = new Url("dPccam", "ajax_autocomplete_ccam_asso");
                url.addParam("code", "{{$_code->code}}");
                url.autoComplete(form.keywords, '_ccam_add_comp_autocomplete_{{$_code->code}}', {
                  minChars: 2,
                  dropdown: true,
                  width: "250px",
                  updateElement: function(selected) {
                    CCAMField{{$subject->_class}}{{$subject->_id}}.add(selected.down("strong").innerHTML);
                  }
                });
              });
            </script>
          {{/if}}

          <button type="button" class="trash notext" onclick="CCAMField{{$subject->_class}}{{$subject->_id}}.remove('{{$_code->code}}')">
            {{tr}}Delete{{/tr}}
          </button>
      </span>
      {{/foreach}}
    </th>
  </tr>
  <tr>
    <th class="narrow">{{mb_title class=CActeCCAM field=code_acte}}</th>
    <th colspan="2" class="narrow">
      {{mb_title class=CActeCCAM field=code_activite}}
      <form name="list_activites-{{$codage->_guid}}" action="?" method="post" onsubmit="return false;">
        {{foreach from=$list_activites key=_num_activite item=_activite}}
          <input type="checkbox" name="activite_{{$_num_activite}}"
                 onchange="switchViewActivite($V(this), {{$_num_activite}});"
            {{if $_activite}}checked="checked"{{/if}} />
          <label for="activite_{{$_num_activite}}">{{$_num_activite}}</label>
        {{/foreach}}
      </form>
    </th>
    <th>{{mb_title class=CActeCCAM field=modificateurs}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=execution}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=motif_depassement}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=code_association}}</th>
    <th>{{mb_title class=CActeCCAM field=_tarif}}</th>
    <th class="narrow">Actions</th>
  </tr>
  {{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
  {{foreach from=$_code->activites item=_activite}}
    {{assign var="numero" value=$_activite->numero}}
  {{foreach from=$_activite->phases item=_phase}}
    {{assign var="acte" value=$_phase->_connected_acte}}
    {{assign var="view" value=$acte->_id|default:$acte->_view}}
    {{assign var="key" value="$_key$view"}}
    {{if (!$acte->_id) || ($acte->executant_id == $codage->praticien_id)}}
      <tr {{if !$acte->_id}}class="activite-{{$acte->code_activite}}"{{/if}}
        {{if !$list_activites.$numero && !$acte->_id}}style="display:none;"{{/if}}>
        <td {{if !$acte->_id}}class="error"{{/if}}>
          <a href="#" onclick="CodeCCAM.show('{{$acte->code_acte}}', '{{$subject->_class}}')">
            {{if $_code->type != 2}}
              <strong>
                {{mb_value object=$acte field=code_acte}}
              </strong>
            {{else}}
              <em>{{mb_value object=$acte field=code_acte}}</em>
            {{/if}}
          </a>
          {{if $_code->forfait}}
            <br />
            <small style="color: #f00">({{tr}}CDatedCodeCCAM.remboursement.{{$_code->forfait}}{{/tr}})</small>
          {{/if}}
        </td>
        <td class="narrow">
          <span class="circled {{if $acte->_id}}ok{{else}}error{{/if}}">
            {{mb_value object=$acte field=code_activite}}-{{mb_value object=$acte field=code_phase}}
          </span>
        </td>
        <td>
          {{mb_value object=$acte field=_tarif_base}}
        </td>
        <td class="greedyPane">
          {{assign var=nb_modificateurs value=$acte->modificateurs|strlen}}
          {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
            <span class="circled {{if $_mod->_state == 'prechecked'}}ok{{elseif $_mod->_checked && in_array($_mod->_state, array('not_recommended', 'forbidden'))}}error{{elseif in_array($_mod->_state, array('not_recommended', 'forbidden'))}}warning{{/if}}"
                  title="{{$_mod->libelle}} ({{$_mod->_montant}})">
              <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}"
                     {{if $_mod->_checked}}checked="checked"{{elseif $nb_modificateurs == 4 || $_mod->_state == 'forbidden' || (intval($acte->_exclusive_modifiers) > 0 && in_array($_mod->code, array('F', 'U', 'P', 'S')))}}disabled="disabled"{{/if}}
                     data-acte="{{$view}}" data-code="{{$_mod->code}}" data-double="{{$_mod->_double}}" class="modificateur" onchange="syncCodageField(this, '{{$view}}');" />
              <label for="modificateur_{{$_mod->code}}{{$_mod->_double}}">
                {{$_mod->code}}
              </label>
            </span>

            {{foreachelse}}
            <em>{{tr}}None{{/tr}}</em>
          {{/foreach}}
        </td>
        <td>
          <form name="codageActeExecution-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=execution form="codageActeExecution-$view" register=true onchange="syncCodageField(this, '$view');"}}
          </form>
        </td>
        <td>
          <form name="codageActeMontantDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=montant_depassement onchange="syncCodageField(this, '$view');"}}
          </form>
        </td>
        <td>
          <form name="codageActeMotifDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=motif_depassement emptyLabel="CActeCCAM-motif_depassement" onchange="syncCodageField(this, '$view');"}}
          </form>
        </td>
        <td
          {{if $acte->_id && ($acte->code_association != $acte->_guess_association)}}style="background-color: #fc9"{{/if}}>
          {{if $acte->_id}}
          <form name="codageActeCodeAssociation-{{$view}}" action="?" method="post" onsubmit="return false;">
            {{mb_field object=$acte field=code_association emptyLabel="CActeCCAM.code_association." onchange="syncCodageField(this, '$view');"}}
          </form>
          {{if $acte->code_association != $acte->_guess_association}}
            ({{$acte->_guess_association}})
          {{/if}}
          {{/if}}
        </td>
        <td {{if $acte->_id && !$acte->facturable}}style="background-color: #fc9"{{/if}}>
          {{mb_value object=$acte field=_tarif}}
        </td>
        <td class="button">
          <form name="codageActe-{{$view}}" action="?" method="post"
          onsubmit="return onSubmitFormAjax(this, {onComplete: function() {window.urlCodage.refreshModal()}});">
            <input type="hidden" name="m" value="salleOp" />
            <input type="hidden" name="dosql" value="do_acteccam_aed" />
            <input type="hidden" name="del" value="0" />
            {{mb_key object=$acte}}

            <input type="hidden" name="_calcul_montant_base" value="1" />
            <input type="hidden" name="_edit_modificateurs" value="1"/>

            {{mb_field object=$acte field=object_id hidden=true value=$subject->_id}}
            {{mb_field object=$acte field=object_class hidden=true value=$subject->_class}}
            {{mb_field object=$acte field=code_acte hidden=true}}
            {{mb_field object=$acte field=code_activite hidden=true}}
            {{mb_field object=$acte field=code_phase hidden=true}}
            {{mb_field object=$acte field=code_association hidden=true emptyLabel="None"}}
            {{mb_field object=$acte field=executant_id hidden=true value=$codage->praticien_id}}
            {{mb_field object=$acte field=execution hidden=true}}
            {{mb_field object=$acte field=montant_depassement hidden=true}}
            {{mb_field object=$acte field=motif_depassement hidden=true emptyLabel="CActeCCAM-motif_depassement"}}

            {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
              <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked="checked"{{/if}} hidden="hidden" />
            {{/foreach}}

            {{if !$acte->_id}}
              <button class="add notext compact" type="submit" {{if $_activite->anesth_comp && !$_activite->anesth_comp|in_array:$subject->_codes_ccam}}
                      onclick="addActeAnesthComp('{{$_activite->anesth_comp}}');"
              {{/if}}>
                {{tr}}Add{{/tr}}
              </button>
            {{else}}
              <button class="edit notext compact" type="button" onclick="ActesCCAM.edit({{$acte->_id}})">{{tr}}Edit{{/tr}}</button>
              <button class="trash notext compact" type="button"
                      onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}', ajax: '1'},
                        {onComplete: function() {window.urlCodage.refreshModal()}});">
                {{tr}}Delete{{/tr}}
              </button>
            {{/if}}
          </form>
        </td>
      </tr>
    {{/if}}
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
</table>

<ul id="rules-tab" class="control_tabs">
  <li><a href="#questionRules">Informations médicales</a></li>
  <li><a href="#concreteRules">Règles de codage</a></li>
  <li>
    <input type="checkbox" name="_association_mode" value="manuel"
           {{if $codage->association_mode == "user_choice"}}checked="checked"{{/if}}
           onchange="changeCodageMode(this);"/>
    Mode manuel pour les règles d'association
  </li>
</ul>

<hr class="control_tabs" />

<div style="display: none;" id="questionRules">
  <form name="questionRulesForm" action="?" method="post" onsubmit="return false;">
  <table class="tbl">
    <tr>
      <th class="title" colspan="2">Les actes que vous codez répondent-ils à un des critères suivants ?</th>
    </tr>
    <tr>
      <th class="category" colspan="2">Pour les interventions chirurgicales</th>
    </tr>
    {{if isset($codage->_possible_rules.EA|smarty:nodefaults)}}
    <tr>
      <th class="narrow {{if $codage->_possible_rules.EA}}ok{{/if}}">
        <input type="radio" name="_association_question" value="EA"
               {{if $codage->association_rule == "EA"}}checked="checked"{{/if}}
               onchange="setRule(this);"/>
      </th>
      <td>
        Les actes portent sur :
        <ul>
          <li><strong>des membres différents ou</strong></li>
          <li><strong>le tronc et un membre ou</strong></li>
          <li><strong>la tête et un membre.</strong></li>
        </ul>
      </td>
    </tr>
    {{/if}}
    {{if isset($codage->_possible_rules.EB|smarty:nodefaults)}}
    <tr>
      <th class="narrow {{if $codage->_possible_rules.EB}}ok{{/if}}">
        <input type="radio" name="_association_question" value="EB"
               {{if $codage->association_rule == "EB"}}checked="checked"{{/if}}
               onchange="setRule(this);"/>
      </th>
      <td>
        Les actes visent à traiter des <strong>lésions traumatiques multiples et récentes</strong>
      </td>
    </tr>
    {{/if}}
    {{if isset($codage->_possible_rules.EC|smarty:nodefaults)}}
    <tr>
      <th class="narrow {{if $codage->_possible_rules.EC}}ok{{/if}}">
        <input type="radio" name="_association_question" value="EC"
               {{if $codage->association_rule == "EC"}}checked="checked"{{/if}}
               onchange="setRule(this);"/>
      </th>
      <td>
        Les actes décrivent une intervention de <strong>carcinologie ORL</strong> comprenant :
        <ul>
          <li>une exérèse et</li>
          <li>un curage et</li>
          <li>une reconstruction.</li>
        </ul>
      </td>
    </tr>
    {{/if}}
    {{if isset($codage->_possible_rules.EH|smarty:nodefaults)}}
      <tr>
        <th class="narrow {{if $codage->_possible_rules.EH}}ok{{/if}}">
          <input type="radio" name="_association_question" value="EH"
                 {{if $codage->association_rule == "EH"}}checked="checked"{{/if}}
                 onchange="setRule(this);"/>
        </th>
        <td>
          <strong>Des actes ont précédemment été codés pour ce patient dans cette journée</strong> et les nouveaux actes
          sont effectués dans un <strong>temps différent et discontinu</strong> des premiers.
        </td>
      </tr>
    {{/if}}
    <tr>
      <th class="category" colspan="2">Pour les actes d'imagerie</th>
    </tr>
    {{if isset($codage->_possible_rules.ED|smarty:nodefaults)}}
    <tr>
      <th class="narrow {{if $codage->_possible_rules.ED}}ok{{/if}}">
        <input type="radio" name="_association_question" value="ED"
               {{if $codage->association_rule == "ED"}}checked="checked"{{/if}}
               onchange="setRule(this);"/>
      </th>
      <td>
        Les actes sont des actes d'<strong>échographie</strong> portant sur <strong>plusieurs régions anatomiques</strong>.
      </td>
    </tr>
    {{/if}}
    {{if isset($codage->_possible_rules.EE|smarty:nodefaults)}}
    <tr>
      <th class="narrow {{if $codage->_possible_rules.EE}}ok{{/if}}">
        <input type="radio" name="_association_question" value="EE"
               {{if $codage->association_rule == "EE"}}checked="checked"{{/if}}
               onchange="setRule(this);"/>
      </th>
      <td>
        Les actes sont des actes d'<strong>électromyographie</strong>, de <strong>mesure des vitesses de conduction</strong>, d'<strong>étude des latences et des réflexes</strong> portant sur <strong>plusieurs régions anatomiques</strong>.
      </td>
    </tr>
    {{/if}}
    {{if isset($codage->_possible_rules.EF|smarty:nodefaults)}}
    <tr>
      <th class="narrow {{if $codage->_possible_rules.EF}}ok{{/if}}">
        <input type="radio" name="_association_question" value="EF"
               {{if $codage->association_rule == "EF"}}checked="checked"{{/if}}
               onchange="setRule(this);"/>
      </th>
      <td>
        Les actes sont des actes de <strong>scanographie</strong> portant sur <strong>plusieurs régions anatomiques</strong>.
      </td>
    </tr>
    {{/if}}
  </table>
  </form>
</div>
<div style="display: none;" id="concreteRules">
  <form name="formCodageRules" action="?" method="post"
        onsubmit="return onSubmitFormAjax(this, {onComplete: function() {window.urlCodage.refreshModal()}});">
    <input type="hidden" name="m" value="ccam" />
    <input type="hidden" name="dosql" value="do_codageccam_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="codage_ccam_id" value="{{$codage->_id}}" />
    <input type="hidden" name="association_mode" value="{{$codage->association_mode}}" />
    <table class="tbl">
      <tr>
        <th class="title" colspan="20">
          Règles d'association
        </th>
      </tr>
      {{assign var=association_rules value="CCodageCCAM"|static:"association_rules"}}
      {{foreach from=$codage->_possible_rules key=_rulename item=_rule}}
        {{if $_rule || 1}}
          <tr>
            <th class="narrow {{if $_rulename == $codage->association_rule}}ok{{/if}}">
              <input type="radio" name="association_rule" value="{{$_rulename}}"
                     {{if $_rulename == $codage->association_rule}}checked="checked"{{/if}}
                {{if $codage->association_mode == "auto"}}disabled="disabled"{{/if}}
                     onchange="this.form.onsubmit()"/>
            </th>
            <td class="{{if $_rule}}ok{{else}}error{{/if}}">
              {{$_rulename}} {{if $association_rules.$_rulename == 'ask'}}(manuel){{/if}}
            </td>
            <td class="text greedyPane">
              {{tr}}CActeCCAM-regle-association-{{$_rulename}}{{/tr}}
            </td>
          </tr>
        {{/if}}
      {{/foreach}}
    </table>
  </form>
</div>