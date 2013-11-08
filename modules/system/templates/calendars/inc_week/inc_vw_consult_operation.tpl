{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<span>
  {{assign var="plage" value=$_event->plage}}
  {{if $plage.pct lt 50}}
    {{assign var="backgroundClass" value="empty"}}
  {{elseif $plage.pct lt 90}}
    {{assign var="backgroundClass" value="normal"}}
  {{elseif $plage.pct lt 100}}
    {{assign var="backgroundClass" value="booked"}}
  {{else}}
    {{assign var="backgroundClass" value="full"}}
  {{/if}}
  <a onclick="window['planning-{{$planning->guid}}'].onMenuClick('{{$plage.list_class}}','{{$plage.id}}', this)" href="#" title="{{$plage.list_title}}" >
    {{$_event->title|smarty:nodefaults|nl2br}}<br/>
    {{$_event->start|date_format:$conf.time}} - {{$_event->end|date_format:$conf.time}}
  </a>
  <a onclick="window['planning-{{$planning->guid}}'].onMenuClick('{{$plage.add_class}}','{{$plage.id}}', this)" href="#" title="{{$plage.add_title}}" >
    <div class="progressBar">
      <div class="bar {{$backgroundClass}}" style="width: {{$plage.pct}}%;"></div>
      <div class="text">
        {{if $plage.locked}}
          <img style="float: right; height: 12px;" src="style/mediboard/images/buttons/lock.png" />
        {{/if}}
        {{if $_event->type == "consultation"}}
          {{$plage._affected}} {{if $plage._nb_patients != $plage._affected}}({{$plage._nb_patients}}){{/if}} / {{$plage._total|string_format:"%.0f"}}
        {{else}}
          {{$plage._nb_operations}} Op
        {{/if}}
      </div>
    </div>
  </a>
  {{if $_event->_ref_disponibility|@count}}
    <div class="progressBar_dispo" title="Statut des créneaux">
      {{foreach from=$_event->_ref_disponibility item=_dispo}}
        <div class="disponibility_bar disponibility_planning_{{$_dispo}}"></div>
      {{/foreach}}
    </div>
  {{/if}}
</span>