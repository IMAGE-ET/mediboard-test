{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<script type="text/javascript">
  checkGraph = function() {
    var checkboxes = $$('input[name="_displayGraph"]:checked');

    if (checkboxes.length >= 5) {
      checkboxes = $$('input[name="_displayGraph"]:not(:checked)').each(function(elt) {
        elt.disable();
      });
    }
    else {
      checkboxes = $$('input[name="_displayGraph"]:not(:checked)').each(function(elt) {
        elt.enable();
      });
    }
  };
  
  displayGraph = function() {
    var checkboxes = $$('input[name="_displayGraph"]:checked');
    var selection = [];
    checkboxes.each(function(checkbox) {
      selection.push(checkbox.getAttribute('data-constant'));
    });

    if (selection.length == 0) {
      alert('Vous devez au moins sélectionner une constante!');
      return;
    }

    var url = new Url('patients', 'ajax_custom_constants_graph');
    url.addParam('patient_id', '{{$patient_id}}');
    url.addParam('constants', JSON.stringify(selection));
    url.requestModal();
  };

  displayAllConstantes = function(patient_id) {
    var url = new Url('patients', 'ajax_display_constantes');
    url.addParam('patient_id', patient_id);
    url.requestModal();
  };

  editConstant = function(constant_id) {
    var url = new Url('patients', 'ajax_edit_constantes');
    url.addParam('constant_id', constant_id);
    url.requestModal();
  };

  Main.add(function() {
    var form = getForm('edit-constantes');
    var url = new Url('patients', 'ajax_do_autocomplete_constants');
    url.autoComplete(form._search_constants, '_constants_autocomplete', {
      minChars: 2,
      dropdown: true,
      updateElement: function(selected) {
        var constant = selected.getAttribute('data-constant');
        var row = $$('tr[data-constant="' + constant + '"]');
        if (row.length != 0) {
          row = row[0];
          var table = row.up();
          /* We remove the row, and add to the table to display the node at the bottom of the table */
          row = table.removeChild(row);
          row = table.appendChild(row);
          row.show();
        }
      }
    });

    {{if !$constantes->datetime}}
      var formCst = getForm('edit-constantes');
      formCst.datetime.value = "now";
      formCst.datetime_da.value = "Maintenant";
    {{/if}}
  });
</script>

<form name="edit-constantes" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: loadConstants.curry()});">
  <input type="hidden" name="m" value="dPpatients"/>
  <input type="hidden" name="dosql" value="do_constantes_medicales_aed"/>
  {{if $constantes->_id}}
    <input type="hidden" name="_new_constantes_medicales" value="0"/>
    <input type="hidden" name="constantes_medicales_id" value="{{$constantes->_id}}"/>
  {{else}}
    <input type="hidden" name="_new_constantes_medicales" value="1"/>
   {{/if}}

    {{mb_field object=$constantes field=_unite_ta hidden=1}}
    {{mb_field object=$constantes field=_unite_glycemie hidden=1}}
    {{mb_field object=$constantes field=_unite_cetonemie hidden=1}}
    {{mb_field object=$constantes field=context_class hidden=1}}
    {{mb_field object=$constantes field=context_id hidden=1}}
    {{mb_field object=$constantes field=patient_id hidden=1}}

  <table class="tbl" id="tableConstant" style="width: 1px;">
    <tr>
      <th rowspan="2" class="category narrow">
        <button class="stats notext" type="button" onclick="displayGraph();"></button>
      </th>
      <th rowspan="2" class="category">
        {{tr}}Name{{/tr}}
        <br/>
        <span>
          <input type="text" name="_search_constants" class="autocomplete" placeholder="{{tr}}Search{{/tr}}"/>
        </span>
        <div style="text-align: left; color: #000; display: none; width: 200px !important; font-weight: normal; font-size: 11px; text-shadow: none;" class="autocomplete" id="_constants_autocomplete"></div>
      </th>
      <th colspan="2" class="category" style="border-bottom: none;">
        <button class="save notext" type="submit" style="float: right;"></button>
        <div style="margin-top: 5px;">
          {{tr}}Value{{/tr}}
        </div>
      </th>
      <th rowspan="2" colspan="2" class="category">
        {{tr}}CConstantesMedicales-title-latest_values{{/tr}}
      </th>
      {{if $list_constantes|@count > 0}}
        <th class="category" colspan="{{$list_constantes|@count}}">
          <button type="button" class="list" title="{{tr}}CConstantesMedicales-msg-see_last_values{{/tr}}" onclick="displayAllConstantes('{{$patient_id}}');">
            {{tr}}CConstantesMedicales-msg-see_last_values{{/tr}}
          </button>
        </th>
      {{/if}}
    </tr>
    <tr>
      <th colspan="2" class="category" style="border-top: none;">
        {{mb_field object=$constantes field=datetime form="edit-constantes" register=true}}
      </th>
      {{if $list_constantes|@count > 0}}
        {{foreach from=$list_constantes item=_constantes}}
          <th class="narrow" class="category">
            {{$_constantes->datetime|date_format:"%d/%m/%Y"}}
            <button type="button" class="edit notext" onclick="editConstant('{{$_constantes->_id}}');" title="{{tr}}Edit{{/tr}}"/>
          </th>
        {{/foreach}}
      {{/if}}
    </tr>

    {{assign var=constants_list value="CConstantesMedicales"|static:"list_constantes"}}
    {{assign var=const value=$latest_constantes.0}}
    {{assign var=dates value=$latest_constantes.1}}

    {{foreach from=$selection key=_type item=_ranks}}
      {{foreach from=$_ranks key=_rank item=_constants}}
        {{foreach from=$_constants item=_constant}}
          {{assign var=_params value=$constants_list.$_constant}}
          <tr class="alternate{{if $_rank == 'hidden' && $const->$_constant == ""}} secondary" style="display: none;{{/if}}" data-constant="{{$_constant}}">
            <td class="narrow" style="text-align: center;">
              {{if $_constant[0] != '_'}}
                <input name="_displayGraph" type="checkbox" data-constant="{{$_constant}}" onclick="checkGraph();"/>
              {{/if}}
            </td>
            <td style="text-align: left;">
              <label for="{{$_constant}}" title="{{tr}}CConstantesMedicales-{{$_constant}}-desc{{/tr}}">
                {{tr}}CConstantesMedicales-{{$_constant}}{{/tr}}
              </label>
            </td>
            <td style="text-align: center">
              {{assign var=_hidden value=false}}
              {{assign var=_readonly value=null}}
              {{if array_key_exists('formfields', $_params) && !array_key_exists('readonly', $_params)}}
                {{foreach from=$_params.formfields item=_formfield_name key=_key name=_formfield}}
                  {{assign var=_style value="width:1.7em;"}}
                  {{assign var=_size value=2}}
                  {{if $_params.formfields|@count == 1}}
                    {{assign var=_style value=""}}
                    {{assign var=_size value=3}}
                  {{/if}}

                  {{if !$smarty.foreach._formfield.first}}/{{/if}}
                  {{mb_field object=$constantes field=$_params.formfields.$_key size=$_size style=$_style}}
                {{/foreach}}
              {{else}}
                {{if $_constant.0 == "_" && !array_key_exists('edit', $_params)}}
                  {{assign var=_readonly value='readonly'}}

                  {{if array_key_exists('formula', $_params)}}
                    {{assign var=_hidden value=true}}
                  {{/if}}
                {{/if}}
                {{if array_key_exists('callback', $_params)}}
                  {{assign var=_callback value=$_params.callback}}
                {{else}}
                  {{assign var=_callback value=null}}
                {{/if}}

                {{mb_field object=$constantes field=$_constant size="3" onchange=$_callback|ternary:"$_callback(this.form)":null readonly=$_readonly hidden=$_hidden}}
              {{/if}}
            </td>
            <td style="text-align: center">
              {{if $_params.unit}}
                <span>
                  {{$_params.unit}}
                </span>
              {{/if}}
            </td>
            <td class="narrow" style="text-align: center; font-weight: bold;">
              {{assign var=isnull value=$const->$_constant|is_null}}
              {{if $isnull != '1'}}
                {{if array_key_exists('formfields', $_params) && !array_key_exists('readonly', $_params)}}
                  {{foreach from=$_params.formfields item=_formfield_name key=_key name=_formfield}}
                    {{if !$smarty.foreach._formfield.first}}/{{/if}}
                    {{mb_value object=$const field=$_params.formfields.$_key}}
                  {{/foreach}}
                {{else}}
                  {{mb_value object=$const field=$_constant}}
                {{/if}}

                {{if $_params.unit}}
                  <span>
                    {{$_params.unit}}
                  </span>
                {{/if}}
              {{/if}}
            </td>
            <td class="narrow" style="text-align: center; font-weight: bold;">
              {{$dates.$_constant|date_format:"%d/%m/%Y"}}
            </td>
            {{foreach from=$list_constantes item=_constantes}}
              <td class="narrow" style="text-align: center">
                {{if $_constantes->$_constant != ''}}
                  {{if array_key_exists('formfields', $_params) && !array_key_exists('readonly', $_params)}}
                    {{foreach from=$_params.formfields item=_formfield_name key=_key name=_formfield}}
                      {{if !$smarty.foreach._formfield.first}}/{{/if}}
                      {{mb_value object=$_constantes field=$_params.formfields.$_key}}
                    {{/foreach}}
                  {{else}}
                    {{mb_value object=$_constantes field=$_constant}}
                  {{/if}}

                  {{if $_constantes->$_constant != '' && $_params.unit}}
                    <span>
                      {{$_params.unit}}
                    </span>
                  {{/if}}
                {{/if}}
              </td>
            {{/foreach}}
          </tr>
        {{/foreach}}
      {{/foreach}}
    {{/foreach}}
  </table>
</form>