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
      <td> <input type="checkbox" name="transformation_rules[]" value="" /></td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="name"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="component_from"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="component_to"}}</td>
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
</table>