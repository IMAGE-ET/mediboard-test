{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 9174 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
resetValue = function(object_id, field_name) {
	var oForm = getForm('editObjectConfig-'+object_id);
	$V(oForm[field_name], "");
}
  
onSubmitObjectConfigs = function(oForm, object_instance_id, object_guid) {
	return onSubmitFormAjax(oForm, { onComplete: refreshConfigObjectValues.curry(object_instance_id, object_guid) });
}
</script>

<form name="editObjectConfig-{{$object->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitObjectConfigs(this, '{{$object->object_id}}', '{{$object->_guid}}') ">
  <input type="hidden" name="m" value="system" />
  <input type="hidden" name="dosql" value="do_config_object_aed" />
  {{mb_key object=$object}}
  <input type="hidden" name="del" value="0" /> 
  <input type="hidden" name="_class_name" value="{{$object->_class_name}}" /> 
  <input type="hidden" name="object_id" value="{{$object->object_id}}" /> 
  <table class="form">
    <tr>
      <th class="category">{{tr}}Name{{/tr}}</th>
      <th class="category" colspan="2">{{tr}}Value{{/tr}}</th>
      <th class="category">{{tr}}Default{{/tr}}</th>
    </tr>
    {{foreach from=$fields key=_field_name item=_field_value}}
    <tr>
      <th>{{mb_label object=$object field=$_field_name}}</th>
      <td><button class="notext cancel" type="button" onclick="resetValue('{{$object->_id}}', '{{$_field_name}}');">{{tr}}Delete{{/tr}}</button></td>
      <td>{{mb_field object=$object field=$_field_name default=null}} </td>
      <td {{if $object->$_field_name !== null}}class="arretee"{{/if}}>
        {{if $object->_default_specs_values}}
          {{mb_value object=$default_config field=$_field_name}}
        {{else}}
          {{mb_value object=$default field=$_field_name}}
        {{/if}}
      </td>
    </tr>
    {{/foreach}}
    <tr>
      <td class="button" colspan="4">
        <button class="submit singleclick" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>