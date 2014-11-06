{{*
 * View transformations rules
 *
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=eai script=transformation_rule ajax=true}}

<div>
  <button onclick="EAITransformationRule.edit('0');" class="button new">
    {{tr}}CEAITransformationRule-title-create{{/tr}}
  </button>

  <button onclick="EAITransformationRule.edit('','{{$transf_ruleset->_id}}',true);" class="button duplicate">
    {{tr}}CEAITransformationRule-button-Duplicate all{{/tr}}
  </button>
</div>

<table class="main tbl">
  <tr>
    <th colspan="13" class="title">
      {{tr}}CEAITransformationRule.all{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="narrow"></th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=name}}</th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=profil}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=message}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=transaction}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=version}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=extension}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=component_from}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=component_to}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=action}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=value}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=active}} </th>
    <th class="category narrow"> {{mb_title class=CEAITransformationRule field=rank}} </th>
  </tr>

  {{foreach from=$transf_rules item=_transformation_rule}}
    <tr {{if !$_transformation_rule->active}}class="opacity-30"{{/if}}>
      <td class="narrow">
        <button class="button edit notext compact" onclick="EAITransformationRule.edit('{{$_transformation_rule->_id}}');"
                title="{{tr}}Edit{{/tr}}">
          {{tr}}Edit{{/tr}}
        </button>
        <button onclick="EAITransformationRule.edit('{{$_transformation_rule->_id}}',
                          '{{$_transformation_rule->eai_transformation_ruleset_id}}', true)"
                class="button notext compact duplicate" type="button" title="{{tr}}Duplicate{{/tr}}">{{tr}}Duplicate{{/tr}}</button>
      </td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="name"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="profil"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="message"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="transaction"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="version"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="extension"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="component_from"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="component_to"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="action"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="value"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="active"}}</td>
      <td class="text compact">{{mb_value object=$_transformation_rule field="rank"}}
        <!-- Order -->
        <form name="formOrderRule-{{$_transformation_rule->_id}}" method="post"
              onsubmit="return EAITransformationRule.onSubmit(this)">

          <input type="hidden" name="dosql" value="do_manage_transformation_rule" />
          <input type="hidden" name="m" value="eai" />
          <input type="hidden" name="ajax" value="1" />
          <input type="hidden" name="transformation_rule_id_move" value="{{$_transformation_rule->_id}}" />
          <input type="hidden" name="direction" value="" />

          <input type="hidden" name="callback"
                 value="EAITransformationRuleSet.refreshTransformationRuleList.curry('{{$_transformation_rule->eai_transformation_ruleset_id}}')" />

          <img src="./images/icons/updown.gif" usemap="#map-{{$_transformation_rule->_id}}" />
          <map name="map-{{$_transformation_rule->_id}}">
            <area coords="0,0,10,7"  href="#1" onclick="$V(this.up('form').direction, 'up');
              EAITransformationRule.moveRowUp(this.up('tr'));   this.up('form').onsubmit();" />
            <area coords="0,8,10,14" href="#1" onclick="$V(this.up('form').direction, 'down');
              EAITransformationRule.moveRowDown(this.up('tr')); this.up('form').onsubmit();" />
          </map>
        </form>
      </td>
    </tr>
    {{foreachelse}}
    <tr><td class="emtpy" colspan="13">{{tr}}CEAITransformationRule.none{{/tr}}</td></tr>
  {{/foreach}}
</table>