{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th>{{$mbobject->_view}}
  {{if $infoPersonnel}}
    {{assign var="pers_id" value=$mbobject->_id}}
    ({{if @$persusersType.$pers_id.op}}
      Aide opératoire
    {{/if}}
    {{if @$persusersType.$pers_id.op && @$persusersType.$pers_id.op_panseuse}} / {{/if}}
    {{if @$persusersType.$pers_id.op_panseuse}}
      Panseuse
    {{/if}})
    {{/if}}
  </th>
  <td>
    <form name="editFrm-{{$mbobject->_guid}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
      <input type="hidden" name="m" value="dPsante400" />
      <input type="hidden" name="dosql" value="do_idsante400_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="id_sante400_id" value="{{$mbobject->_ref_last_id400->_id}}" />
      <input type="hidden" name="object_class" value="{{$mbobject->_class_name}}" />
      <input type="hidden" name="object_id" value="{{$mbobject->_id}}" />
      <input type="hidden" name="tag" value="{{$tag}}" />
      <input type="hidden" name="last_update" value="{{$today}}" />
      {{mb_field object=$mbobject->_ref_last_id400 field="id400"}}
      <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
    </form>
  </td>
</tr>
