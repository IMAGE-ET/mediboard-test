// $Id$

var CCAMSelector = {
  sForm     : null,
  sView     : null,
  sTarif    : null,
  sClass    : null,
  sChir     : null,
  sAnesth   : null,
  oUrl      : null,
  
  prepared : {
	  code : null,
	  tarif : null
	},
  
  options : {
    width : 800,
    height: 600
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPplanningOp", "code_selector");
    
    if(this.sAnesth) {
      this.oUrl.addParam("anesth"    , oForm[this.sAnesth].value);
    }
    this.oUrl.addParam("chir"        , oForm[this.sChir].value);
    this.oUrl.addParam("object_class", oForm[this.sClass].value);
    this.oUrl.addParam("type"        , "ccam");
    
    this.oUrl.popup(this.options.width, this.options.height, "CCAM Selector");
  },
  
  set: function(code, tarif) {
    this.prepared.code  = code;
    this.prepared.tarif = tarif;
    
    window.setTimeout(window.CCAMSelector.doSet , 1);
  },
  
  doSet: function(){
    var oForm = document[CCAMSelector.sForm];

    $V(oForm[CCAMSelector.sView], CCAMSelector.prepared.code);
    if (this.sTarif) {
	    $V(oForm[CCAMSelector.sTarif], CCAMSelector.prepared.tarif);
    }
  }
}
