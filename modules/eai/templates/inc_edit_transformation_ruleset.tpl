{{*
 * Edit transformation ruleset
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

<form name="edit{{$transf_ruleset->_guid}}" action="?m={{$m}}" method="post"
      onsubmit="return EAITransformationRuleSet.onSubmit(this)">
  {{mb_key object=$transf_ruleset}}
  {{mb_class object=$transf_ruleset}}
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="EAITransformationRuleSet.refreshList" />

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$transf_ruleset}}

    <tr>
      <th>{{mb_label object=$transf_ruleset field="name"}}</th>
      <td>{{mb_field object=$transf_ruleset field="name"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$transf_ruleset field="description"}}</th>
      <td>{{mb_field object=$transf_ruleset field="description"}}</td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $transf_ruleset->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash"
                  onclick="confirmDeletion(this.form,
                    {typeName:'',objName:'{{$transf_ruleset->_view|smarty:nodefaults|JSAttribute}}', ajax:true})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>