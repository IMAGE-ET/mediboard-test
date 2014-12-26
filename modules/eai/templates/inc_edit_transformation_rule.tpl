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

<style>
  .EAITransformationRule-select {
    width: 100%;
    height: 120px;
    font-size: 10px;
  }
</style>

<script type="text/javascript">
  Main.add(function () {
    {{if $transf_rule && $transf_rule->_id}}
      EAITransformationRule.showVersions('{{$transf_rule->_id}}', '{{$transf_rule->standard}}', '{{$transf_rule->profil}}');
    {{/if}}
  });
</script>

{{if !$mode_duplication}}
<form name="edit-{{$transf_rule->_guid}}{{$transf_rule->_guid}}" method="post" onsubmit="return EAITransformationRule.onSubmit(this)">
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
      <th>{{mb_label object=$transf_rule field="action_type"}}</th>
      <td>
        <input type="hidden" name="action_type" value="{{$transf_rule->action_type}}" onchange="EAITransformationRule.selectedActionType(this, this.value)" />
        {{foreach from=$transf_rule->_specs.action_type->_locales key=_type item=_locale}}
          <button class="transformation-{{$_type}} notext {{if $_type == $transf_rule->action_type}}selected{{/if}}" title="{{$_locale}}" type="button"
                  onclick="$V(this.form.elements.action_type, '{{$_type}}');">
            {{$_locale}}
          </button>
        {{/foreach}}
      </td>
    </tr>

    <tr>
      <td colspan="2">
        <table class="form">
          <tr>
            <td>{{mb_label object=$transf_rule field="standard"}}</td>
            <td>{{mb_label object=$transf_rule field="domain"}}</td>
            <td>{{mb_label object=$transf_rule field="profil"}}</td>
            <td>{{mb_label object=$transf_rule field="transaction"}}</td>
            <td>{{mb_label object=$transf_rule field="message"}}</td>
          </tr>

          <tr>
            <!-- NORME !-->
            <td id="EAITransformationRule-standard" style="width: 20%">
              <select size="10" name="standard" class="EAITransformationRule-select"
                      onchange="EAITransformationRule.fillSelect(this, 'domain');"
                      onclick="EAITransformationRule.showVersions('{{$transf_rule->_id}}', this.value)">
              {{foreach from=$standards item=_standards key=_standard_name}}
                <option value="{{$_standard_name}}" {{if $_standard_name == $transf_rule->standard}}selected{{/if}}>
                  {{tr}}{{$_standard_name}}{{/tr}}
                </option>
              {{/foreach}}
              </select>
            </td>

            <!-- DOMAINE !-->
            <td id="EAITransformationRule-domain" style="width: 20%">
              <select size="10" name="domain" class="EAITransformationRule-select"
                      onchange="EAITransformationRule.fillSelect(this, 'profil');">
                {{foreach from=$standards item=_standards key=_standard_name}}
                    {{foreach from=$_standards item=_domains key=_domain_name}}
                      <option value="{{$_domain_name}}" data-parent="{{$_standard_name}}"
                              {{if $_standard_name == $transf_rule->standard && $_domain_name == $transf_rule->domain}}selected
                              {{else}}style="display: none" {{/if}}>
                        {{if $_domain_name != "none"}}
                          {{tr}}{{$_standard_name}}_{{$_domain_name}}-desc{{/tr}} ({{$_domain_name}})
                        {{else}}
                          {{tr}}EAITransformationRule-{{$_domain_name}}{{/tr}}
                        {{/if}}
                      </option>
                    {{/foreach}}
                {{/foreach}}
              </select>
            </td>

            <!-- PROFIL !-->
            <td id="EAITransformationRule-profil" style="width: 20%">
              <select size="10" name="profil" class="EAITransformationRule-select"
                      onchange="EAITransformationRule.fillSelect(this, 'transaction');"
                      onclick="EAITransformationRule.showVersions('{{$transf_rule->_id}}', this.form.elements.standard.value, this.value)">
                {{foreach from=$standards item=_standards key=_standard_name}}
                  {{foreach from=$_standards item=_domains key=_domain_name}}
                    {{foreach from=$_domains item=_profils key=_profil_name}}
                      <option value="{{$_profil_name}}" data-parent="{{$_standard_name}}-{{$_domain_name}}"
                              {{if $_domain_name == $transf_rule->domain && $_profil_name == $transf_rule->profil}}selected
                              {{else}}style="display: none" {{/if}}>
                        {{if $_profil_name != "none"}}
                          {{tr}}{{$_profil_name}}-desc{{/tr}} ({{$_profil_name}})
                        {{else}}
                          {{tr}}EAITransformationRule-{{$_profil_name}}{{/tr}}
                        {{/if}}
                      </option>
                    {{/foreach}}
                  {{/foreach}}
                {{/foreach}}
              </select>
            </td>

            <!-- TRANSACTION !-->
            <td id="EAITransformationRule-transaction" style="width: 20%">
              <select size="10" name="transaction" class="EAITransformationRule-select"
                      onchange="EAITransformationRule.fillSelect(this, 'message'); ">
                {{foreach from=$standards item=_standards key=_standard_name}}
                  {{foreach from=$_standards item=_domains key=_domain_name}}
                    {{foreach from=$_domains item=_profils key=_profil_name}}
                      {{foreach from=$_profils item=_transactions key=_transaction_name}}
                        <option value="{{$_transaction_name}}" data-parent="{{$_standard_name}}-{{$_domain_name}}-{{$_profil_name}}"
                              {{if $_profil_name == $transf_rule->profil && $_transaction_name == $transf_rule->transaction}}selected
                              {{else}}style="display: none" {{/if}}>
                          {{if $_transaction_name != "none"}}
                            {{tr}}{{$_transaction_name}}{{/tr}} ({{$_transaction_name}})
                          {{else}}
                            {{tr}}EAITransformationRule-{{$_transaction_name}}{{/tr}}
                          {{/if}}
                        </option>
                      {{/foreach}}
                    {{/foreach}}
                  {{/foreach}}
                {{/foreach}}
              </select>
            </td>

            <!-- MESSAGE !-->
            <td id="EAITransformationRule-message" style="width: 20%">
              <select size="10" name="message" class="EAITransformationRule-select">
                {{foreach from=$standards item=_standards key=_standard_name}}
                  {{foreach from=$_standards item=_domains key=_domain_name}}
                    {{foreach from=$_domains item=_profils key=_profil_name}}
                      {{foreach from=$_profils item=_transactions key=_transaction_name}}
                        {{foreach from=$_transactions item=_event key=_event_name}}
                          <option value="{{$_event_name}}" data-parent="{{$_standard_name}}-{{$_domain_name}}-{{$_profil_name}}-{{$_transaction_name}}"
                                  {{if $_profil_name == $transf_rule->profil && $_transaction_name == $transf_rule->transaction && $_event_name == $transf_rule->message}}selected
                                  {{else}}style="display: none" {{/if}}>
                            {{tr}}{{$_event}}{{/tr}}
                          </option>
                        {{/foreach}}
                      {{/foreach}}
                    {{/foreach}}
                  {{/foreach}}
                {{/foreach}}
              </select>
            </td>
          </tr>
        </table>
      </td>

    <tr>
      <th>{{mb_label object=$transf_rule field="version"}}</th>
      <td id="EAITransformationRule-version">
      </td>
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
          <input type="hidden" name="eai_transformation_ruleset_id" value="{{$transformation_ruleset_id}}" />

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