{{mb_script module="dPccam" script="code_ccam" ajax=$ajax}}

<script>
  duplicateCodage = function(codage_id, acte_id) {
    var url = new Url('ccam', 'ajax_duplicate_codage');
    if (codage_id) {
      url.addParam('codage_id', codage_id);
    }
    if (acte_id) {
      url.addParam('acte_id', acte_id);
    }
    url.requestModal();
  }

  changeCodageMode = function(element, codage_id) {
    var codageForm = getForm("formCodageRules_codage-" + codage_id);
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

  setRule = function(element, codage_id) {
    var codageForm = getForm("formCodageRules_codage-" + codage_id);
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

  addActeAnesthComp = function(acte, auto) {
    if (auto || confirm("Voulez vous ajoutez l'acte d'anesthésie complémentaire " + acte + '?')) {
      var on_change = CCAMField{{$subject->_class}}{{$subject->_id}}.options.onChange;
      CCAMField{{$subject->_class}}{{$subject->_id}}.options.onChange = Prototype.emptyFunction;
      CCAMField{{$subject->_class}}{{$subject->_id}}.add(acte, true);
      onSubmitFormAjax(getForm('addActes-{{$subject->_guid}}'));
      CCAMField{{$subject->_class}}{{$subject->_id}}.options.onChange = on_change;
    }
  }

  CCAMSelector.init = function() {
    this.sForm = "addActes-{{$subject->_guid}}";
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

{{if $codages|@count != 1}}
  Main.add(function() {
    Control.Tabs.create('codages-tab', true);
  });
{{/if}}
</script>

<table class="main" style="min-width: 400px; border-spacing: 0px;">
  <tr>
    <th class="title" style="border-bottom: none; border-spacing: 0px;">
      <div style="float: left">
        <form name="addActes-{{$subject->_guid}}" method="post" onsubmit="return false">
          {{if $subject instanceof CConsultation}}
            <input type="hidden" name="m" value="cabinet" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
          {{elseif $subject instanceof COperation}}
            <input type="hidden" name="m" value="planningOp" />
            <input type="hidden" name="dosql" value="do_planning_aed" />
          {{elseif $subject instanceof CDevisCodage}}
            <input type="hidden" name="m" value="ccam" />
            <input type="hidden" name="dosql" value="do_devis_codage_aed" />
          {{else}}
            <input type="hidden" name="m" value="planningOp" />
            <input type="hidden" name="dosql" value="do_sejour_aed" />
          {{/if}}
          {{mb_key object=$subject}}

          <input type="hidden" name="_class" value="{{$subject->_class}}" />
          <input type="hidden" name="_chir" value="{{$subject->_praticien_id}}" />
          {{if ($subject->_class=="COperation")}}
            <input type="hidden" name="_anesth" value="{{$subject->_ref_plageop->anesth_id}}" />
          {{/if}}
          {{mb_field object=$subject field="codes_ccam" hidden=true}}
          <input type="hidden" name="_new_code_ccam" value="" onchange="CCAMField{{$subject->_class}}{{$subject->_id}}.add(this.value, true);"/>

          <button id="didac_actes_ccam_tr_modificateurs" class="search" type="button" onclick="CCAMSelector.init()">
            {{tr}}Search{{/tr}}
          </button>
          <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()" style="width: 12em" value="" class="autocomplete" placeholder="Ajoutez un acte" />
          <div style="text-align: left; color: #000; display: none; width: 200px !important; font-weight: normal; font-size: 11px; text-shadow: none;"
               class="autocomplete" id="_ccam_autocomplete_{{$subject->_guid}}"></div>
          <script>
            Main.add(function() {
              var form = getForm("addActes-{{$subject->_guid}}");
              var url = new Url("ccam", "httpreq_do_ccam_autocomplete");
              {{if $subject->_class == 'CSejour'}}
                url.addParam("date", '{{$subject->_sortie}}');
              {{else}}
                url.addParam("date", '{{$subject->_datetime}}');
              {{/if}}
              url.autoComplete(form._codes_ccam, "_ccam_autocomplete_{{$subject->_guid}}", {
                minChars: 1,
                dropdown: true,
                width: "250px",
                updateElement: function(selected) {
                  CCAMField{{$subject->_class}}{{$subject->_id}}.add(selected.down("strong").innerHTML, true);
                }
              });
              CCAMField{{$subject->_class}}{{$subject->_id}} = new TokenField(form.elements["codes_ccam"], {
                onChange : function() {
                  return onSubmitFormAjax(form, window.urlCodage.refreshModal.bind(window.urlCodage));
                },
                sProps : "notNull code ccam"
              } );
            })
          </script>
        </form>
      </div>

      {{if $codages|@count == 1}}
        <div style="float: right;">
          {{mb_include module=system template=inc_object_history object=$codages|@reset}}
        </div>
      {{/if}}

      Actes du Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticien}}
      {{if $subject->_class == 'CSejour'}}
        {{assign var=codage value=$codages|@reset}}
        le {{'CMbDT::format'|static_call:$codage->date:'%A %d/%m/%Y'}}
      {{/if}}
    </th>
  </tr>
  <tr>
    <th class="title" style="border-top: none; border-spacing: 0px;">
      {{foreach from=$subject->_ext_codes_ccam item=_code}}
        <span id="action-{{$_code->code}}" class="circled" style="background-color: #eeffee; color: black; font-weight: normal; font-size: 0.8em;">
         {{$_code->code}}

          {{if count($_code->assos) > 0}}
            {{unique_id var=uid_autocomplete_comp}}
            <form name="addAssoCode{{$uid_autocomplete_comp}}" method="get" onsubmit="return false;">
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
                    CCAMField{{$subject->_class}}{{$subject->_id}}.add(selected.down("strong").innerHTML, true);
                  }
                });
              });
            </script>
          {{/if}}

          <button type="button" class="trash notext" onclick="CCAMField{{$subject->_class}}{{$subject->_id}}.remove('{{$_code->code}}', true)">
            {{tr}}Delete{{/tr}}
          </button>
      </span>
      {{/foreach}}
    </th>
  </tr>
  <tr>
    <td>
      {{if $codages|@count != 1}}
        {{assign var=total value=0}}
        <ul id="codages-tab" class="control_tabs">
          {{foreach from=$codages item=_codage}}
            {{math assign=total equation="x+y" x=$total y=$_codage->_total}}
            <li>
              <a href="#codage-{{$_codage->_id}}">
                {{tr}}CCodageCCAM.activite_anesth.{{$_codage->activite_anesth}}{{/tr}}&nbsp;&nbsp;
                {{* Le tpl inc_object_history pose problème avec le Control.Tabs et le style des controls tabs (il traite le lien vers l'historique comme un nouveau tab) *}}
                <img src="images/icons/history.gif" style="float:right;" width="16" height="16"
                     onmouseover="ObjectTooltip.createEx(this,'{{$_codage->_guid}}', 'objectViewHistory')"
                     onclick="guid_log('{{$_codage->_guid}}')" />
              </a>
            </li>
          {{/foreach}}
          <li>
            Total activités : {{$total|number_format:2:',':' '}} {{$conf.currency_symbol|html_entity_decode}}
          </li>
        </ul>

        <hr class="control_tabs" />

        {{foreach from=$codages item=_codage}}
          <div style="display: none;" id="codage-{{$_codage->_id}}">
            {{mb_include module=salleOp template=inc_edit_codage codage=$_codage}}
          </div>
        {{/foreach}}
      {{else}}
        {{mb_include module=salleOp template=inc_edit_codage codage=$codages|@reset}}
      {{/if}}
    </td>
  </tr>
</table>