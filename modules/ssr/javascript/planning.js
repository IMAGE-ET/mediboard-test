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

    var height = this.planning.getCellHeight() / 60;
   
    container.setStyle({
      top:    (this.minutes * height)+"px",
      left:   (this.offset * 100)+"%",
      width:  (this.width * 100)+"%",
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
    this.planning.scrollTop = this.planning.container.down('.week-container').scrollTop;
    return this.planning.onEventChange(this);
  }
});

WeekPlanning = Class.create({
  scrollTop: null,
  load_data: [],
  maximum_load: null,
  initialize: function(guid, hour_min, hour_max, events, hour_divider, scroll_top, adapt_range) {
    this.eventsById = {};
    for (var i = 0; i < events.length; i++) {
      this.eventsById[events[i].internal_id] = events[i] = new PlanningEvent(events[i], this);
    }
    
    this.container = $(guid);
    this.hour_min = hour_min;
    this.hour_max = hour_max;
    this.events = events;
    this.hour_divider = hour_divider;
    this.adapt_range = adapt_range;
    
    if (this.adapt_range) {
      this.adaptRangeHeight(); 
    }
    
    this.updateEventsDimensions();
    this.scroll(scroll_top);
    
    this.container.addClassName("drawn");
  },
  scroll: function(scroll_top) {
    var top = this.container.down(".hour-"+this.hour_min).offsetTop;
    this.container.down('.week-container').scrollTop = (scroll_top !== null && !Object.isUndefined(scroll_top) ? scroll_top : top);
  },
  setPlanningHeight: function(height) {
    var top = this.container.down("table").getHeight();
    this.container.down('.week-container').setStyle({height: height - parseInt(top, 10) + "px"});
    if (this.adapt_range) {
      this.adaptRangeHeight(); 
    }
    this.updateEventsDimensions();
  },
  adaptRangeHeight: function(){
    var weekContainer = this.container.down('.week-container table');
    var viewportHeight = this.container.down('.week-container').getHeight();
    var delta = parseInt(this.hour_max, 10) - parseInt(this.hour_min, 10) + 1;
    var visibleLines = this.countVisibleLines();
    
    weekContainer.setStyle({height: (viewportHeight / delta) * visibleLines + "px"});
  },
  updateEventsDimensions: function(){
    this.events.invoke("updateDimensions");
  },
	selectAllEvents: function(){
		this.container.select('.event:not(.now)').invoke('toggleClassName','selected');
		this.updateNbSelectEvents();
	},
  selectDayEvents: function(day){
    this.container.select('.day-'+day+' .event:not(.now)').invoke('toggleClassName','selected');
    this.updateNbSelectEvents();
  },
	updateNbSelectEvents : function(){
	  this.container.down('span.nbSelectedEvents').update("("+this.container.select('.event.selected').length+")");
  },
  getEventById: function(id) {
    return this.eventsById[id];
  },
  countVisibleLines: function(){
    return this.container.select(".week-container tr").filter(Element.visible).length;
  },
  getCellHeight: function(){
    return this.container.down(".week-container table").getHeight() / this.countVisibleLines();
  },
  onEventChange: function(e){
    console.debug(e.getTime());
  },
  setLoadData: function(load_data, maximum_load){
    this.load_data = load_data;
    this.maximum_load = maximum_load;
    
    if (!this.load_data) return;
    
    // Day
    $H(this.load_data).each(function(day){
      if (day.value.length === 0) return;
      
      var cellHeight = this.getCellHeight();
      var height = Math.ceil(cellHeight / this.hour_divider);
          
      // Hour
      $H(day.value).each(function(hour){
        
        // Minute
        $H(hour.value).each(function(load){
          var container = $(this.container.id+"-"+day.key+"-"+hour.key+"-"+load.key);
          
          var width = container.up().getWidth();
          var top = Math.ceil(cellHeight * (load.key / this.hour_divider) / 10);
          
          container.setStyle({
            top:    top+"px",
            width:  Math.round(width * (load.value / maximum_load))+"px",
            height: height+"px"
          });
        }, this);
      }, this);
    }, this);
  }
});
