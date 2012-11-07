{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$logs item=_log}}
<tbody class="hoverable">
  
  <tr {{if $_log->type != "store"}} style="font-weight: bold" {{/if}}>
    {{assign var=field_count value=$_log->_fields|@count}}
    {{if !$dialog}}
    <td rowspan="{{$field_count}}">
      <label title="{{$_log->object_class}}">
        {{tr}}{{$_log->object_class}}{{/tr}}
      </label>
      ({{$_log->object_id}})
    </td>
    <td rowspan="{{$field_count}}" class="text">
      {{assign var=ref_object value=$_log->_ref_object}}
      {{if $ref_object->_id}} 
        <label onmouseover="ObjectTooltip.createEx(this, '{{$ref_object->_guid}}');">
          {{$ref_object}}
        </label>
      {{else}}
        {{$ref_object}}
        {{if $_log->extra}}
          - {{$_log->extra}}
        {{/if}}
      {{/if}}
    </td>
    <td rowspan="{{$field_count}}">{{mb_value object=$_log field=ip_address}}</td>
    {{/if}}
    <td rowspan="{{$field_count}}" style="text-align: center;">
      <label onmouseover="ObjectTooltip.createEx(this, '{{$_log->_ref_user->_guid}}');">
        {{mb_ditto name=user value=$_log->_ref_user->_view}}
      </label>
    </td>
    <td rowspan="{{$field_count}}" style="text-align: center;">
      {{mb_ditto name=date value=$_log->date|date_format:$conf.date}}
    </td>
    <td rowspan="{{$field_count}}" style="text-align: center;">
      <span title="{{$_log->date|iso_time}}">{{mb_ditto name=time value=$_log->date|date_format:$conf.time}}</span>
    </td>
    <td rowspan="{{$field_count}}"
      {{if !count($_log->_fields) && $object->_id}} colspan="4" {{/if}}>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_log->_guid}}')">
        {{mb_value object=$_log field=type}}
      </span>
    </td>
    
    <!-- Valeurs de champs-->
    {{if $object->_id}}
      {{foreach from=$_log->_fields item=_field name=field}}
        <td class="text" style="font-weight: normal;">
          {{if array_key_exists($_field, $object->_specs)}}
            {{if $object->_specs[$_field]->derived}}
  	          <td colspan="3" style="display: none;"></td>
    				{{else}}
              {{mb_label object=$object field=$_field}}
            {{/if}}
          {{else}}
            {{tr}}CMbObject.missing_spec{{/tr}} ({{$_field}})
          {{/if}}
        </td>
  
        {{if array_key_exists($_field,$_log->_old_values)}}
          <td class="text" style="font-weight: normal;">
            {{assign var=old_value value=$_log->_old_values.$_field}}
            {{mb_value object=$object field=$_field value=$old_value tooltip=1}}
          </td>
          <td class="text" style="font-weight: normal;">
            {{assign var=log_id value=$_log->_id}}
            {{assign var=new_value value=$object->_history.$log_id.$_field}}
            <strong>
              {{*
                Pour le log le plus récent, si c'est un champ qui a été supprimé (donc qui vaut null) :
                dans la fonction mb_value le champ value est utilisé seulement s'il est différent de null.
                Donc affectation d'une chaîne vide au lieu de null
              *}}
              {{if $new_value === null}}
                {{assign var=new_value value=""}}
              {{/if}}
              {{mb_value object=$object field=$_field value=$new_value tooltip=1}}
            </strong>
          </td>
        {{else}}
          <td colspan="3"  class="empty" style="font-weight: normal;">{{tr}}Unavailable information{{/tr}}</td>
        {{/if}}
      {{if !$smarty.foreach.field.last}}</tr><tr>{{/if}}
      {{/foreach}}
    {{else}}
      <td class="text">
      {{if $_log->object_class|strpos:"CExObject_" === false}} {{* Because the object can't be instanciated *}}
        {{foreach from=$_log->_fields item=_field name=field}}
         {{if array_key_exists($_field, $ref_object->_specs)}}
           {{mb_label class=$_log->object_class field=$_field}}
           {{if !$smarty.foreach.field.last}} - {{/if}}
         {{else}}
           {{tr}}CMbObject.missing_spec{{/tr}} ({{$_field}})
         {{/if}}
        {{/foreach}}
      {{/if}}
      </td>

    {{/if}}

  </tr>
</tbody>
{{foreachelse}}
<tr>
  <td colspan="20" class="empty">{{tr}}CUserLog.none{{/tr}}</td>
</tr>
{{/foreach}}
