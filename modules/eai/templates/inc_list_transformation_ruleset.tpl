{{*
 * View transformations ruleset
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$transf_rulesets item=_transformation_ruleset}}
  <tr>
    <td class="narrow">
      <button class="button edit notext compact" onclick="EAITransformationRuleSet.edit('{{$_transformation_ruleset->_id}}');"></button>
    </td>
    <td class="text compact">
      <a href="#" onclick="EAITransformationRuleSet.onSelect('{{$_transformation_ruleset->_id}}', this.up('tr'));">
        {{$_transformation_ruleset->name}}
      </a>
      <span>{{$_transformation_ruleset->description}}</span>
    </td>
    <td>{{mb_value object=$_transformation_ruleset field="_count_active_transformation_rules"}}</td>
    <td>{{mb_value object=$_transformation_ruleset field="_count_inactive_transformation_rules"}}</td>
    <td>{{mb_value object=$_transformation_ruleset field="_count_transformation_rules"}}</td>
  </tr>
  {{foreachelse}}
  <tr><td class="emtpy" colspan="6">{{tr}}CEAITransformationRuleSet.none{{/tr}}</td></tr>
{{/foreach}}