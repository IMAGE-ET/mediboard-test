{{*
 * View transformations
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=transformation_ruleset}}

<div>
  <button onclick="EAITransformationRuleSet.edit('0');" class="button new">
    {{tr}}CEAITransformationRuleSet-title-create{{/tr}}
  </button>
</div>

<table class="main tbl">
  <tr>
    <th colspan="6" class="title">
        {{tr}}CEAITransformationRuleSet.all{{/tr}}
      </th>
  </tr>
  <tr>
    <th></th>
    <th class="category">
      {{mb_title class=CEAITransformationRuleSet field=name}} -
      {{mb_title class=CEAITransformationRuleSet field=description}}
    </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRuleSet field=_count_active_transformation_rules}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRuleSet field=_count_inactive_transformation_rules}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRuleSet field=_count_transformation_rules}} </th>
  </tr>

  <tbody id="list-transformation-ruleset">
    {{mb_include template=inc_list_transformation_ruleset}}
  </tbody>
</table>

<br/>
<hr />
<br/>

<div id="transformation_rules"></div>