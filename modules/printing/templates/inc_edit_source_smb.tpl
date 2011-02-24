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
after_edit_source_smb = function(id) {
  editSource(id, "CSourceSMB");
  refreshList();
}
</script>

<form name="edit_source" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
  <input type="hidden" name="m" value="printing" />
  <input type="hidden" name="dosql" value="do_source_smb_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$source_smb}}
  <input type="hidden" name="callback" value="after_edit_source_smb" />
  
  
  <table class="form">
    <tr>
      {{if $source_smb->_id}}
        <th class="title modify" colspan="4">{{tr}}CSourceSMB-title-modify{{/tr}}</th>
      {{else}}
        <th class="title create" colspan="4">{{tr}}CSourceSMB-title-create{{/tr}}</th>
      {{/if}}
    </tr>
    <tr>
      <td>
        {{mb_label object=$source_smb field=name}}
      </td>
      <td>
        {{mb_field object=$source_smb field=name}}
      </td>
      <td>
        {{mb_label object=$source_smb field=host}}
      </td>
      <td>
        {{mb_field object=$source_smb field=host}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$source_smb field=port}}
      </td>
      <td>
        {{mb_field object=$source_smb field=port}}
      </td>
      <td>
        {{mb_label object=$source_smb field=printer_name}}
      </td>
      <td>
        {{mb_field object=$source_smb field=printer_name size=30}}
      </td>
    </tr>
    <tr>
      <td>
        {{mb_label object=$source_smb field=user}}
      </td>
      <td>
        {{mb_field object=$source_smb field=user}}
      </td>
      <td>
        {{mb_label object=$source_smb field=password}}
      </td>
      <td>
        {{mb_field object=$source_smb field=password}}
      </td>
    </tr>
    <td>
        {{mb_label object=$source_smb field=workgroup}}
      </td>
      <td colspan="3">
        {{mb_field object=$source_smb field=workgroup}}
      </td>
    {{if $source_smb->_id}}
      <tr>
        <td style="padding-top: 20px;">
          <button type="button" class="print" onclick="testPrint('{{$source_smb->_class_name}}','{{$source_smb->_id}}')">
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
        {{if $source_smb->_id}}
          <button class="cancel" onclick="confirmDeletion(this.form, {
            typeName: 'la source SMB',
            objName:'{{$source_smb->name|smarty:nodefaults|JSAttribute}}',
            ajax: true})" type="button">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>