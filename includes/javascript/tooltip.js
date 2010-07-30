/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * ObjectTooltip Class
 *   Handle object tooltip creation, associated with a MbObject and a target HTML element
 */

var ObjectTooltip = Class.create({
  // Constructor
  initialize: function(eTrigger, oOptions) {
    eTrigger = $(eTrigger);
    
    this.sTrigger = eTrigger.identify();
    this.sTooltip = null;
    this.idTimeout = null;
    
    var appearenceTimeout = {
      "short": 0.4,
      "medium": 0.8,
      "long": 1.2
    };

    this.oOptions = Object.extend( {
      mode: "objectView",
      duration: appearenceTimeout[Preferences.tooltipAppearenceTimeout] || 0.6,
      durationHide: 0.2,
      params: {}
    }, oOptions);
        
    eTrigger
        .observe("mouseout", this.cancelShow.bind(this))
        .observe("mouseleave", this.cancelShow.bind(this))
        .observe("mouseout", this.launchHide.bind(this))
        .observe("mouseover", this.cancelHide.bind(this))
        .observe("mousedown", this.cancelShow.bind(this))
        .observe("mousemove", this.resetShow.bind(this));
    
    this.mode = ObjectTooltip.modes[this.oOptions.mode];
  },
  
  launchShow: function() {
    this.idTimeout = this.show.bind(this).delay(this.oOptions.duration);
    this.dontShow = false;
  },
  
  launchHide: function() {
    this.idTimeoutHide = this.hide.bind(this).delay(this.oOptions.durationHide);
  },
  
  cancelHide: function() {
    window.clearTimeout(this.idTimeoutHide);
  },
  
  cancelShow: function() {
    window.clearTimeout(this.idTimeout);
    this.dontShow = true;
  },
  
  resetShow: function(){
    window.clearTimeout(this.idTimeout);
    this.idTimeout = this.show.bind(this).delay(this.oOptions.duration);
  },
  
  show: function() {
    if (!this.sTooltip) {
      this.createContainer();
    }
    
    var eTooltip = $(this.sTooltip);
    
    if (eTooltip.empty()) {
      this.load();
    }
    
    this.reposition();
  },
  
  hide: function() {
    var eTooltip = $(this.sTooltip);
    if (eTooltip) eTooltip.hide();
  },
  
  reposition: function() {
    var eTrigger = $(this.sTrigger),
        eTooltip = $(this.sTooltip);
				
    if (!eTrigger || this.dontShow) return; // necessary, unless it throws an error some times (why => ?)

    var dim = eTrigger.getDimensions();
    
    eTooltip.show()
        .setStyle({marginTop: 0, marginLeft: 0})
        .clonePosition(eTrigger, {
          offsetTop: dim.height, 
          offsetLeft: Math.min(dim.width, 20), 
          setWidth: false, 
          setHeight: false
        })
        .unoverflow(20);
  },
  
  load: function() {
    var eTooltip = $(this.sTooltip);
    
    if (this.oOptions.mode != 'dom') {
      var url = new Url;
      url.setModuleAction(this.mode.module,this.mode.action); // needed here as it makes a bug with httrack in offline mode when in the constructor (???)
      $H(this.oOptions.params).each( function(pair) { url.addParam(pair.key,pair.value); } );
      
      url.requestUpdate(eTooltip, {
        waitingText: $T("Loading tooltip"),
        coverIE: false,
        onComplete: this.reposition.bind(this)
      });
    }
    else {
      eTooltip.update($(this.oOptions.params.element).show());
      this.reposition();
    }
  },

  createContainer: function() {
    var eTrigger = $(this.sTrigger);
    var eTooltip = DOM.div({className: this.mode.sClass}).hide();
    
    $((Prototype.Browser.IE ? document.body : eTrigger.up(".tooltip")) || document.body).insert(eTooltip);
    
    if (!Prototype.Browser.IE) {
      eTooltip.setStyle({
        minWidth : this.mode.width+"px",
        minHeight: this.mode.height+"px"
      });
    }
        
    eTooltip
        .observe("mouseout", this.cancelShow.bind(this))
        .observe("mouseleave", this.cancelShow.bind(this))
        .observe("mouseout", this.launchHide.bind(this))
        .observe("mouseover", this.cancelHide.bind(this));
    
    this.sTooltip = eTooltip.identify();
  }
} );

/**
 * ObjectTooltip utility fonctions
 *   Helpers for ObjectTooltip instanciations
 */

Object.extend(ObjectTooltip, {
  modes: {
    objectCompleteView: {
      module: "system",
      action: "httpreq_vw_complete_object",
      sClass: "tooltip",
      width: 600,
      height: 500
    },
    objectViewHistory: {
      module: "system",
      action: "httpreq_vw_object_history",
      sClass: "tooltip",
      width: 200,
      height: 0
    },
    objectView: {
      module: "system",
      action: "httpreq_vw_object",
      sClass: "tooltip",
      width: 300,
      height: 100
    },
    identifiers: {
      module: "dPsante400",
      action: "ajax_tooltip_identifiers",
      sClass: "tooltip",
      width: 150,
      height: 0
    },
    objectNotes: {
      module: "system",
      action: "httpreq_vw_object_notes",
      sClass: "tooltip postit"
    },
    dom: {
      sClass: "tooltip"
    }
  },
  
  create: function(eTrigger, oOptions) {
    if (!eTrigger.oTooltip) {
      eTrigger.oTooltip = new ObjectTooltip(eTrigger, oOptions);
    }

    eTrigger.oTooltip.launchShow();
    return eTrigger.oTooltip;
  },

  createEx: function(eTrigger, guid, mode, params) {
  	mode = mode || 'objectView';
  	params = params || {};
    
    params.object_guid = guid;
    
    var oOptions = {
      mode: mode,
      params: params
    };
    
    return this.create(eTrigger, oOptions);
  },
  
  createDOM: function(eTrigger, sTooltip, oOptions) {
    oOptions = Object.extend( {
      params: {}
    }, oOptions);
    
    oOptions.params.element = sTooltip;
    oOptions.mode = "dom";
    
    return this.create(eTrigger, oOptions);
  }
});

function initNotes(refresh){
  // The first argument of the onComplete callback is the XHR response, we have to filter it
  var selector = "div.noteDiv" + ((refresh && !refresh.status) ? "" : ":not(.initialized)");
  
  $$(selector).each(function(element) {
    element.addClassName("initialized");
    var url = new Url("system", "httpreq_get_notes_image");
    url.addParam("object_guid", element.className.split(" ")[1]);
    url.requestUpdate(element, {
      coverIE: false
    });
  });
}