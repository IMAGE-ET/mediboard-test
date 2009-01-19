/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision: $
 * @author OpenXtrem
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

    this.oOptions = Object.extend({
      mode: "objectView",
      popup: false,
      duration: 0.4,
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
  },
  
  launchHide: function() {
    this.idTimeoutHide = this.hide.bind(this).delay(this.oOptions.durationHide);
  },
  
  cancelHide: function() {
    window.clearTimeout(this.idTimeoutHide);
  },
  
  cancelShow: function() {
    window.clearTimeout(this.idTimeout);
  },
  
  show: function() {
    var eDiv    = $(this.sDiv);
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
    eTrigger = $(this.sTrigger);
    if (!eTrigger) return; // necessary, unless it throws an error some times (why => ?)
    
    var dim = eTrigger.getDimensions();
    
    $(this.sDiv)
        .show()
        .setStyle({marginTop: '0', marginLeft: '0'})
        .clonePosition(eTrigger, {offsetTop: dim.height, offsetLeft: Math.min(dim.width, 20), setWidth: false, setHeight: false})
        .unoverflow();
  },
  
  load: function() {
    var eTarget = $(this.sTarget);
    if (this.oOptions.mode != 'dom') {
      var url = new Url;
      url.setModuleAction(this.mode.module, this.mode.action);
      $H(this.oOptions.params).each( function(pair) { url.addParam(pair.key,pair.value); } );
      
      if(!this.oOptions.popup) {
        url.requestUpdate(eTarget, {onComplete: this.reposition.bind(this)});
      } else {
        url.popup(this.mode.width, this.mode.height, this.oOptions.mode);
      }
    } else {
      var elt = $(this.oOptions.params.element);
      eTarget.update(elt.remove().show());
      this.reposition();
    }
  },
  
  addHandlers: function() {
    $(this.sTrigger)
        .observe("mouseout", this.cancelShow.bind(this))
        .observe("mouseout", this.launchHide.bind(this))
        .observe("mouseover", this.cancelHide.bind(this));
        
    $(this.sDiv)
        .observe("mouseout", this.cancelShow.bind(this))
        .observe("mouseout", this.launchHide.bind(this))
        .observe("mouseover", this.cancelHide.bind(this));
  },
  
  createDiv: function() {
    var eTrigger = $(this.sTrigger);
    
    var eDiv  = Dom.cloneElemById("tooltipTpl",true);
    eDiv.hide()
        .addClassName(this.mode.sClass)
        .removeAttribute("_extended");
        
    this.sDiv = eDiv.identify();
    $(document.body).insert(eDiv);

    var eTarget = eDiv.select(".content")[0];
    eTarget.removeAttribute("_extended");
    
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
      width: 300,
      height: 150
    },
    objectView: {
      module: "system",
      action: "httpreq_vw_object",
      sClass: "tooltip",
      width: 300,
      height: 250
    },
    objectNotes: {
      module: "system",
      action: "httpreq_vw_object_notes",
      sClass: "postit"
    },
    translate: {
      module: "system",
      action: "httpreq_vw_translation",
      sClass: "tooltip"
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
    if (!mode) mode = 'objectView';
    if (!params) params = {};
    
    params.object_guid = guid;
    
    oOptions = {
      mode: mode,
      params: params
    };
    
    this.create(eTrigger, oOptions);
  }

} );


function initNotes(){
  $$("div.noteDiv").each(function(pair) {
    var aInfos = pair.className.split(" ")[1].split("-");

    url = new Url;
    url.setModuleAction("system", "httpreq_get_notes_image");
    url.addParam("object_class" , aInfos[0]);
    url.addParam("object_id"    , aInfos[1]);
    url.requestUpdate(pair, { waitingText : null });
  });
}


function initSante400(){
  $$("div.idsante400").each(function(element) {
    var aInfos = element.id.split("-");
  
    url = new Url;
    url.setModuleAction("system", "httpreq_vw_object_idsante400");
    url.addParam("object_class" , aInfos[0]);
    url.addParam("object_id"    , aInfos[1]);
    url.requestUpdate(element, { waitingText : null });
  });
}

function initPuces() {
  initNotes();
  initSante400();
}

function reloadNotes(){
  initNotes(); 
}