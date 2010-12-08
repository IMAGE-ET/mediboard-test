{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_form name="editExObject" m="system" dosql="do_ex_object_aed" method="post" onsubmit="return checkForm(this)"}}
  {{*mb_key object=$ex_object*}}
  {{* mb_key is not usable here *}}
  <input type="hidden" name="id" value="{{$ex_object->_id}}" />
  
  {{mb_field object=$ex_object field=object_class}}
  {{mb_field object=$ex_object field=object_id}}
  {{mb_field object=$ex_object field=_ex_class_id}}
  
  <table class="main form">
    <tr>
      <th class="title" colspan="2">
        {{$ex_object->_ref_ex_class}} - {{$object}}
      </th>
    </tr>
    {{foreach from=$ex_object->_ref_ex_class->_ref_fields item=_field}}
    <tr>
      <th>
        {{mb_label object=$ex_object field=$_field->name}}
      </th>
      <td>
        {{mb_field object=$ex_object field=$_field->name register=true increment=true form=editExObject}}
      </td>
    </tr>
    {{/foreach}}
    
    <tr>
      <td></td>
      <td>
        {{if $ex_object->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
                
          <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$ex_object->_view|smarty:nodefaults|JSAttribute}}'})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>

{{/mb_form}}