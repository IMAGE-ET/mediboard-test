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
        {{if !"dPpatients CAntecedent show_form_add_atcd"|conf:"CGroups-$g"}}
          <button style="float: right" class="add notext" onclick="$('textarea-ant-{{$type}}-{{$appareil}}').toggle(); this.toggleClassName('remove').toggleClassName('add')">Ajouter</button>
        {{/if}}
        {{tr}}CAntecedent.appareil.{{$appareil}}{{/tr}}
      </th>
    </tr>
    <tr id="textarea-ant-{{$type}}-{{$appareil}}" {{if !"dPpatients CAntecedent show_form_add_atcd"|conf:"CGroups-$g"}}style="display: none;"{{/if}}>
      <td colspan="1000">
        <form name="addAnt-{{$type}}-{{$appareil}}" method="post">
          <input name="antecedent" size="60"/>
          <button class="submit" type="button" onclick="$V(oFormAntFrmGrid.type, '{{$type}}'); $V(oFormAntFrmGrid.appareil, '{{$appareil}}'); $V(oFormAntFrmGrid.rques, this.form.antecedent.value); $V(this.form.antecedent, '');">Ajouter l'antécédent</button>
        </form>
      </td>
    </tr>
    {{foreach from=$_aides item=aides_by_line}}
      <tr>
        {{foreach from=$aides_by_line item=curr_aide}}
          {{if $curr_aide instanceof CAideSaisie}}
            {{assign var=owner_icon value="group"}}
            {{if $curr_aide->_owner == "user"}}
              {{assign var=owner_icon value="user"}}
            {{elseif $curr_aide->_owner == "func"}}
              {{assign var=owner_icon value="function"}}
            {{/if}}
            {{assign var=text value=$curr_aide->text}}
            {{assign var=checked value=$curr_aide->_applied}}
            <td class="text {{if $checked}}opacity-30{{/if}} {{$owner_icon}}"
                style="cursor: pointer; width: {{$width}}%; {{if $checked}}cursor: default;{{/if}}">
              <label onmouseover="ObjectTooltip.createDOM(this, 'tooltip_{{$curr_aide->_guid}}')">
                <input type="checkbox" {{if $checked}}checked disabled{{/if}} id="aide_{{$curr_aide->_guid}}"
                       onclick="
                         {{if "dPcabinet CConsultation complete_atcd_mode_grille"|conf:"CGroups-$g"}}
                           $('tooltip_{{$curr_aide->_guid}}').down('button').click();
                         {{else}}
                           addAntecedent(arguments[0] || window.event, '{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '', '{{$type}}', '{{$appareil}}', this)
                         {{/if}}" />

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
                              onclick="var event = {ctrlKey: true}; addAntecedent(event, '{{$curr_aide->text|smarty:nodefaults|JSAttribute}}', '', '{{$type}}', '{{$appareil}}', $('aide_{{$curr_aide->_guid}}'))">Compléter</button>
                    </td>
                  </tr>
                </table>
              </div>
            </td>
          {{/if}}
        {{/foreach}}
      </tr>
    {{/foreach}}
  </table>
{{/foreach}}