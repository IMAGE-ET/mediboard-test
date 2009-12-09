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
    this.sDiv = null;
    this.sTarget = null;
    this.idTimeout = null;
    
    var appearenceTimeout = {
      "short": 0.3,
      "medium": 0.6,
      "long": 0.9
    };

    this.oOptions = Object.extend( {
      mode: "objectView",
      popup: false,
      duration: appearenceTimeout[Preferences.tooltipAppearenceTimeout] || 0.6,
      durationHide: 0.2,
      params: {}
    }, oOptions);
    
    this.mode = ObjectTooltip.modes[this.oOptions.mode];

    if (!this.oOptions.popup) {
      this.createDiv();
      this.addHandlers();
    }
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
  
  show: function() {
    var eTarget = $(this.sTarget);
    
    if (this.oOptions.popup || !eTarget.innerHTML) {
      this.load();
    }
    
    if (!this.oOptions.popup) {
      this.reposition();
    }
  },
  
  hide: function() {
    $(this.sDiv).hide();
  },
  
  reposition: function() {
    var eTrigger = $(this.sTrigger),
        eDiv = $(this.sDiv);
				
    if (!eTrigger || this.dontShow) return; // necessary, unless it throws an error some times (why => ?)

    var dim = eTrigger.getDimensions();
    
    eDiv.show()
        .setStyle({marginTop: 0, marginLeft: 0})
        .clonePosition(eTrigger, {
          offsetTop: dim.height, 
          offsetLeft: Math.min(dim.width, 20), 
          setWidth: false, 
          setHeight: false}
        )
        .unoverflow();
  },
  
  load: function() {
    var eTarget = $(this.sTarget);
    
    if (this.oOptions.mode != 'dom') {
      var url = new Url(this.mode.module, this.mode.action);
      $H(this.oOptions.params).each( function(pair) { url.addParam(pair.key,pair.value); } );
      
      if(!this.oOptions.popup) {
        url.requestUpdate(eTarget, { 
				  waitingText: "Chargement infobulle",
				  onComplete: this.reposition.bind(this)
				} );
      } else {
        url.popup(this.mode.width, this.mode.height, this.oOptions.mode);
      }
    } else {
      eTarget.update($(this.oOptions.params.element).show());
      this.reposition();
    }
  },
  
  addHandlers: function() {
    $(this.sTrigger)
        .observe("mouseout", this.cancelShow.bind(this))
        .observe("mouseout", this.launchHide.bind(this))
        .observe("mouseover", this.cancelHide.bind(this))
        .observe("mousedown", this.cancelShow.bind(this));
        
    $(this.sDiv)
        .observe("mouseout", this.cancelShow.bind(this))
        .observe("mouseout", this.launchHide.bind(this))
        .observe("mouseover", this.cancelHide.bind(this));
  },
  
  createDiv: function() {
    var eTrigger = $(this.sTrigger),
        eDiv  = $("tooltipTpl").clone(true);
        
    eDiv.addClassName(this.mode.sClass)
        .removeAttribute("_extended");
        
    this.sDiv = eDiv.identify();
    $(document.body).insert(eDiv.hide());
    
    var eTarget = eDiv.down(".content");
    eTarget.removeAttribute("_extended");
    
    if (!Prototype.Browser.IE) {
      eTarget.setStyle( {
        minWidth : this.mode.width+"px",
        minHeight: this.mode.height+"px"
      } );
    }
    
    this.sTarget = eTarget.identify();
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
      sClass: "postit"
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
  },

  createEx: function(eTrigger, guid, mode, params) {
  	mode = mode || 'objectView';
  	params = params || {};
    
    params.object_guid = guid;
    
    var oOptions = {
      mode: mode,
      params: params
    };
    
    this.create(eTrigger, oOptions);
  },
  
  createDOM: function(eTrigger, sTarget, params) {
  	params = params || {};
  	params.element = sTarget;
    
    var oOptions = {
      mode: 'dom',
      params: params
    };
    
    this.create(eTrigger, oOptions);
  }
});

function initNotes(refresh){
  // The first argument of the onComplete callback is the XHR response, we have to filter it
  var selector = "div.noteDiv" + ((refresh && !refresh.status) ? "" : ":not(.initialized)");
  
  $$(selector).each(function(element) {
    element.addClassName("initialized");
    var url = new Url("system", "httpreq_get_notes_image");
    url.addParam("object_guid", element.className.split(" ")[1]);
    url.requestUpdate(element);
  });
}