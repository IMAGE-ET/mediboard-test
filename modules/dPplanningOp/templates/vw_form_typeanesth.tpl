{{*
  * typeanesth form
  *  
  * @category PlanningOp
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<form name="editType" action="?m={{$m}}&amp;tab=vw_edit_typeanesth" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPplanningOp" />
  <input type="hidden" name="dosql" value="do_typeanesth_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$type_anesth}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$type_anesth}}
    <tr>
      <th>{{mb_label object=$type_anesth field="name"}}</th>
      <td>{{mb_field object=$type_anesth field="name"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$type_anesth field="ext_doc"}}</th>
      <td>{{mb_field object=$type_anesth field="ext_doc" emptyLabel="Choose"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$type_anesth field="actif"}}</th>
      <td>{{mb_field object=$type_anesth field="actif"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $type_anesth->_id}}
          <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{objName:'{{$type_anesth->name|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>