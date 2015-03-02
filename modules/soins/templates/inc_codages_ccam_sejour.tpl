{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_script module="dPpmsi" script="PMSI" ajax=$ajax}}
{{mb_script module="dPccam" script="code_ccam" ajax=$ajax}}
{{mb_script module=planningOp script=ccam_selector ajax=true}}

<script type="text/javascript">
  changeDateCodage = function(subject_id, date, direction) {
    var date = Date.fromDATE(date);
    var from, to;
    if (direction == 'left') {
      to = date.addDays(-1).toDATE();
      from = date.addDays(-4).toDATE();
      date = date.addDays(2).toDATE();
    }
    else {
      from = date.addDays(1).toDATE();
      to = date.addDays(4).toDATE();
      date = date.addDays(-2).toDATE();
    }

    loadCodagesCCAM(subject_id, date, from, to);
  };

  editCodages = function(codable_class, codable_id, praticien_id, date) {
    var url = new Url("salleOp", "ajax_edit_codages_ccam");
    url.addParam('codable_class', codable_class);
    url.addParam('codable_id', codable_id);
    url.addParam('praticien_id', praticien_id);
    url.addParam('date', date);
    url.requestModal(
      -10, -50,
      {onClose: loadCodagesCCAM.curry(codable_id,date)}
    );
    window.urlCodage = url;
  };

  lockCodages = function(praticien_id, codable_class, codable_id, date) {
    var url = new Url('ccam', 'ajax_check_lock_codage');
    url.addParam('praticien_id', praticien_id);
    url.addParam('codable_class', codable_class);
    url.addParam('codable_id', codable_id);
    url.addParam('date', date);
    url.addParam('lock', 1);
    url.requestModal(null, null, {onClose: loadCodagesCCAM.curry(codable_id, date)});
  };

  unlockCodages = function(praticien_id, codable_class, codable_id, date) {
    var url = new Url('ccam', 'ajax_check_lock_codage');
    url.addParam('praticien_id', praticien_id);
    url.addParam('codable_class', codable_class);
    url.addParam('codable_id', codable_id);
    url.addParam('date', date);
    url.addParam('lock', 0);
    url.requestModal(null, null, {onClose: loadCodagesCCAM.curry(codable_id, date)});
  };

  deleteCodages = function(praticien_id, date) {
    var forms = $$('form[data-praticien_id="' + praticien_id + '"][data-date="' + date + '"]');
    forms.each(function(form) {
      $V(form.del, 1);
      form.onsubmit();
    });
  };

  deleteAllCodages = function(praticien_id) {
    Modal.confirm('Voulez réellement supprimer tous les codages CCAM de ce praticien?', {
    onOK: function() {
      var forms = $$('form[data-praticien_id="' + praticien_id + '"]');
      forms.each(function(form) {
        $V(form.del, 1);
        form.onsubmit();
      });
    }
  })
  };

  changeCodeToDel = function(subject_id, code_ccam, actes_ids) {
    console.debug('changeCodeToDel');
    var oForm = getForm("manageCodes");
    $V(oForm._selCode, code_ccam);
    $V(oForm._actes, actes_ids);
    ActesCCAM.remove(subject_id);
  };

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

  toggleListCodes = function(code) {
    var main_td = $('main-' + code);
    main_td.toggleClassName('triggerHide');
    main_td.toggleClassName('triggerShow');
    $$('.' + code).each(function(elt) {
      elt.toggle();
    });
  }

  CCAMSelector.init = function() {
    this.sForm = "manageCodes";
    this.sClass = "_class";
    this.sChir = "_chir";
    this.sDate = '{{$subject->_sortie}}';
    this.sView = "_codes_ccam";
    this.pop();
  };


  Main.add(function() {
    var dates = {};
    dates.limit = {
      start: '{{$subject->entree|date_format:"%Y-%m-%d"}}',
      stop: '{{$subject->sortie|date_format:"%Y-%m-%d"}}'
    };

    var oForm = getForm("selectDate");
    if (oForm) {
      Calendar.regField(oForm.date, dates, {noView: true});
    }
  });
</script>

<table class="main layout">
  <tr>
    <td class="halfPane">
      <fieldset id="codages_ccam">
        <legend>Ajouter un executant</legend>
        <form name="newCodage" action="?" method="post"
              onsubmit="return onSubmitFormAjax(this, {
                onComplete: loadCodagesCCAM.curry('{{$subject->_id}}', '{{$date}}')})">
          <input type="hidden" name="m" value="ccam" />
          <input type="hidden" name="dosql" value="do_codageccam_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="codage_ccam_id" value="" />
          <input type="hidden" name="date" value="{{$date}}" />
          <input type="hidden" name="codable_class" value="{{$subject->_class}}" />
          <input type="hidden" name="codable_id" value="{{$subject->_id}}" />
          <select name="praticien_id" style="width: 20em;" onchange="this.form.onsubmit();">
            <option value="">&mdash; Choisir un professionnel de santé</option>
            {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs}}
          </select>
        </form>
        &nbsp;
        <span>{{$date|date_format:"%d/%m/%Y"}}</span>
        <form name="selectDate" action="?" method="get" onsubmit="return false">
          <input type="hidden" name="date" class="date" value="{{$date}}" onchange="loadCodagesCCAM({{$subject->_id}}, this.value);"/>
        </form>
        <table class="tbl">
          <tr>
            <th rowspan="2" class="title" style="vertical-align: middle;">Praticien</th>
            <th colspan="7" class="title" style="border-bottom: none;">
              <button type="button" class="left" style="float: left;" {{if $from == 'CMbDT::date'|static_call:null:$subject->entree}}disabled="disabled"{{/if}}
                      onclick="changeDateCodage({{$subject->_id}}, '{{$from}}', 'left');"></button>
              <button type="button" class="right" style="float: right;" {{if $to == 'CMbDT::date'|static_call:null:$subject->sortie}}disabled="disabled"{{/if}}
                      onclick="changeDateCodage({{$subject->_id}}, '{{$to}}', 'right');"></button>
              Codage CCAM
              <br/>
              <span style="font-size: 11px;">{{$subject->_shortview|replace:"Du":"Séjour du"}}</span>
            </th>
          </tr>
          <tr>
            {{foreach from=$days item=_day}}
              <th class="selected">
                {{$_day|date_format:'%a %d/%m/%Y'}}
              </th>
            {{/foreach}}
          </tr>
          {{foreach from=$subject->_ref_codages_ccam key=_praticien_id item=_codages_by_prat name=codages_by_prat}}
            <tr{{if !$smarty.foreach.codages_by_prat.last}} style="border-bottom: 1pt dotted #93917e;"{{/if}}>
              <td>
                {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$praticiens[$_praticien_id]}}
              </td>
              {{foreach from=$days item=_day}}
                <td style="text-align: center;">
                  {{if array_key_exists($_day, $_codages_by_prat)}}
                    {{assign var=count_actes value=0}}
                    {{assign var=total value=0}}
                    {{foreach from=$_codages_by_prat.$_day item=_codage name=codages_by_day}}
                      {{assign var=codage_locked value=$_codage->locked}}
                      {{math assign=count_actes equation="x+y" x=$count_actes y=$_codage->_ref_actes_ccam|@count}}
                      {{math assign=total equation="x+y" x=$total y=$_codage->_total}}
                      {{if $_codage->_ref_actes_ccam|@count != 0}}
                        <form name="formCodage-{{$_praticien_id}}-{{$_day}}_{{$_codage}}" data-date="{{$_codage->date}}" data-praticien_id="{{$_codage->praticien_id}}" action="?" method="post"
                        onsubmit="return onSubmitFormAjax(this{{if $smarty.foreach.codages_by_day.first}}
                                      , {
                                        onComplete: loadCodagesCCAM.curry({{$_codage->codable_id}},'{{$_codage->date}}')}{{/if}});">
                          <input type="hidden" name="m" value="ccam" />
                          <input type="hidden" name="dosql" value="do_codageccam_aed" />
                          <input type="hidden" name="del" value="0" />
                          <input type="hidden" name="locked" value="{{$_codage->locked}}"/>
                          {{mb_key object=$_codage}}

                          <div style="position: relative; min-height: 22px; vertical-align: middle;{{if $smarty.foreach.codages_by_day.first && !$smarty.foreach.codages_by_day.last}}border-bottom: 1pt dotted #93917e;{{/if}}">
                            <span style="position: absolute; right: 0%; top: 40%; height: 20px; margin-top: -10px; float: right;">
                              <button type="button" class="notext copy" onclick="duplicateCodage({{$_codage->_id}});" title="{{tr}}CCodageCCAM-action-duplicate{{/tr}}">
                                {{tr}}CCodageCCAM-action-duplicate{{/tr}}
                              </button>
                            </span>
                            <span onclick="editCodages('{{$subject->_class}}', {{$subject->_id}}, {{$_codage->praticien_id}}, '{{$_day}}');"
                                  onmouseover="ObjectTooltip.createEx(this, '{{$_codage->_guid}}');" style="font-size: 0.85em;">
                                {{foreach from=$_codage->_ref_actes_ccam item=_act name=codages}}
                                  {{if !$smarty.foreach.codages.first || !$smarty.foreach.codages_by_day.first}}
                                    <br/>
                                  {{/if}}
                                {{$_act->code_acte}} <span class="circled ok">{{$_act->code_activite}}-{{$_act->code_phase}}</span>
                                {{/foreach}}
                                {{if $smarty.foreach.codages_by_day.last && $count_actes == 0}}
                                  {{tr}}CActeCCAM.none{{/tr}}
                                {{/if}}
                            </span>
                          </div>
                        </form>
                      {{/if}}
                    {{/foreach}}

                    {{if $total != 0}}
                      <div style="font-size: 0.85em;">
                        Total : {{$total|number_format:2:',':' '}} {{$conf.currency_symbol|html_entity_decode}}
                      </div>
                    {{/if}}

                    <div{{if $count_actes !=0}} style="border-top: 1pt dotted #93917e;"{{/if}}>
                      {{if !$codage_locked}}
                        <button type="button" class="notext edit" onclick="editCodages('{{$subject->_class}}', {{$subject->_id}}, {{$_praticien_id}}, '{{$_day}}')"
                                title="{{tr}}Edit{{/tr}}">
                          {{tr}}Edit{{/tr}}
                        </button>
                      {{/if}}

                      {{if $codage_locked}}
                        <button type="button" class="notext unlock"
                                onclick="unlockCodages({{$_codage->praticien_id}}, '{{$_codage->codable_class}}', {{$_codage->codable_id}}, '{{$_day}}')">
                          {{tr}}Unlock{{/tr}}
                        </button>
                      {{else}}
                        <button type="button" class="notext lock"
                                onclick="lockCodages({{$_codage->praticien_id}}, '{{$_codage->codable_class}}', {{$_codage->codable_id}}, '{{$_day}}')">
                          {{tr}}Lock{{/tr}}
                        </button>
                      {{/if}}

                      {{if $count_actes == 0}}
                        <button type="button" class="notext trash"
                                onclick="deleteCodages({{$_praticien_id}}, '{{$_day}}')">.
                          {{tr}}Delete{{/tr}}
                        </button>
                      {{/if}}
                    </div>
                  {{else}}
                    <form name="formCodage-{{$_praticien_id}}-{{$_day}}" action="?" method="post"
                          onsubmit="return onSubmitFormAjax(this, {
                                      onComplete: function() {
                                        loadCodagesCCAM({{$subject->_id}},'{{$_day}}');
                                        editCodages('{{$subject->_class}}', {{$subject->_id}}, {{$_praticien_id}}, '{{$_day}}');}});">
                      <input type="hidden" name="m" value="ccam" />
                      <input type="hidden" name="dosql" value="do_codageccam_aed" />
                      <input type="hidden" name="del" value="0" />
                      <input type="hidden" name="codage_ccam_id" value="" />
                      <input type="hidden" name="codable_class" value="{{$subject->_class}}" />
                      <input type="hidden" name="codable_id" value="{{$subject->_id}}" />
                      <input type="hidden" name="praticien_id" value="{{$_praticien_id}}" />
                      <input type="hidden" name="date" value="{{$_day}}" />

                      <button type="submit" class="add notext" title=""></button>
                    </form>
                  {{/if}}
                </td>
              {{/foreach}}
            </tr>
          {{foreachelse}}
            <tr>
              <td class="empty" colspan="10">{{tr}}CCodageCCAM.none{{/tr}}</td>
            </tr>
          {{/foreach}}
        </table>
      </fieldset>
    </td>
    <td class="halfPane">
      <fieldset id="didac_inc_manage_codes_fieldset_code">
        <legend id="didac_actes_ccam_execution">Ajouter un code</legend>
        <form name="manageCodes" method="post">
          <input type="hidden" name="m" value="planningOp" />
          <input type="hidden" name="dosql" value="do_sejour_aed" />
          <input name="_actes" type="hidden" value="" />
          <input name="_selCode" type="hidden" value="" />
          {{mb_key object=$subject}}

          <input type="hidden" name="_class" value="{{$subject->_class}}" />
          <input type="hidden" name="_chir" value="{{$subject->_praticien_id}}" />

          <button id="didac_actes_ccam_tr_modificateurs" class="search" type="button" onclick="CCAMSelector.init()">
            {{tr}}Search{{/tr}}
          </button>

          {{mb_field object=$subject field="codes_ccam" hidden=true onchange="this.form.onsubmit()"}}
          <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()" style="width: 12em" value="" class="autocomplete" placeholder="Ajoutez un acte" />
          <div style="text-align: left; color: #000; display: none; width: 200px !important; font-weight: normal; font-size: 11px; text-shadow: none;"
               class="autocomplete" id="_ccam_autocomplete_{{$subject->_guid}}"></div>
          <script>
            Main.add(function() {
              var form = getForm("manageCodes");
              var url = new Url("ccam", "httpreq_do_ccam_autocomplete");
              url.addParam("date", '{{$subject->_sortie}}');
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
                  return onSubmitFormAjax(form, {onComplete: loadCodagesCCAM.curry('{{$subject->_id}}', '{{$date}}')});
                },
                sProps : "notNull code ccam"
              } );
            })
          </script>
        </form>
        <table class="tbl">
          <tr>
            <th class="category" colspan="10">Actes disponibles</th>
          </tr>

          {{foreach from=$ext_codes_ccam item=_ext_code name=ext_codes_ccam}}
            {{assign var=_code value=$_ext_code.codes[0]}}
            {{unique_id var=uid_autocomplete_asso}}
            <tr {{if !$smarty.foreach.ext_codes_ccam.first}}style="border-top: 1pt dotted #93917e;"{{/if}}{{if $_ext_code.count > 1}} onclick="toggleListCodes('{{$_code->code}}');"{{/if}}>
              <td id="main-{{$_code->code}}"{{if $_ext_code.count > 1}} class="triggerShow" style="padding-left: 20px;"{{/if}}>
                <a href="#" onclick="CodeCCAM.show('{{$_code->code}}', '{{$subject->_class}}');">
                  {{if $_ext_code.count > 1}}
                    {{$_ext_code.count}} x
                  {{/if}}{{$_code->code}}
                </a>
              </td>
              <td>
                {{foreach from=$_code->activites item=_activite}}
                  {{foreach from=$_activite->phases item=_phase}}
                    <span class="circled{{if $_ext_code.count <= 1}} {{if $_phase->_connected_acte->_id}}ok{{else}}error{{/if}}{{/if}}" style="background-color: #eeffee;">
                      {{$_activite->numero}}-{{$_phase->phase}}
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
                          CCAMField{{$subject->_class}}{{$subject->_id}}.add(selected.down("strong").innerHTML, true);
                        }
                      });
                    });
                  </script>
                {{/if}}
              </td>
              <td>
                {{if $_ext_code.count == 1}}
                  <button type="button" class="notext trash" onclick="CCAMField{{$subject->_class}}{{$subject->_id}}.remove('{{$_code->code}}', true);">
                    {{tr}}Delete{{/tr}}
                  </button>
                {{/if}}
              </td>
            </tr>
            {{foreach from=$_ext_code.codes item=_code name=codes_ccam}}
              <tr class="{{$_code->code}}" style="display:none;">
                <td></td>
                <td>
                  {{assign var=can_delete value=1}}
                  {{foreach from=$_code->activites item=_activite}}
                    {{foreach from=$_activite->phases item=_phase}}
                      {{if $can_delete && $_phase->_connected_acte->_id}}
                        {{assign var=can_delete value=0}}
                      {{/if}}
                      <span class="circled {{if $_phase->_connected_acte->_id}}ok{{else}}error{{/if}}">
                        {{$_activite->numero}}-{{$_phase->phase}}
                      </span>
                    {{/foreach}}
                  {{/foreach}}
                </td>
                <td></td>
                <td></td>
                <td>
                  <button type="button" class="notext trash" {{if !$can_delete}}disabled="disabled"{{/if}} onclick="CCAMField{{$subject->_class}}{{$subject->_id}}.remove('{{$_code->code}}', true);">
                    {{tr}}Delete{{/tr}}
                  </button>
                </td>
              </tr>
            {{/foreach}}
          {{/foreach}}
        </table>
      </fieldset>
    </td>
  </tr>
</table>