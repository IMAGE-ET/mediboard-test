{{*
 * $Id$
 *  
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=obj_guid value=$subject->_guid}}

<script>

  editCodage = function(codable_class, codable_id, praticien_id) {
    var url = new Url("salleOp", "ajax_edit_codages_ccam");
    url.addParam('codable_class', codable_class);
    url.addParam("codable_id", codable_id);
    url.addParam('praticien_id', praticien_id);
    url.requestModal(
      -10, -50,
      {onClose: PMSI.reloadActesCCAM.curry('{{$obj_guid}}')}
    );
    window.urlCodage = url;
  };

  Main.add(function() {
    // Mise à jour du compteur et de la classe du volet correspondant
    var span = $("count_actes_{{$obj_guid}}");
    span.update("{{$subject->_count_actes}}");
    if ({{$subject->_count_actes}} == 0) {
      span.up("a").addClassName("empty");
    }
    else {
      span.up("a").removeClassName("empty");
    }
  });
</script>

<table class="tbl">
  <tr>
    <th class="title" colspan="20" style="border-bottom: none;">
      <form name="addActes-{{$obj_guid}}" method="post" onsubmit="return false;">
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

        {{if !$read_only}}
          <div style="float: left">
            {{mb_field object=$subject field="codes_ccam" hidden=true onchange="this.form.onsubmit()"}}
            <input type="text" name="_codes_ccam" ondblclick="CCAMSelector.init()" style="width: 12em" value="" class="autocomplete" placeholder="Ajoutez un acte" />
            <div style="text-align: left; color: #000; display: none; width: 200px !important; font-weight: normal; font-size: 11px; text-shadow: none;"
                 class="autocomplete" id="_ccam_autocomplete_{{$subject->_guid}}"></div>
            <script>
              Main.add(function() {
                var form = getForm("addActes-{{$obj_guid}}");
                var url = new Url("ccam", "httpreq_do_ccam_autocomplete");
                url.autoComplete(form._codes_ccam, "_ccam_autocomplete_{{$obj_guid}}", {
                  minChars: 1,
                  dropdown: true,
                  width: "250px",
                  updateElement: function(selected) {
                    CCAMField{{$subject->_class}}{{$subject->_id}}.add(selected.down("strong").innerHTML, true);
                  }
                });
                CCAMField{{$subject->_class}}{{$subject->_id}} = new TokenField(form.elements["codes_ccam"], {
                  onChange : function() {
                    return onSubmitFormAjax(form, PMSI.reloadActesCCAM.curry('{{$obj_guid}}'))
                  },
                  sProps : "notNull code ccam"
                } );
              })
            </script>
          </div>
        {{/if}}
        {{tr}}CActeCCAM{{/tr}}
      </form>
    </th>
  </tr>
  {{if !$read_only}}
    <tr>
      <th class="title" colspan="11" style="border-top: none;">
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
                      CCAMField{{$subject->_class}}{{$subject->_id}}.add(selected.down("strong").innerHTML);
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
  {{/if}}
  <tr>
    <th class="narrow">{{mb_title class=CActeCCAM field=code_acte}}</th>
    <th colspan="2" class="narrow">{{mb_title class=CActeCCAM field=code_activite}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=executant_id}}</th>
    <th>{{mb_title class=CActeCCAM field=modificateurs}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=execution}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=montant_depassement}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=motif_depassement}}</th>
    <th class="narrow">{{mb_title class=CActeCCAM field=code_association}}</th>
    <th>{{mb_title class=CActeCCAM field=_tarif}}</th>
    {{if !$read_only}}
      <th class="narrow">Actions</th>
    {{/if}}
  </tr>
  {{foreach from=$subject->_ext_codes_ccam item=_code key=_key}}
    {{foreach from=$_code->activites item=_activite}}
      {{foreach from=$_activite->phases item=_phase}}
        {{assign var="acte" value=$_phase->_connected_acte}}
        {{assign var="view" value='PMSI-'|cat:$acte->_id|default:$acte->_view}}
        {{assign var="key" value="$_key$view"}}
        <tr>
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
          <td>
            {{if $read_only}}
              {{mb_value object=$acte field=executant_id}}
            {{else}}
              {{mb_field object=$acte field=executant_id options=$listPrats onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
              {{if $conf.dPccam.CCodeCCAM.use_new_association_rules && $acte->executant_id && $acte->_id}}
                <button type="button" class="notext edit" onclick="editCodage('{{$subject->_class}}', {{$subject->_id}}, {{$acte->executant_id}})"
                        title="Modifier le codage">
                  {{tr}}Edit{{/tr}}
                </button>
              {{/if}}
            {{/if}}
          </td>
          <td class="greedyPane text">
            {{assign var=nb_modificateurs value=$acte->modificateurs|strlen}}
            {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
              <span class="circled {{if $_mod->_state == 'prechecked'}}ok{{elseif $_mod->_checked && in_array($_mod->_state, array('not_recommended', 'forbidden'))}}error{{elseif in_array($_mod->_state, array('not_recommended', 'forbidden'))}}warning{{/if}}"
                    title="{{$_mod->libelle}} ({{$_mod->_montant}})">
                        {{if !$read_only}}
                          <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked{{elseif $nb_modificateurs == 4 || $_mod->_state == 'forbidden' || (intval($acte->_exclusive_modifiers) > 0 && in_array($_mod->code, array('F', 'U', 'P', 'S')))}}disabled="disabled"{{/if}}
                                 data-acte="{{$view}}" data-code="{{$_mod->code}}" data-double="{{$_mod->_double}}" class="modificateur" onchange="CCodageCCAM.syncCodageField(this, '{{$view}}');" />
                        {{/if}}
                <label for="modificateur_{{$_mod->code}}{{$_mod->_double}}">
                  {{$_mod->code}}
                </label>
                      </span>
              {{foreachelse}}
              <em>{{tr}}None{{/tr}}</em>
            {{/foreach}}
          </td>
          <td>
            {{if $read_only}}
              {{mb_value object=$acte field=execution}}
            {{else}}
              <form name="codageActeExecution-{{$view}}" action="?" method="post" onsubmit="return false;">
                {{mb_field object=$acte field=execution form="codageActeExecution-$view" register=true onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
              </form>
            {{/if}}
          </td>
          <td>
            {{if $read_only}}
              {{mb_value object=$acte field=montant_depassement}}
            {{else}}
              <form name="codageActeMontantDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
                {{mb_field object=$acte field=montant_depassement onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
              </form>
            {{/if}}
          </td>
          <td>
            {{if $read_only}}
              {{mb_value object=$acte field=motif_depassement}}
            {{else}}
              <form name="codageActeMotifDepassement-{{$view}}" action="?" method="post" onsubmit="return false;">
                {{mb_field object=$acte field=motif_depassement emptyLabel="CActeCCAM-motif_depassement" onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
              </form>
            {{/if}}
          </td>
          <td
            {{if $acte->_id && ($acte->code_association != $acte->_guess_association)}}style="background-color: #fc9"{{/if}}>
            {{if $read_only}}
              {{mb_value object=$acte field=code_association}}
            {{else}}
              {{if $acte->_id}}
                <form name="codageActeCodeAssociation-{{$view}}" action="?" method="post" onsubmit="return false;">
                  {{mb_field object=$acte field=code_association emptyLabel="CActeCCAM.code_association." onchange="CCodageCCAM.syncCodageField(this, '$view');"}}
                </form>
                {{if $acte->code_association != $acte->_guess_association}}
                  ({{$acte->_guess_association}})
                {{/if}}
              {{/if}}
            {{/if}}
          </td>
          <td {{if $acte->_id && !$acte->facturable}}style="background-color: #fc9"{{/if}}>
            {{mb_value object=$acte field=_tarif}}
          </td>
          {{if !$read_only}}
            <td class="button">
              <form name="codageActe-{{$view}}" action="?" method="post"
                    onsubmit="return onSubmitFormAjax(this, PMSI.reloadActesCCAM.curry('{{$obj_guid}}'))">
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
                {{mb_field object=$acte field=executant_id hidden=true}}
                {{mb_field object=$acte field=execution hidden=true}}
                {{mb_field object=$acte field=montant_depassement hidden=true}}
                {{mb_field object=$acte field=motif_depassement hidden=true emptyLabel="CActeCCAM-motif_depassement"}}

                {{foreach from=$_phase->_modificateurs item=_mod name=modificateurs}}
                  <input type="checkbox" name="modificateur_{{$_mod->code}}{{$_mod->_double}}" {{if $_mod->_checked}}checked{{/if}} hidden />
                {{/foreach}}

                {{if !$acte->_id}}
                  <button class="add notext compact" type="submit">
                    {{tr}}Add{{/tr}}
                  </button>
                {{else}}
                  <button class="edit notext compact" type="button" onclick="CCodageCCAM.editActe({{$acte->_id}}, {{$subject->_id}})">{{tr}}Edit{{/tr}}</button>
                  <button class="trash notext compact" type="button"
                          onclick="confirmDeletion(this.form,{typeName:'l\'acte',objName:'{{$acte->_view|smarty:nodefaults|JSAttribute}}', ajax: '1'},
                            {onComplete: PMSI.reloadActesCCAM.curry('{{$obj_guid}}')});">
                    {{tr}}Delete{{/tr}}
                  </button>
                {{/if}}
              </form>
            </td>
          {{/if}}
        </tr>
      {{/foreach}}
    {{/foreach}}
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="20">{{tr}}CActeCCAM.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>