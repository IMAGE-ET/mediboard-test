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

{{if !$mode_duplication}}
<form name="edit-{{$transf_rule->_guid}}{{$transf_rule->_guid}}" action="?m={{$m}}" method="post"
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
          <button class="trash" type="button" onclick="confirmDeletion(this.form, {
            ajax:1,
            typeName:&quot;{{tr}}{{$transf_rule->_class}}.one{{/tr}}&quot;,
            objName:&quot;{{$transf_rule->_view|smarty:nodefaults|JSAttribute}}&quot;},
            { onComplete: function() {
              Control.Modal.close();
            }})">
            {{tr}}Delete{{/tr}}
          </button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
{{else}}
  <table class="form">
    <tr>
      <th class="title">{{tr}}Duplicate{{/tr}} <strong style="text-decoration: underline">{{$transf_rule->_view}}</strong></th>
    </tr>
    <tr>
      <td class="button">
        <form name="duplicateTransformationRule-{{$transf_rule->_guid}}" action="?m={{$m}}" method="post"
                onsubmit="return EAITransformationRule.onSubmit(this)">
          <input type="hidden" name="m" value="eai" />
          <input type="hidden" name="dosql" value="do_duplicate_transformation_rule_aed" />
          <input type="hidden" name="eai_transformation_rule_id" value="{{$transf_rule->_id}}" />

          <input type="hidden" name="callback"
                 value="EAITransformationRuleSet.refreshTransformationRuleList.curry({{$transf_rule->eai_transformation_ruleset_id}})" />

          {{tr}}at{{/tr}}
          <select name="transformation_ruleset_dest_id">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$transf_rulesets item=_transf_ruleset}}
              <option value="{{$_transf_ruleset->_id}}"
                      {{if $_transf_ruleset->_id == $transf_rule->eai_transformation_ruleset_id}}selected="selected" {{/if}}>
                {{$_transf_ruleset->name}}</option>
            {{/foreach}}
          </select>

          <button type="button" class="submit"
                  onclick="if (this.form.transformation_ruleset_dest_id.value) { this.form.onsubmit(); }">
            {{tr}}Validate{{/tr}}
          </button>
        </form>
      </td>
    </tr>
  </table>
{{/if}}