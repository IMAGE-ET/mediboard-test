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
  initialize: function(trigger, options) {
    trigger = $(trigger);
    
    if (!trigger) {
      return;
    }
    
    this.sTrigger = trigger.identify();
    this.sTooltip = null;
    this.idTimeout = null;
    this.modalScrollTop = null;
    
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
    }, options);
        
    trigger
        .observe("mouseout", this.cancelShow.bind(this))
        .observe("mouseleave", this.cancelShow.bind(this))
        .observe("mouseout", this.launchHide.bind(this))
        .observe("mouseover", this.cancelHide.bind(this))
        .observe("mousedown", this.cancelShow.bind(this))
        .observe("mousemove", this.resetShow.bind(this));
    
    if (App.touchDevice) {
      var that = this;
      document.observe("touchstart", function(event){
        var element = Event.element(event);
        var eTooltip = $(that.sTooltip);
        
        if (!eTooltip || element == eTooltip || element.descendantOf(eTooltip)) {
          return;
        }
        
        that.launchHide(that);
      });
    }
    
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
  
  getScrollTop: function(){
    if (!Prototype.Browser.IE) return;
    
    var modal = $(this.sTrigger).up(".modal");
    if (!modal) return;
    
    this.modalScrollTop = modal.scrollTop;
  },
  
  setScrollTop: function(){
    if (!Prototype.Browser.IE) return;
    
    var modal = $(this.sTrigger).up(".modal");
    if (!modal) return;
    
    modal.scrollTop = this.modalScrollTop;
  },
  
  show: function() {
    if (!this.sTooltip) {
      this.createContainer();
    }
    
    this.getScrollTop();
    
    var eTooltip = $(this.sTooltip);
    
    if (!Prototype.Browser.IE) {
      $$("div.tooltip").each(function(other) {
        if (!eTooltip.descendantOf(other)) {
          other.hide();
        }
      });
    }
    
    if (!eTooltip) return;
    
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
          offsetTop: (App.touchDevice ? -eTooltip.getHeight() : dim.height), 
          offsetLeft: Math.min(dim.width, 20), 
          setWidth: false, 
          setHeight: false
        })
        .unoverflow(20);
        
    this.setScrollTop();
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
    
    if (!eTrigger) return;
    
    var eTooltip = DOM.div({className: this.mode.sClass}).hide();
    
    $((Prototype.Browser.IE ? document.body : eTrigger.up("div.tooltip")) || document.body).insert(eTooltip);
    
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
 * Helpers for ObjectTooltip instanciations
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
      height: 50
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

    objectUFs: {
      module: "dPhospi",
      action: "httpreq_vw_object_ufs",
      sClass: "tooltip"
    },
    dom: {
      sClass: "tooltip"
    }
  },

  init: function(){
    // Init object tooltips on elements with the "data-object_guid" attribute
    document.on("mouseover", "[data-object_guid]", function(event, element){
      ObjectTooltip.createEx(element, element.get("object_guid"));
    });
  },

  /**
   * Create a generic tooltip
   *
   * @param {HTMLElement} trigger
   * @param {Object=}     options
   *
   * @return {ObjectTooltip,null}
   */
  create: function(trigger, options) {
    if (!trigger) {
      return null;
    }
    
    if (!trigger.oTooltip) {
      trigger.oTooltip = new ObjectTooltip(trigger, options);
    }

    trigger.oTooltip.launchShow();

    return trigger.oTooltip;
  },

  /**
   * Create a tooltip based on predefined ones
   *
   * @param {HTMLElement} trigger
   * @param {String}      guid
   * @param {String=}     mode
   * @param {Object=}     params
   *
   * @return {ObjectTooltip,null}
   */
  createEx: function(trigger, guid, mode, params) {
    mode = mode || 'objectView';
    params = params || {};
    
    params.object_guid = guid;
    
    var options = {
      mode: mode,
      params: params
    };
    
    return this.create(trigger, options);
  },

  createDOM: function(trigger, tooltip, options) {
    options = Object.extend( {
      params: {}
    }, options);
    
    options.params.element = tooltip;
    options.mode = "dom";
    
    return this.create(trigger, options);
  }
});
