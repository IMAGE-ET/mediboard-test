// $Id$

if (!window.CIM10Selector)
CIM10Selector = {
  sForm     : null,
  sView     : null,
  sChir     : null,
  sCode     : null,
  oUrl      : null,
  
	prepared : {
	  code : null
	},
	
  options : {
    mode: "stats",
    width: 800,
    height: 500
  },

  pop: function() {
    var oForm = getForm(this.sForm);
    this.oUrl = new Url("dPplanningOp", "code_selector");
    
    this.oUrl.addParam("chir", oForm[this.sChir].value);
    this.oUrl.addParam("type", "cim10");
    this.oUrl.addParam("mode", this.options.mode);
    
    this.oUrl.popup(this.options.width, this.options.height, "CIM10 Selector");
  },
  
  // Code finder
  find: function(){
    var oForm = getForm(this.sForm);
    this.oUrl = new Url("dPcim10", "code_finder");
    this.oUrl.addParam("code", oForm[this.sCode].value);
    this.oUrl.popup(this.options.width, this.options.height, "CIM");
  },
  
  set: function(code) {
    this.prepared.code = code;
    window.setTimeout(window.CIM10Selector.doSet, 1);
  },
  
  doSet: function(){
    var oForm = getForm(CIM10Selector.sForm);
    $V(oForm[CIM10Selector.sView], CIM10Selector.prepared.code);
  }
};