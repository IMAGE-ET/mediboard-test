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
    window["planning-{{$planning->guid}}"] && window["planning-{{$planning->guid}}"].scrollTop
  );
  
  planning.setLoadData({{$planning->load_data|@json}}, {{$planning->maximum_load}});
  
	window["planning-{{$planning->guid}}"] = planning;
});

</script>

<div class="planning {{if $planning->large}}large{{/if}} {{if $planning->has_load}}load{{/if}}" id="{{$planning->guid}}">
  {{assign var=nb_days value=$planning->nb_days}}
  <table class="tbl" style="table-layout: fixed;">
    <col style="width: 3.0em;" />
    <col span="{{$nb_days}}" />
    <col style="width: 16px;" />
    <tr>
    	<th class="title" colspan="{{$nb_days+2}}" {{if $planning->selectable}}onclick="window['planning-{{$planning->guid}}'].selectAllEvents()"{{/if}}>
    		<span class="nbSelectedEvents" style="float: left; font-size: smaller;"></span>
			{{$planning->title}}
			</th>
    </tr>
    <tr>
       <th></th>
    	 {{foreach from=$planning->days key=_day item=_events}}
         {{if $_day < $planning->date_min_active || $_day > $planning->date_max_active}}
           {{assign var=disabled value=true}}
         {{else}}
           {{assign var=disabled value=false}}
         {{/if}}
             
         <th class="day {{if $disabled}}disabled{{/if}}">
           {{$_day|date_format:"%a %d"|nl2br}}
           
           {{if array_key_exists($_day, $planning->day_labels)}}
             {{assign var=label value=$planning->day_labels.$_day}}
	             <label style="background: {{$label.color}};" title="{{$label.detail}}">
	               {{$label.text}}
	             </label>
           {{/if}}
         </th>
    	 {{/foreach}}
       <th></th>
    </tr>
  </table>
  
  <div style="overflow-y: scroll; overflow-x: hidden; height: {{$planning->height}}px;" class="week-container">
    <table class="tbl hours" style="table-layout: fixed;">
      <col style="width: 3.0em;" />
      <col span="{{$nb_days}}" />
      
      {{assign var=prev_pause value=false}}
      
      {{foreach from=$planning->hours item=_hour}}
        <tr class="hour-{{$_hour}} {{if in_array($_hour, $planning->pauses)}}pause{{/if}} {{if $prev_pause}}pause-next{{/if}}">
          <th class="hour">{{$_hour}}h</th>
          
          {{foreach from=$planning->days key=_day item=_events}}
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
            
            <td class="segment-{{$_day}}-{{$_hour}} {{if $disabled}}disabled{{/if}} {{if $unavail}}unavailable{{/if}}">
              <div><!-- <<< This div is necessary (relative positionning) -->
              
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
											 class="event {{if $_event->draggable}}draggable{{/if}} {{if $disabled}}disabled{{/if}} {{$_event->css_class}} {{$_event->guid}}" 
											 style="background-color: {{$_event->color}}; {{if !$_event->important}}opacity: 0.6{{/if}}">
											 	
                    <div class="time-preview" style="display: none;"></div>
                    
										{{if $planning->large}}
										<div class="time" title="{{$_event->start|date_format:"%H:%M"}}{{if $_event->length}} - {{$_event->end|date_format:"%H:%M"}}{{/if}}">
											{{$_event->start|date_format:"%H:%M"}}
                      {{if $_event->length}}
                       - {{$_event->end|date_format:"%H:%M"}}
                      {{/if}}
                    </div>
                    {{/if}}
										
                    <div class="body">
                      {{$_event->title|smarty:nodefaults}}
                    </div>
                    <div class="footer"></div>
                    <div class="handle"></div>
                  </div>
                  
                  {{if $_event->draggable}}
                  <script type="text/javascript">
                   Main.add(function(){
                     var planning = window['planning-{{$planning->guid}}'];
                     
                     function showTime(elt, event){
                       elt.down(".time-preview").update(event.getTimeString()).show();
                     }
                     function hideTime(elt){
                       elt.down(".time-preview").hide();
                     }
                   
                     function onDragSize(d){
                       var grip = d.element;
                       var e = grip.up();
                       
                       e.setStyle({
                         height: (grip.offsetTop+grip.getHeight())+"px"
                       });
                       
                       var event = planning.getEventById(e.id);
                       showTime(e, event);
                     }
                     
                     function onDragPosition(d){
                       var event = planning.getEventById(d.element.id);
                       showTime(d.element, event);
                     }
                     
                     function onEndPosition(d){
                       var event = planning.getEventById(d.element.id);
                       hideTime(d.element);
                       event.onChange();
                     }
                     
                     function onEndSize(d){
                       var element = d.element.up();
                       var event = planning.getEventById(element.id);
                       hideTime(element);
                       event.onChange();
                     }
                     
                     var element = $('{{$_event->internal_id}}');
                     var parent = element.up("td");
                     var snap = [parent.getWidth(), planning.getCellHeight()/planning.hour_divider];
                     
                     new Draggable(element, {snap: snap, handle: element.down(".handle"), change: onDragPosition, onEnd: onEndPosition});
                     new Draggable(element.down(".footer"), {constraint: "vertical", snap: snap, change: onDragSize, onEnd: onEndSize});
                   });
                   </script>
                   {{/if}}
                {{/if}}
              {{/foreach}}
              
              </div>
              
              {{* Time range *}}
              {{if array_key_exists($_day, $planning->load_data) && array_key_exists($_hour, $planning->load_data.$_day)}}
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