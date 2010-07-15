{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function() {
  var planning = new WeekPlanning(
    '{{$planning->guid}}', 
    '{{$planning->hour_min}}', 
    '{{$planning->hour_max}}', 
    {{$planning->events|@json}}, 
    {{$planning->hour_divider}},
    window["planning-{{$planning->guid}}"] && window["planning-{{$planning->guid}}"].scrollTop,
    {{$planning->adapt_range|@json}}
  );
  
  planning.container.addClassName("drawn");
  planning.container.show();
  planning.setPlanningHeight(planning.container.up().getHeight());
  planning.setLoadData({{$planning->load_data|@json}}, {{$planning->maximum_load}});
  
  planning.scroll();
  
	window["planning-{{$planning->guid}}"] = planning;
});

</script>

<div class="planning {{if $planning->large}}large{{/if}} {{if $planning->has_load}}load{{/if}}" id="{{$planning->guid}}" style="display: none;">
  {{assign var=nb_days value=$planning->nb_days}}
  <table class="tbl" style="table-layout: fixed;">
    <col style="width: 3.0em;" />
    <col span="{{$nb_days}}" />
    <col style="width: 16px;" />
    <tr>
    	<th class="title {{if $planning->selectable}}selector{{/if}}" colspan="{{$nb_days+2}}" {{if $planning->selectable}}onclick="window['planning-{{$planning->guid}}'].selectAllEvents()"{{/if}}>
    		<span class="nbSelectedEvents" style="float: left; font-size: smaller;"></span>
			  {{$planning->title}}
			</th>
    </tr>
    <tr>
       <th></th>
    	 {{foreach from=$planning->days key=_day item=_events name=days}}
         {{if $_day < $planning->date_min_active || $_day > $planning->date_max_active}}
           {{assign var=disabled value=true}}
         {{else}}
           {{assign var=disabled value=false}}
         {{/if}}
             
         <th class="day {{if $disabled}}disabled{{/if}} text day-{{$smarty.foreach.days.index}} {{if $planning->selectable}}selector{{/if}}"
             {{if $planning->selectable}}onclick="window['planning-{{$planning->guid}}'].selectDayEvents({{$smarty.foreach.days.index}})"{{/if}}>
           {{$_day|date_format:"%a %d"|nl2br}}
					 {{if array_key_exists($_day, $planning->day_labels)}}
	           {{assign var=_labels_for_day value=$planning->day_labels.$_day}}
						 {{foreach from=$_labels_for_day item=_days_label}}
		           <label style="background: {{$_days_label.color}};" title="{{$_days_label.detail}}">
	               {{$_days_label.text}}
	             </label>
						 {{/foreach}}
					 {{/if}}
					</th>
    	 {{/foreach}}
       <th></th>
    </tr>
  </table>
  
  <div style="overflow-y: scroll; overflow-x: hidden; {{if $planning->height}}height: {{$planning->height}}px;{{/if}}" class="week-container">
    <table class="tbl hours" style="table-layout: fixed; overflow: hidden;">
      <col style="width: 3.0em;" />
      <col span="{{$nb_days}}" />
      
      {{foreach from=$planning->hours item=_hour}}
        <tr class="hour-{{$_hour}} {{if in_array($_hour, $planning->pauses)}}pause{{/if}}">
          <th class="hour">{{$_hour}}h</th>
          
          {{foreach from=$planning->days key=_day item=_events name=days}}
            {{if $_day < $planning->date_min_active || $_day > $planning->date_max_active}}
              {{assign var=disabled value=true}}
            {{else}}
              {{assign var=disabled value=false}}
            {{/if}}
            
            {{if array_key_exists($_day, $planning->unavailabilities)}}
              {{assign var=unavail value=true}}
            {{else}}
              {{assign var=unavail value=false}}
            {{/if}}
            
            <td class="segment-{{$_day}}-{{$_hour}} {{if $disabled}}disabled{{/if}} {{if $unavail}}unavailable{{/if}} day-{{$smarty.foreach.days.index}}">
              {{if isset($planning->events_sorted.$_day.$_hour|smarty:nodefaults)}}
                {{assign var=has_events value=true}} 
              {{else}}
                {{assign var=has_events value=false}} 
              {{/if}}
              
              {{if isset($planning->load_data.$_day.$_hour|smarty:nodefaults)}}
                {{assign var=has_load value=true}} 
              {{else}}
                {{assign var=has_load value=false}} 
              {{/if}}
              
              {{if $has_events || $has_load}}
                <div>{{* <<< This div is necessary (relative positionning) *}}
                
                  {{if $has_events}}
                    <div class="event-container">
                    {{foreach from=$_events item=_event}}
                      {{if $_event->hour == $_hour}}
                        <div id="{{$_event->internal_id}}" 
      									     {{if $_event->guid}}
                               onmouseover="ObjectTooltip.createEx(this, '{{$_event->guid}}');" 
      									       {{if $planning->selectable}}onclick="this.toggleClassName('selected'); window['planning-{{$planning->guid}}'].updateNbSelectEvents();"
      												   ondblclick="if(window.onSelect){ onSelect(this, '{{$_event->css_class}}'); }"
      												 {{/if}}
      											 {{/if}} 
      											 class="event {{if $app->user_prefs.ssr_planning_dragndrop && $_event->draggable}}draggable{{/if}} {{if $app->user_prefs.ssr_planning_resize && $_event->resizable}}resizable{{/if}} {{if $disabled}}disabled{{/if}} {{$_event->css_class}} {{$_event->guid}}" 
      											 style="background-color: {{$_event->color}}; {{if !$_event->important}}opacity: 0.6{{/if}}">
      										
                          {{if $app->user_prefs.ssr_planning_dragndrop && $_event->draggable || $app->user_prefs.ssr_planning_resize && $_event->resizable}}
                            <div class="time-preview" style="display: none;"></div>
                          {{/if}}
                          
      										{{if $planning->large}}
      										<div class="time" title="{{$_event->start|date_format:"%H:%M"}}{{if $_event->length}} - {{$_event->end|date_format:"%H:%M"}}{{/if}}">
      											{{$_event->start|date_format:"%H:%M"}}
                            {{if $_event->length}}
                             - {{$_event->end|date_format:"%H:%M"}}
                            {{/if}}
                          </div>
                          {{/if}}
      										
                          <div class="body">
                            {{$_event->title|smarty:nodefaults|nl2br}}
                          </div>
                          
                          {{if $app->user_prefs.ssr_planning_resize && $_event->resizable}}
                            <div class="footer"></div>
                          {{/if}}
                          
                          {{if $app->user_prefs.ssr_planning_dragndrop && $_event->draggable}}
                            <div class="handle"></div>
                          {{/if}}
                          
                        </div>
                      {{/if}}
                    {{/foreach}}
                    </div>
                  {{/if}}
                  
                  {{* Time range *}}
                  {{if $has_load}}
                    <div class="load-container">
                      {{foreach from=$planning->load_data.$_day.$_hour item=_load key=_key}}
                        {{math equation="x/y" x=$_load y=$planning->maximum_load assign=_load_ratio}}
                        {{if $_load_ratio < 0.3}}
                          {{assign var=level value=low}}
                        {{elseif $_load_ratio < 0.7}}
                          {{assign var=level value=medium}}
                        {{else}}
                          {{assign var=level value=high}}
                        {{/if}}
                        <div id="{{$planning->guid}}-{{$_day}}-{{$_hour}}-{{$_key}}" class="load {{$level}}"></div>
                      {{/foreach}}
                    </div>
                  {{/if}}
                
                </div>
              {{/if}}
            </td>
            {{if in_array($_hour,$planning->pauses)}}
              {{assign var=prev_pause value=true}}
            {{else}}
              {{assign var=prev_pause value=false}}
            {{/if}}
          {{/foreach}}
          
        </tr>
      {{/foreach}}
    </table>
  </div>
</div>