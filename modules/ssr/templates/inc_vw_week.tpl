{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_default var=bank_holidays value="|"|explode:""}}
{{mb_default var=print value=0}}
{{mb_default var=scroll_top value=null}}

<script type="text/javascript">

Main.add(function() {
  var planning = new WeekPlanning(
    '{{$planning->guid}}', 
    '{{$planning->hour_min}}', 
    '{{$planning->hour_max}}', 
    {{$planning->events|@json}},
    {{$planning->ranges|@json}},
    {{$planning->hour_divider}},
    window["planning-{{$planning->guid}}"] && window["planning-{{$planning->guid}}"].scrollTop,
    {{$planning->adapt_range|@json}},
    '{{$planning->selectable}}',
    {{$planning->dragndrop}},
    {{$planning->resizable}},
    {{$planning->no_dates}}
  );
  
  planning.container.addClassName("drawn");
  planning.container.show();
  planning.setPlanningHeight(planning.container.up().getHeight());
  planning.setLoadData({{$planning->load_data|@json}}, {{$planning->maximum_load}});
  
  planning.scroll({{$scroll_top}});
  window["planning-{{$planning->guid}}"] = planning;
  
  {{if $planning->show_half}}
    planning.showHalf();
  {{/if}}
});

</script>

<div class="planning {{if $planning->large}} large {{/if}} {{if $planning->has_load}} load {{/if}} {{if $planning->has_range}} range {{/if}}" id="{{$planning->guid}}" style="display: none;">
  {{assign var=nb_days value=$planning->nb_days}}
  <table class="tbl" style="table-layout: fixed;">
    <colgroup>
      <col style="width: 3.0em;" />
      <col span="{{$nb_days}}" />
      <col style="width: 18px;"/>
    </colgroup>
    <tr>
      <th class="title {{if $planning->selectable}}selector{{/if}}" colspan="{{$nb_days+2}}" 
      {{if $planning->selectable}} onclick="window['planning-{{$planning->guid}}'].selectAllEvents()" {{/if}}>
        {{if $print}}
          <button type="button" class="print notext not-printable" onclick="$('{{$planning->guid}}').print()"></button>
        {{/if}}
        <div class="nbSelectedEvents" style="float: left; font-size: smaller; width: 20px;">
          (-) {{if @$date && $dialog}} {{$date|date_format:$conf.datetime}} {{/if}}
        </div>
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
           {{if $planning->selectable}} onclick="window['planning-{{$planning->guid}}'].selectDayEvents({{$smarty.foreach.days.index}})" {{/if}}
           {{if in_array($_day, $bank_holidays)}}style="background: #fc0"{{/if}}>
             {{if !$planning->no_dates}}
               {{$_day|date_format:"%a %d"|nl2br}}
             {{/if}}
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
    <table class="tbl hours" style="table-layout: fixed; overflow: hidden;" id="planningWeek">
      <colgroup>
        <col style="width: 3.0em;" />
        <col span="{{$nb_days}}" />
      </colgroup>
      {{foreach from=$planning->hours item=_hour}}
        {{assign var=printable value="not-printable"}}
        {{foreach from=$planning->days key=_day item=_events name=days}}
          {{if isset($planning->events_sorted.$_day.$_hour|smarty:nodefaults)}}
            {{assign var=printable value=""}}
          {{/if}}
        {{/foreach}}
        <tr class="hour-{{$_hour}} {{$printable}} {{if in_array($_hour, $planning->pauses)}}pause{{/if}}">
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
              {{if $planning->show_half}}
                <div style="width: 100%; height: 1px; background: #ccc;" class="show_half opacity-50"></div>
              {{/if}}
              
              {{if isset($planning->ranges_sorted.$_day.$_hour|smarty:nodefaults)}}
                {{assign var=has_range value=true}} 
              {{else}}
                {{assign var=has_range value=false}} 
              {{/if}}
              
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
              
              {{if $has_range || $has_events || $has_load}}
                <div class="cell-positioner" unselectable="on">{{* <<< This div is necessary (relative positionning) *}}
                  
                  {{if $has_range}}
                    <div class="range-container">
                      {{foreach from=$planning->ranges_sorted.$_day.$_hour item=_range key=_key}}
                        <div id="{{$_range->internal_id}}" class="range"
                          {{assign var=explode_guid value="-"|explode:$_range->guid}}
                          {{if $_range->type == "plageconsult"}}
                            style="cursor: help; background: #{{$_range->color}}";
                            onclick="PlageConsultation.edit('{{$explode_guid.1}}')"
                          {{/if}}>
                          {{if $_range->title}}
                            <div class="libelle">
                              {{$_range->title|smarty:nodefaults}}
                            </div>
                          {{/if}}
                        </div>
                      {{/foreach}}
                    </div>
                  {{/if}}
                  
                  {{if $has_events}}
                    <div class="event-container">
                    {{foreach from=$_events item=_event}}
                      {{if $_event->hour == $_hour}}
                      
                        {{assign var=draggable value=""}}
                        {{if ($app->user_prefs.ssr_planning_dragndrop || $planning->dragndrop) && $_event->draggable}}
                          {{assign var=draggable value=draggable}}
                        {{/if}}
                        
                        {{assign var=resizable value=""}}
                        {{if $app->user_prefs.ssr_planning_resize && $_event->resizable}}
                          {{assign var=resizable value=resizable}}
                        {{/if}}
                        
                        <div id="{{$_event->internal_id}}"
                             class="event {{$draggable}} {{$resizable}} {{if $disabled}}disabled{{/if}} {{$_event->css_class}} {{$_event->guid}} {{$_event->type}} {{if !$_event->important}}opacity-60{{/if}} {{if  isset($plageconsult_id|smarty:nodefaults) && $plageconsult_id == $_event->plage.id }}selected{{/if}}" 
                             style="background-color:{{$_event->color}}; {{if $_event->type == 'consultation' || $_event->type == 'operation'}}text-align:center;{{/if}}"
                             {{if $_event->type == "rdvfull"}}onmouseover="ObjectTooltip.createEx(this, '{{$_event->guid}}')"{{/if}}
                             {{if ($_event->type == "rdvfree" || $_event->type == "rdvfull") && !$_event->disabled}}
                               onclick="setClose('{{$_event->start|date_format:"%H:%M:00"}}', '{{$_event->plage.id}}', '{{$_event->start|date_format:"%A %d/%m/%Y"}}', '{{$chir_id}}' {{if isset($_event->plage.consult_id|smarty:nodefaults)}}, '{{$_event->plage.consult_id}}'{{/if}});"
                               data-plageconsult_id="{{$_event->plage.id}}"
                             {{/if}}>
                            {{if $_event->type == "consultation"}}
                              <div style="height:100%; width:5px; background-color:#{{if isset($_event->plage.color|smarty:nodefaults)}}{{$_event->plage.color}}{{else}}DDDDDD{{/if}};"> </div>
                            {{/if}}
                            
                           {{if $_event->menu|@count <= 3}}
                            <div class="toolbar" {{if $_event->hour == 0}}style="top:100%;"{{/if}}>
                              {{foreach from=$_event->menu item=element}}
                                <a class="button {{$element.class}} notext" onclick="window['planning-{{$planning->guid}}'].onMenuClick('{{$element.class}}','{{$_event->plage.id}}', this)" title="{{$element.title}}"></a>
                              {{/foreach}}
                            </div>
                            {{/if}}
                            
                          {{if (($app->user_prefs.ssr_planning_dragndrop || $planning->dragndrop ) && $_event->draggable) ||
                               ($app->user_prefs.ssr_planning_resize && $_event->resizable)}}
                            <div class="time-preview" style="display: none;"></div>
                          {{/if}}
                          
                          {{if $planning->large}}
                          <div class="time" title="{{$_event->start|date_format:"%H:%M"}}{{if $_event->length}} - {{$_event->end|date_format:"%H:%M"}}{{/if}}">
                            {{$_event->hour}}:{{$_event->minutes}}
                            {{if $_event->length}}
                             - {{$_event->end|date_format:"%H:%M"}}
                            {{/if}}
                          </div>
                          {{/if}}
                          
                          <div class="body">
                            {{if $_event->type == "consultation" || $_event->type == "operation"}}
                              <span>
                              {{assign var="plage" value=$_event->plage}}
                              {{assign var="elements" value=$_event->menu}}
                              {{foreach from=$elements key=num item=_plage}}
                                {{if $plage.pct lt 50}}
                                  {{assign var="backgroundClass" value="empty"}}
                                {{elseif $plage.pct lt 90}}
                                  {{assign var="backgroundClass" value="normal"}}
                                {{elseif $plage.pct lt 100}}
                                  {{assign var="backgroundClass" value="booked"}}
                                {{else}}
                                  {{assign var="backgroundClass" value="full"}}
                                {{/if}} 
                                {{if $num!=1}}
                                  <a onclick="window['planning-{{$planning->guid}}'].onMenuClick('{{$_plage.class}}','{{$plage.id}}', this)" href="#" title="{{$_plage.title}}" >
                                    {{if $num==2}}
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
                                    {{elseif $num==0}}
                                      {{$_event->title|smarty:nodefaults|nl2br}}<br/>
                                      {{$_event->start|date_format:$conf.time}} - {{$_event->end|date_format:$conf.time}}
                                    {{/if}}
                                  </a>
                                {{/if}}
                              {{/foreach}}
                              
                            {{elseif $_event->type == "rdvfree" || $_event->type == "rdvfull"}}
                              {{if $_event->disabled}}
                                <img src="style/mediboard/images/buttons/lock.png" style="float: right; height: 12px; width: 12px;" />
                              {{/if}}
                              <span style="color: #000;">
                              <strong>{{$_event->start|date_format:"%H:%M"}}</strong>
                              {{if $_event->icon}}
                                <img src="{{$_event->icon}}" style="height: 12px; width: 12px;" alt="{{$_event->icon_desc}}" title="{{$_event->icon_desc}}" />
                              {{/if}}
                              {{$_event->title|smarty:nodefaults|nl2br}}
                            {{else}}
                              <span>
                              {{$_event->title|smarty:nodefaults|nl2br}}
                            {{/if}}
                            </span>
                          </div>
                          
                          {{if ($app->user_prefs.ssr_planning_resize || $planning->resizable) && $_event->resizable}}
                            <div class="footer"></div>
                          {{/if}}
                          
                          {{if ($app->user_prefs.ssr_planning_dragndrop || $planning->dragndrop) && $_event->draggable}}
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
                        <div id="{{$planning->guid}}-{{$_day}}-{{$_hour}}-{{$_key}}" class="load {{$level}}" style="width: {{$_load_ratio*100|round}}%;"></div>
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