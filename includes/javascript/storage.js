dojo.require("dojo.dom");
dojo.require("dojo.io.*");
dojo.require("dojo.event.*");
dojo.require("dojo.html.*");
dojo.require("dojo.lfx.*");
dojo.require("dojo.storage.*");


var MbStorage = {
  initialize: function(){
    var directory = dojo.byId("directory");
    dojo.event.connect(directory, "onchange", this, this.directoryChange);
    this._printAvailableKeys();
  },

  directoryChange: function(evt){
    var key = evt.target.value;
    this._handleLoad(key);    
  },
  
  load: function(key){   
    if(key == null || typeof key == "undefined" || key == ""){
      this._printStatus("Veuillez entrer une clé", "error");
      return;
    }
    return this._handleLoad(key);
  },

  save: function(key,value){
    if(key == null || typeof key == "undefined" || key == ""){
      this._printStatus("Veuillez entrer une clé", "error");
      return;
    }
    this._save(key, value)
  },

  clear: function(){
    
    dojo.storage.clear();
    
    this._printStatus("Cleared", "message");
    this._printAvailableKeys();
  },

  configure: function(evt){
    evt.preventDefault();
    evt.stopPropagation();
    if(dojo.storage.hasSettingsUI()){
      // redraw our keys after the dialog is closed, in
      // case they have all been erased
      var self = this;
      dojo.storage.onHideSettingsUI = function(){
        self._printAvailableKeys();
      }
      
      // show the dialog
      dojo.storage.showSettingsUI();
    }
  },
    
  remove: function(key){    
    this._printStatus("Removing '" + key + "'...", "loading");
    dojo.storage.remove(key);
    this._printStatus("Removed '" + key, "message");
  },
    
  _save: function(key, value){
    this._printStatus("Saving '" + key + "'...", "loading");
    var self = this;
    var saveHandler = function(status, keyName){
      if(status == dojo.storage.FAILED){
        alert("You do not have permission to store data for this web site. "
              + "Press the Configure button to grant permission.");
      }else if(status == dojo.storage.SUCCESS){
        self._printStatus("Saved '" + key + "'", "message");
        self._printAvailableKeys();
      }
    };
    try{
      dojo.storage.put(key, value, saveHandler);
    }catch(exp){
      alert(exp);
    }
  },
    
  _printAvailableKeys: function(){
    var availableKeys = dojo.storage.getKeys();
    
    var directory = dojo.byId("directory");
    
    // clear out any old keys
    directory.innerHTML = "";
    
    // add new ones
    for (var i = 0; i < availableKeys.length; i++){
      var optionNode = document.createElement("option");
      optionNode.appendChild(document.createTextNode(availableKeys[i]));
      optionNode.value = availableKeys[i];
      directory.appendChild(optionNode);
    }
    
    return availableKeys;
  },

  _handleLoad: function(key){
    this._printStatus("Loading '" + key + "'...", "loading");
    var results = dojo.storage.get(key);
    this._printStatus("Loaded '" + key + "'", "message");
    return results;
  },

  _printStatus: function(message,classname){
    $('systemMsg').innerHTML = "<div class='" + classname + "'>" + message + "</div>";
  }
};


//wait until the storage system is finished loading
if(dojo.storage.manager.isInitialized() == false){ // storage might already be loaded when we get here
  dojo.event.connect(dojo.storage.manager, "loaded", MbStorage, MbStorage.initialize);
}else{
  dojo.event.connect(dojo, "loaded", MbStorage, MbStorage.initialize);
}
