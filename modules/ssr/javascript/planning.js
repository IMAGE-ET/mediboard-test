/* $Id$ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

PlanningEvent = Class.create({
  initialize: function(event, planning) {
    Object.extend(this, event);
    this.planning = planning;
  }, 
  updateDimensions: function(){
    var container = $(this.internal_id);
    if (!container) return;

    var width = container.up("td").getWidth();
    var height = this.planning.getCellHeight() / 60;
   
    container.setStyle({
      top:    (this.minutes * height)+"px",
      left:   (this.offset * width)+"px",
      width:  (this.width * width - 1)+"px",
      height: ((this.length * height) || 1)+"px"
    });
  },
  getElement: function(){
    return $(this.internal_id);
  },
  getTime: function(){
    var element = this.getElement();
    var date = element.up("td").className.match(/segment-([\d-]{10})-(\d{2})/i);
    var divider = this.planning.hour_divider;
    var minutes = 60/divider;
    var cellHeight = this.planning.getCellHeight();
    var cellWidth = element.up().getWidth();
    
    date = Date.fromDATETIME(date[1]+" "+date[2]+":00:00");
    
    var offset = {
      date: Math.round(element.offsetLeft/parseInt(cellWidth)), 
      time: Math.round((element.offsetTop/cellHeight) * divider) / divider
    };
    
    date.addDays(offset.date);
    date.addMinutes(offset.time * 60);
    
    var end = new Date(date);
    end.addMinutes((Math.round((element.getHeight() / cellHeight) * divider) / divider) * 60);

    return {start: date, end: end, length: (end - date) / (1000 * 60)};
  },
  getTimeString: function(){
    var time = this.getTime();
    return DateFormat.format(time.start, "HH:mm") + " - " + DateFormat.format(time.end, "HH:mm");
  },
  onChange: function(){
    return this.planning.onEventChange(this);
  }
});

WeekPlanning = Class.create({
  initialize: function(guid, hour_min, hour_max, events, hour_divider) {
    this.eventsById = {};
    for (var i = 0; i < events.length; i++) {
      this.eventsById[events[i].internal_id] = events[i] = new PlanningEvent(events[i], this);
    }
    
    this.container = $(guid);
    this.events = events;
    this.scroll(hour_min, hour_max);
    this.hour_divider = hour_divider;
    this.updateEventsDimensions();
  },
  scroll: function(hour_min, hour_max) {
    var top = this.container.down(".hour-"+hour_min).offsetTop;
    this.container.down('.week-container').scrollTop = top;
  },
  updateEventsDimensions: function(){
    this.events.invoke("updateDimensions");
  },
	selectAllEvents: function(){
		this.container.select('.event').invoke('toggleClassName','selected');
		this.updateNbSelectEvents();
	},
	updateNbSelectEvents : function(){
	  this.container.down('span.nbSelectedEvents').update("("+this.container.select('.event.selected').length+")");
  },
  getEventById: function(id) {
    return this.eventsById[id];
  },
  getCellHeight: function(){
    return this.container.down(".week-container table").getHeight() / 24;
  },
  onEventChange: function(e){
    console.debug(e.getTime());
  }
});
