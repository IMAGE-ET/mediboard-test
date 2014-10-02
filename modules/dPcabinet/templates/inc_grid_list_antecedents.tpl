{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{foreach from=$aides_antecedent.$type item=_aides key=appareil}}
  <table id="{{$type}}-{{$appareil}}" style="display: none; width: 100%" class="tbl">
    <tr>
      <th colspan="1000" class="title">
        <button style="float: right" class="add notext" onclick="$('textarea-ant-{{$type}}-{{$appareil}}').toggle(); this.toggleClassName('remove').toggleClassName('add')">Ajouter</button>
        {{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}}
      </th>
    </tr>
    <tr id="textarea-ant-{{$type}}-{{$appareil}}" style="display: none;">
      <td colspan="1000">
        <form name="addAnt-{{$type}}-{{$appareil}}" method="post">
          <input name="antecedent" size="60"/>
          <button class="submit" type="button" onclick="$V(oFormAntFrmGrid.type, '{{$type}}'); $V(oFormAntFrmGrid.appareil, '{{$appareil}}'); $V(oFormAntFrmGrid.rques, this.form.antecedent.value); $V(this.form.antecedent, '');">Ajouter l'antécédent</button>
        </form>
      </td>
    </tr>
    <tr>
      {{foreach from=$_aides item=curr_aide name=aides}}
      {{if $curr_aide instanceof CAideSaisie}}
      {{if $curr_aide->_owner == "user"}}
        {{assign var=owner_icon value="user"}}
      {{elseif $curr_aide->_owner == "func"}}
        {{assign var=owner_icon value="function"}}
      {{else}}
        {{assign var=owner_icon value="group"}}
      {{/if}}
      {{assign var=i value=$smarty.foreach.aides.index}}
      {{assign var=text value=$curr_aide->text}}
      {{assign var=checked value=$curr_aide->_applied}}
      <td class="text {{if $checked}}opacity-30{{/if}} {{$owner_icon}}"
          style="cursor: pointer; width: {{$width}}%; {{if $checked}}cursor: default;{{/if}}">
        <label onmouseover="ObjectTooltip.createDOM(this, 'tooltip_{{$curr_aide->_guid}}')">
          <input type="checkbox" {{if $checked}}checked disabled{{/if}} id="aide_{{$curr_aide->_guid}}"
                 onclick="addAntecedent(arguments[0] || window.event, '{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '{{$type}}', '{{$appareil}}', this)"/>
          {{$curr_aide->name}}
        </label>
        <div style="display: none" id="tooltip_{{$curr_aide->_guid}}">
          <table class="tbl">
            <tr>
              <th>
                {{$curr_aide->text}}
              </th>
            </tr>
            <tr>
              <td class="button">
                <button type="button" class="edit"
                        onclick="var event = {ctrlKey: true}; addAntecedent(event, '{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '{{$type}}', '{{$appareil}}', $('aide_{{$curr_aide->_guid}}'))">Compléter</button>
              </td>
            </tr>
          </table>
        </div>
      </td>
      {{if ($i % $numCols) == ($numCols-1) && !$smarty.foreach.aides.last}}</tr><tr>{{/if}}
      {{/if}}
      {{/foreach}}
    </tr>
  </table>
{{/foreach}}