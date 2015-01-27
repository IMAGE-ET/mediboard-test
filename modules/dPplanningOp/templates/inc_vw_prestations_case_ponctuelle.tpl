{{*
 * $Id$
 *  
 * @category PlanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<td class="text">
  {{if isset($liaisons_p.$_date|smarty:nodefaults)}}
    {{foreach from=$liaisons_p.$_date item=_liaisons_by_prestation key=prestation_id}}
      {{assign var=prestation value=$prestations_p.$prestation_id}}
      {{foreach from=$_liaisons_by_prestation item=_liaison}}
        {{assign var=_item value=$_liaison->_ref_item}}
        <div style="height: 2em; display: inline-block;">
          <input type="text" name="liaisons_p[{{$_liaison->_id}}]" value="{{$_liaison->quantite}}"
                 class="ponctuelle" size="1" onchange="this.form.onsubmit()"/>
          <script>
            Main.add(function() {
              getForm('edit_prestations').elements['liaisons_p[{{$_liaison->_id}}]'].addSpinner(
                {step: 1, min: 0});
            });
          </script>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_item->_guid}}');"
                {{if $_item->color}}class="mediuser" style="border-left-color: #{{$_item->color}}"{{/if}}>
            {{$_item}}
          </span>
        </div>
      {{/foreach}}
    {{/foreach}}
  {{/if}}
</td>