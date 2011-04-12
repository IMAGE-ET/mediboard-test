{{*
 * View Printing Sources
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
after_edit_source_lpr = function(id) {
  editSource(id, "CSourceLPR");
  refreshList();
}
</script>

<form name="edit_source" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="printing" />
  <input type="hidden" name="dosql" value="do_source_lpr_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$source_lpr}}
  <input type="hidden" name="callback" value="after_edit_source_lpr" />
  
  <table class="form">
    <tr>
      {{if $source_lpr->_id}}
        <th class="title modify" colspan="4">{{tr}}CSourceLPR-title-modify{{/tr}}</th>
      {{else}}
        <th class="title create" colspan="4">{{tr}}CSourceLPR-title-create{{/tr}}</th>
      {{/if}}
    </tr>
    <tr>
      <th> {{mb_label object=$source_lpr field=name}} </th>
      <td> {{mb_field object=$source_lpr field=name}} </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$source_lpr field=port}}
      </td>
      <td>
        {{mb_field object=$source_lpr field=port}}
      </td>
      <td>
        {{mb_label object=$source_lpr field=printer_name}}
      </td>
      <td>
        {{mb_field object=$source_lpr field=printer_name size=30}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$source_lpr field=user}}
      </td>
      <td>
        {{mb_field object=$source_lpr field=user}}
      </td>
      <td colspan="2"></td>
    </tr>
    {{if $source_lpr->_id}}
      <tr>
        <td style="padding-top: 20px;">
          <button type="button" class="print" onclick="testPrint('{{$source_lpr->_class_name}}','{{$source_lpr->_id}}')">
            {{tr}}CSourceLPR.test_print{{/tr}}
          </button>
        </td>
        <td colspan="3" id="result_print">
        </td>
      </tr>
    {{/if}}
    <tr>
      <td colspan="4" style="text-align: center">
        <button class="modify">{{tr}}Save{{/tr}}</button>
        {{if $source_lpr->_id}}
          <button class="cancel" onclick="confirmDeletion(this.form, {
            typeName: 'la source LPR',
            objName:'{{$source_lpr->name|smarty:nodefaults|JSAttribute}}',
            ajax: true})" type="button">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>