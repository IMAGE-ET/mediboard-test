{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
WeekPlanning = {
  events: {{$planning->events|@json}},
  scroll: function(guid, hour_min, hour_max) {
	  var container = $(guid);
    var top = container.down(".hour-"+hour_min).offsetTop;
    /*var bottom = container.down(".hour-"+hour_max).offsetTop;
    
    container.show().setStyle({ 
      height: bottom-top +"px"
    });*/
    container.scrollTop = top;
	},
  init: function(guid, hour_min, hour_max) {
    this.scroll(guid, hour_min, hour_max);
    
    this.events.each(function(event){
      var container = $(event.guid);
      var dimensions = container.up("td").getDimensions();
      
      var width = dimensions.width;
      var height = dimensions.height / 60;
     
      container.setStyle({
        top:    (event.minutes * height)+"px",
        left:   (event.offset * width)+"px",
        width:  (event.width * width - 1)+"px",
        height: (event.length * height)+"px"
      });
    });
  }
};
	
Main.add(function() {
  WeekPlanning.init('{{$planning->guid}}', '{{$planning->hour_min}}', '{{$planning->hour_max}}');
});
</script>

<div class="planning">
  <table class="tbl" style="table-layout: fixed;">
    <col style="width: 3.5em;" />
    <col span="7" />
    <col style="width: 16px;" />
    
    <tr>
    	 <th class="title" colspan="9">{{$planning->title}}</th>
    </tr>
    <tr>
       <th></th>
    	 {{foreach from=$planning->days key=_day item=_events}}
         <th class="hour">{{$_day|date_format:"%a %d"|nl2br}}</th>
    	 {{/foreach}}
       <th></th>
    </tr>
  </table>
  
  <div id="{{$planning->guid}}" style="overflow-y: scroll; overflow-x: hidden; height: 250px;">
    <table class="tbl hours" style="table-layout: fixed;">
      <col style="width: 3.5em;" />
      <col span="7" />
      
      {{foreach from=$planning->hours item=_hour}}
        <tr class="hour-{{$_hour}} {{if in_array($_hour, $planning->pauses)}}pause{{/if}}">
          <th class="hour">{{$_hour}}:00</th>
          
          {{foreach from=$planning->days key=_day item=_events}}
            <td class="segment-{{$_day}}-{{$_hour}}">
              <div><!-- <<< This div is necessary (relative positionning) -->
              {{foreach from=$_events item=_event}}
                {{if $_event->hour == $_hour}}
                  <div id="{{$_event->guid}}" class="event" style="background-color: {{$_event->color}};">
                    <div class="time" title="{{$_event->start|date_format:"%H:%M"}} - {{$_event->end|date_format:"%H:%M"}}">
                      {{$_event->start|date_format:"%H:%M"}} - {{$_event->end|date_format:"%H:%M"}}
                    </div>
                    <div class="body">
                      {{$_event->title}}
                    </div>
                  </div>
                {{/if}}
              {{/foreach}}
              </div>
            </td>
          {{/foreach}}
          
        </tr>
      {{/foreach}}
    </table>
  </div>
</div>