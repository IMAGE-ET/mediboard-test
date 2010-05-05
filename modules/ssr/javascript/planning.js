/* $Id$ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

WeekPlanning = Class.create({
  initialize: function(guid, hour_min, hour_max, events) {
    this.container = $(guid);
    this.events = events;
    this.scroll(hour_min, hour_max);
    this.updateEventsDimensions();
  },
  scroll: function(hour_min, hour_max) {
    var top = this.container.down(".hour-"+hour_min).offsetTop;
    /*var bottom = this.container.down(".hour-"+hour_max).offsetTop;
    
    this.container.show().setStyle({ 
      height: bottom-top +"px"
    });*/
    this.container.down('.week-container').scrollTop = top;
  },
  updateEventsDimensions: function(){
    this.events.each(function(event, i){
      var container = $(event.internal_id);
      if (!container) return;
      
      var dimensions = container.up("td").getDimensions();
      
      var width = dimensions.width;
      var height = dimensions.height / 60;
     
      container.setStyle({
        top:    (event.minutes * height)+"px",
        left:   (event.offset * width)+"px",
        width:  (event.width * width - 1)+"px",
        height: ((event.length * height) || 1)+"px"
      });
    }, this);
  },
	selectAllEvents: function(){
		this.container.select('.event').invoke('toggleClassName','selected');
		this.updateNbSelectEvents();
	},
	updateNbSelectEvents : function(){
	  this.container.down('span.nbSelectedEvents').update("("+this.container.select('.event.selected').length+")");
  }
});
