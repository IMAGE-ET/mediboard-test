{{*
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

<script>
  PatSelector.init = function() {
    this.sForm      = "editFormGrossesse";
    this.sId        = "parturiente_id";
    this.sView      = "_patient_view";
    this.sSexe      = "_patient_sexe";
    this.pop();
  };
</script>

<form name="editFormGrossesse" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="maternite"/>
  {{mb_class object=$grossesse}}
  {{mb_key   object=$grossesse}}
  <input type="hidden" name="callback" value="Grossesse.afterEditGrossesse" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_patient_sexe" value="f" />

  <table class="main">
    <tr>
      <td>
      <table class="form">
        <tr>
          {{mb_include module=system template=inc_form_table_header object=$grossesse}}
        </tr>

        <tr>
          <th>{{mb_label object=$grossesse field=parturiente_id}}</th>
          <td>
            {{mb_field object=$grossesse field=parturiente_id hidden=1}}
            <input type="text" style="cursor: pointer" name="_patient_view" value="{{$grossesse->_ref_parturiente}}" readonly="readonly" {{if !$grossesse->_id}}onclick="PatSelector.init();"{{/if}}/>
          </td>
        </tr>

        <tr>
          <th>
            {{mb_label object=$grossesse field=terme_prevu}}
          </th>
          <td>
            {{mb_field object=$grossesse field=terme_prevu form=editFormGrossesse register=true}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$grossesse field=date_dernieres_regles}}
          </th>
          <td>
            {{mb_field object=$grossesse field=date_dernieres_regles form=editFormGrossesse register=true}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$grossesse field=active}}
          </th>
          <td>
            {{mb_field object=$grossesse field=active}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$grossesse field=multiple}}
          </th>
          <td>
            {{mb_field object=$grossesse field=multiple}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$grossesse field=allaitement_maternel}}
          </th>
          <td>
            {{mb_field object=$grossesse field=allaitement_maternel}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$grossesse field=fausse_couche}}
          </th>
          <td>
            {{mb_field object=$grossesse field=fausse_couche emptyLabel="Aucune"}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$grossesse field=lieu_accouchement}}
          </th>
          <td>
            {{mb_field object=$grossesse field=lieu_accouchement}}
          </td>
        </tr>
        <tr>
          <th>
            {{mb_label object=$grossesse field=rques}}
          </th>
          <td>
            {{mb_field object=$grossesse field=rques form=editFormGrossesse}}
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            {{if $grossesse->_id}}
              <button type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
              <button type="button" class="cancel"
                onclick="confirmDeletion(this.form, {objName: '{{$grossesse}}', ajax: 1})">{{tr}}Delete{{/tr}}</button>
            {{else}}
              <button id="button_create_grossesse" type="button" class="save" onclick="this.form.onsubmit()">{{tr}}Create{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
    {{if $grossesse->_id && $with_buttons}}
      <td>
        <table class="tbl">
          <tr>
            <th class="title">Actions</th>
          </tr>
          <tr>
            <td class="button">
              <button type="button" class="sejour_create" onclick="Tdb.editSejour('', '{{$grossesse->_id}}', '{{$grossesse->parturiente_id}}')">
                {{tr}}CSejour-title-create{{/tr}}
              </button>
              <button type="button" class="consultation_create" onclick="Tdb.editConsult('', '{{$grossesse->_id}}', '{{$grossesse->parturiente_id}}')">
                {{tr}}CConsultation-title-create{{/tr}}
              </button>
            </td>
          </tr>

        </table>
        TEST
      </td>
    {{/if}}
  </tr></table>
</form>
