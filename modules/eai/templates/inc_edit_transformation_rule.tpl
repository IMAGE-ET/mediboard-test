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
    height: 250px;
    font-size: 10px;
  }
</style>

<script type="text/javascript">
  EAITransformationRule.standards_flat = {{$standards_flat|@json}};
  initializeSelect = function(create) {
    EAITransformationRule.fillSelect("standard"   , null   , '{{$transf_rule->standard}}'   , create);
    EAITransformationRule.fillSelect("domain"     , '-desc', '{{$transf_rule->domain}}'     , create);
    EAITransformationRule.fillSelect("profil"     , '-desc', '{{$transf_rule->profil}}'     , create);
    EAITransformationRule.fillSelect("transaction", null   , '{{$transf_rule->transaction}}', create);
    EAITransformationRule.fillSelect("message"    , null   , '{{$transf_rule->message}}'    , create);
  };

  Main.add(function () {
    initializeSelect(true);

    {{if $transf_rule->_id}}
      EAITransformationRule.showVersions('{{$transf_rule->_id}}', '{{$transf_rule->standard}}', '{{$transf_rule->profil}}');

      var flag = false;
      EAITransformationRule.selects.reverse().each(function(select_name) {
        var select = $("EAITransformationRule-"+select_name);

        if (!flag && $V(select)) {
          select.options[select.selectedIndex].onclick();
          flag = true;
        }
      });
      EAITransformationRule.selects.reverse();
    {{/if}}
  });
</script>

{{if !$mode_duplication}}
<form name="editEAITransformationRule" method="post" onsubmit="return EAITransformationRule.onSubmit(this)">
  {{mb_key object=$transf_rule}}
  {{mb_class object=$transf_rule}}
  <input type="hidden" name="del" value="0" />

  <input type="hidden" name="eai_transformation_ruleset_id" value="{{$transf_rule->eai_transformation_ruleset_id}}" />
  <input type="hidden" name="callback"
         value="EAITransformationRuleSet.refreshTransformationRuleList.curry({{$transf_rule->eai_transformation_ruleset_id}})" />

  {{mb_field object=$transf_rule field="component_from" hidden="1"}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$transf_rule}}

    <tr>
      <th style="width: 45%">{{mb_label object=$transf_rule field="name"}}</th>
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
            <td>
              <button type="button" onclick="initializeSelect();"
                      class="cancel notext">{{tr}}Cancel{{/tr}}</button>
              {{mb_label object=$transf_rule field="standard"}}
            </td>
            <td>
              {{mb_label object=$transf_rule field="domain"}}
            </td>
            <td>
              {{mb_label object=$transf_rule field="profil"}}
            </td>
            <td>
              {{mb_label object=$transf_rule field="transaction"}}
            </td>
            <td>
              {{mb_label object=$transf_rule field="message"}}
            </td>
          </tr>

          <tr>
            <!-- NORME !-->
            <td style="width: 20%">
              <select size="10" name="standard" class="EAITransformationRule-select" id="EAITransformationRule-standard">
              </select>
            </td>

            <!-- DOMAINE !-->
            <td style="width: 20%">
              <select size="10" name="domain" class="EAITransformationRule-select" id="EAITransformationRule-domain">
              </select>
            </td>

            <!-- PROFIL !-->
            <td style="width: 20%">
              <select size="10" name="profil" class="EAITransformationRule-select" id="EAITransformationRule-profil">
              </select>
            </td>

            <!-- TRANSACTION !-->
            <td style="width: 20%">
              <select size="10" name="transaction" class="EAITransformationRule-select" id="EAITransformationRule-transaction">
              </select>
            </td>

            <!-- MESSAGE !-->
            <td style="width: 20%">
              <select size="10" name="message" class="EAITransformationRule-select" id="EAITransformationRule-message">
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transf_rule field="version"}}</th>
      <td id="EAITransformationRule-version">
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transf_rule field="component_from"}}</th>
      <td>
        <button type="button" onclick="EAITransformationRule.target(this.form, 'from')"
                class="target notext">{{tr}}Target{{/tr}}</button>
        <span id="EAITransformationRule-component_from">

          {{if $transf_rule->component_from}}
            {{foreach from="|"|explode:$transf_rule->component_from item=_component}}
              <span class="circled">{{$_component}}</span>
            {{/foreach}}
          {{/if}}
        </span>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$transf_rule field="component_to"}}</th>
      <td>
        <button type="button" onclick="EAITransformationRule.target(this.form, 'to')"
                class="target notext">{{tr}}Target{{/tr}}</button>
        <span id="EAITransformationRule-component_to">
          {{if $transf_rule->component_to}}
            {{foreach from="|"|explode:$transf_rule->component_to item=_component}}
              <span class="circled">{{$_component}}</span>
            {{/foreach}}
          {{/if}}
        </span>
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