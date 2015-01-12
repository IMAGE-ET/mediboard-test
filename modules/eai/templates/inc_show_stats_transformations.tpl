{{*
 * Show transformations stats
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

<table class="tbl">
  <tr>
    <th class="title" colspan="3"> {{$transf_rule->name}} </th>
  </tr>

  <tr>
    <th class="section">{{mb_title class=CEAITransformation field=actor_id}}</th>
    <th class="section">{{mb_title class=CEAITransformation field=standard}}</th>
    <th class="section">{{mb_title class=CEAITransformation field=domain}}</th>
  </tr>

  {{foreach from=$transf_rule->_ref_eai_transformations item=_transformation}}
    {{assign var=actor value=$_transformation->_ref_actor}}
    <tr>
      <td>
        <span onmouseover="ObjectTooltip.createEx(this, '{{$actor->_guid}}');">
           {{$actor->_view}}
         </span>
      </td>
      <td>{{$transf_rule->standard}}</td>
      <td>{{$transf_rule->domain}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="3">
        {{tr}}CEAITransformationRuleSet-msg-transformation not link{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>