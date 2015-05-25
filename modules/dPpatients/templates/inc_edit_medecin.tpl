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
      <td>{{mb_field object=$object field=sexe}}</td>
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
      <td>{{mb_field object=$object field=type style="width: 13em;"}}</td>
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