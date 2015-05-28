{{*
 * $Id$
 *  
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=patients script=salutation ajax=true}}

<script>
  checkSexAndTitle = function(form) {
    var sex_input   = form.elements.sexe;
    var title_input = form.elements.titre;
    var type_input  = form.elements.type;

    var sex = $V(sex_input);
    var type = $V(type_input);

    switch (sex) {
      case 'm':
        var list = ['mme'];
        if (type == 'medecin') {
          list.push('m');
        }

        disableOptions(title_input, list);
        break;

      case 'f':
        var list = ['m'];
        if (type == 'medecin') {
          list.push('mme');
        }

        disableOptions(title_input, list);
        break;

      default:
        $A(title_input.options).each(function (o) {
          o.disabled = null;
        });
    }

    if (type == 'medecin') {
      $V(title_input, 'dr');
    }
    else {
      switch (sex) {
        case 'm':
          $V(title_input, 'm');
          break;

        case 'f':
          $V(title_input, 'mme');
      }
    }
  };

  disableOptions = function (select, list) {
    $A(select.options).each(function (o) {
      o.disabled = list.include(o.value);
    });

    if (select.value == '' || select.options[select.selectedIndex].disabled) {
      selectFirstEnabled(select);
    }
  };

  selectFirstEnabled = function(select){
    var found = false;

    $A(select.options).each(function (o,i) {
      if (!found && !o.disabled && o.value != '') {
        $V(select, o.value);
        found = true;
      }
    });
  };
</script>

<form method="post" name="editMedecin_{{$object->_id}}" onsubmit="return onSubmitFormAjax(this, {onComplete: Control.Modal.close})">
  <input type="hidden" name="m" value="patients"/>
  <input type="hidden" name="dosql" value="do_medecins_aed"/>
  <input type="hidden" name="del" value="0"/>
  {{mb_key object=$object}}

  <table class="main form">
    {{mb_include module=system template=inc_form_table_header}}

    <tr>
      <th>{{mb_label object=$object field=nom}}</th>
      <td>{{mb_field object=$object field=nom style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=prenom}}</th>
      <td>{{mb_field object=$object field=prenom style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=jeunefille}}</th>
      <td>{{mb_field object=$object field=jeunefille style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=sexe}}</th>
      <td>
        {{if $object->titre == ''}}
          {{mb_field object=$object field=sexe onchange="checkSexAndTitle(this.form);"}}
        {{else}}
          {{mb_field object=$object field=sexe}}
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=titre}}</th>
      <td>
        {{assign var=titre_locales value=$object->_specs.titre}}
        <select name="titre">
          <option value="">&mdash; {{tr}}CMedecin-titre.select{{/tr}}</option>
          {{foreach from=$titre_locales->_locales key=key item=_titre}}
            <option value="{{$key}}" {{if $key == $object->titre}}selected="selected"{{/if}}>
              {{tr}}CMedecin.titre.{{$key}}-long{{/tr}} &mdash; ({{$_titre}})
            </option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=adresse}}</th>
      <td>{{mb_field object=$object field=adresse}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=cp}} {{mb_label object=$object field=ville}}</th>
      <td>{{mb_field object=$object field=cp}} {{mb_field object=$object field=ville}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=tel}}</th>
      <td>{{mb_field object=$object field=tel style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=fax}}</th>
      <td>{{mb_field object=$object field=fax style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=portable}}</th>
      <td>{{mb_field object=$object field=portable style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=email}}</th>
      <td>{{mb_field object=$object field=email style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=email_apicrypt}}</th>
      <td>{{mb_field object=$object field=email_apicrypt style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=type}}</th>
      <td>
        {{if $object->titre == ''}}
          {{mb_field object=$object field=type onchange="checkSexAndTitle(this.form);" style="width: 13em;"}}
        {{else}}
          {{mb_field object=$object field=type style="width: 13em;"}}
        {{/if}}
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=disciplines}}</th>
      <td>{{mb_field object=$object field=disciplines}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=orientations}}</th>
      <td>{{mb_field object=$object field=orientations}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=complementaires}}</th>
      <td>{{mb_field object=$object field=complementaires}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=rpps}}</th>
      <td>{{mb_field object=$object field=rpps style="width: 13em;"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$object field=adeli}}</th>
      <td>{{mb_field object=$object field=adeli style="width: 13em;"}}</td>
    </tr>

    {{if $object->_id}}
      <tr>
        <th class="category" colspan="2">{{tr}}CSalutation.mine{{/tr}}</th>
      </tr>

      <tr>
        <th>{{mb_label object=$object field=_starting_formula}}</th>
        <td class="text compact">{{mb_value object=$object field=_starting_formula style="width: 90%; box-sizing: border-box;"}}</td>
      </tr>

      <tr>
        <th>{{mb_label object=$object field=_closing_formula}}</th>
        <td class="text compact">{{mb_value object=$object field=_closing_formula style="width: 90%; box-sizing: border-box;"}}</td>
      </tr>
    {{/if}}

    <tr>
      <td class="button" colspan="2">
        {{if $object->_id}}
          <button class="save">{{tr}}Edit{{/tr}}</button>

          <button class="search" type="button" onclick="Salutation.manageSalutations('{{$object->_class}}', '{{$object->_id}}');">
            {{tr}}CSalutation-action-Manage salutations{{/tr}}
          </button>

          <button class="print notext" type="button" onclick="Medecin.viewPrint('{{$object->_id}}');">
            {{tr}}Print{{/tr}}
          </button>

          <button class="trash" type="button" onclick="confirmDeletion(this.form,{ajax:true},{onComplete: Control.Modal.close})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="save">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>