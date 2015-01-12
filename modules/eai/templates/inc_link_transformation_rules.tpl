{{*
 * Link transformations rules
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

{{assign var=event_name value=$event|get_class}}

<form name="edit-{{$transformation->_guid}}" method="post" onsubmit="return EAITransformation.onSubmit(this)">
  <input type="hidden" name="m" value="eai" />
  <input type="hidden" name="dosql" value="do_link_transformation_aed" />

  <input type="hidden" name="actor_guid" value="{{$actor->_guid}}" />
  <input type="hidden" name="event_name" value="{{$event_name}}" />
  <input type="hidden" name="del" value="0" />

  <table class="main tbl">
    <tr>
      <th colspan="14" class="title">
        {{tr}}CEAITransformationRule.all{{/tr}}
      </th>
    </tr>
    <tr>
      <th class="narrow button"></th>
      <th class="category"> {{mb_title class=CEAITransformationRule field=name}}</th>
      <th class="category"> {{mb_title class=CEAITransformationRule field=component_from}} </th>
      <th class="category"> {{mb_title class=CEAITransformationRule field=component_to}} </th>
      <th class="category narrow"> {{mb_title class=CEAITransformationRule field=action_type}} </th>
      <th class="category narrow"> {{mb_title class=CEAITransformationRule field=value}} </th>
    </tr>

    {{foreach from=$transf_rules item=_transformation_rule}}
      <tr {{if !$_transformation_rule->active}}class="opacity-30"{{/if}}>
        <td>
          <input type="checkbox" name="transformation_rules[{{$_transformation_rule->_id}}]" value="{{$_transformation_rule->_id}}" />
        </td>
        <td class="text compact">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_transformation_rule->_guid}}');">
            {{mb_value object=$_transformation_rule field="name"}}
          </span>
        </td>
        <td class="text compact">
          {{if $_transformation_rule->component_from}}
            {{foreach from="|"|explode:$_transformation_rule->component_from item=_component}}
              <span class="circled">{{$_component}}</span>
            {{/foreach}}
          {{/if}}
        </td>
        <td class="text compact">
          {{if $_transformation_rule->component_to}}
            {{foreach from="|"|explode:$_transformation_rule->component_to item=_component}}
              <span class="circled">{{$_component}}</span>
            {{/foreach}}
          {{/if}}
        </td>
        <td class="button compact">
          <span class="transformation-{{$_transformation_rule->action_type}}"></span>
        </td>
        <td class="text compact">
          {{mb_value object=$_transformation_rule field="value"}}
        </td>
      </tr>
      {{foreachelse}}
      <tr><td class="emtpy" colspan="14">{{tr}}CEAITransformationRule.none{{/tr}}</td></tr>
    {{/foreach}}

    <tr>
      <td colspan="14" class="button">
        <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>