{{*
 * Edit transformation rule
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

<form name="edit{{$transf_rule->_guid}}" action="?m={{$m}}" method="post"
      onsubmit="return EAITransformationRule.onSubmit(this)">
  {{mb_key object=$transf_rule}}
  {{mb_class object=$transf_rule}}
  <input type="hidden" name="del" value="0" />

  <input type="hidden" name="eai_transformation_ruleset_id" value="{{$transf_rule->eai_transformation_ruleset_id}}" />
  <input type="hidden" name="callback"
         value="EAITransformationRuleSet.refreshTransformationRuleList.curry({{$transf_rule->eai_transformation_ruleset_id}})" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$transf_rule}}

    <tr>
      <th>{{mb_label object=$transf_rule field="name"}}</th>
      <td>{{mb_field object=$transf_rule field="name"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$transf_rule field="active"}}</th>
      <td>{{mb_field object=$transf_rule field="active"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $transf_rule->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash"
                  onclick="confirmDeletion(this.form,
                    {typeName:'',objName:'{{$transf_rule->_view|smarty:nodefaults|JSAttribute}}', ajax:true})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>