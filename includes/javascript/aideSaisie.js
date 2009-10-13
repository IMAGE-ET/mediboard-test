/**
 * Provides auto-completion to helped fields
 */

var AideSaisie = {
  AutoComplete: Class.create({
    initialize: function(sForm, sTextField, sDependField, sDependField2, sSearchField, object_class, user_id, context_user_id, validate){
      this.sForm = sForm;
      this.sTextField = sTextField;
      this.sDependField = sDependField;
      this.sDependField2 = sDependField2;
      this.sSearchField = sSearchField;
      this.sObjectClass = object_class;
      this.iUserId = user_id;
      this.bIsContextOwner = user_id == context_user_id;
      this.fValidate = validate || Prototype.emptyFunction;
      
      var url = new Url("dPcompteRendu", "httpreq_do_aide_autocomplete");
      url.addParam("property", sTextField);
      url.addParam("object_class", object_class);
      url.addParam("user_id", user_id);
      
      this.createDiv();
      
      var oSearchField = this.getInput();
      
      // If it is a textarea
      if (/^textarea$/i.test(oSearchField.tagName)) {
        this.buildAdvancedUI(url);
      }
      else {
        url.autoComplete(oSearchField, oSearchField.id + "_auto_complete", {
          minChars: 2,
          updateElement: this.update.bind(this)
        });
      }
    },
        
    getForm: function(){
      return getForm(this.sForm);
    },
    
    getInput: function(){
      return getForm(this.sForm).elements[this.sSearchField];
    },
    
    // Create div to feed
    createDiv: function(){
      var oSearchField = this.getInput();
      
      var oDiv = new Element("div", {
        id: oSearchField.id + "_auto_complete"
      }).addClassName("autocomplete").setStyle({
        width: "600px"
      }).hide();
      
      oSearchField.insert({after: oDiv});
    },
    
    getSelectedData: function(selected){
      var oDepend = selected.select(".depend")[0];
      var oDepend2 = selected.select(".depend2")[0];
      
      return {
        depend: oDepend ? oDepend.textContent : "",
        depend2: oDepend2 ? oDepend2.textContent : "",
        text: selected.select(".text")[0].textContent
      };
    },
    
    // Update field after selection
    update: function(selected){
      var data = this.getSelectedData(selected);
      var oForm = document[this.sForm];
      
      if (this.sDependField) {
        $V(oForm[this.sDependField], data.depend);
      }
      
      if (this.sDependField2) {
        $V(oForm[this.sDependField2], data.depend2);
      }
      
      $V(oForm[this.sTextField], data.text.strip());
      oForm[this.sTextField].focus();
      oForm[this.sSearchField].value = "";
    },
    
    buildAdvancedUI: function(url){
      var oSearchField = this.getInput(),
          form = this.getForm(),
          throbber, toolbar, buttons = {}, list;
      
      var container = 
      DOM.div({className: "textarea-helped"},
        throbber = DOM.div({className: "throbber"}).hide(),
        toolbar = DOM.div({className: "toolbar"},
          buttons.valid  = DOM.a({href: "#1"}, DOM.img({src: "images/icons/tick.png", title: "Valider"})),
          buttons.owner  = DOM.img({src: "images/icons/user-glow.png", title: "Contexte"}),
          buttons.grid   = DOM.a({href: "#1"}, DOM.img({src: "images/icons/grid.png", title: "Mode grille"})),
          buttons.create = DOM.a({href: "#1"}, DOM.img({src: "images/icons/new.png", title: "Nouvelle aide"})),
          buttons.down   = DOM.a({href: "#1"}, DOM.img({src: "images/icons/downarrow.png", title: "Voir tous les choix"}))
        ).hide(),
        list = $(oSearchField.id + "_auto_complete").setStyle({marginTop: "-1px;"})
      );
      
      container.
        observe('mousemove', function(){toolbar.show()}).
        observe('mouseout',  function(){toolbar.hide()})/*.
        observe('click',     function(){toolbar.hide()}).
        observe('keydown',   function(){toolbar.hide()})*/;
      
      //buttons.invoke('observe', 'mouseover', Event.stop);
      
      var validate = function(){
        var input = this.getInput();
        this.fValidate($V(input));
        $V(input, '');
      }.bind(this);
      
      // Setup the autocompleter
      var autocomplete = url.autoComplete(oSearchField, list, {
        minChars: 2,
        frequency: 0.2,
        tokens: '\n',
        indicator: throbber,
        select: 'text'
      });
      
      var gridMode = function(object_class, user_id, property, depend1, depend2) {
        var url = new Url('dPcompteRendu', 'aides_saisie_grid');
        url.addParam('object_class', object_class);
        url.addParam('user_id', user_id);
        url.addParam('property', property);
        url.setFragment([depend1, depend2].compact().join(","));
        url.popup(900, 600, "Grille d'aides à la saisie");
      };
      
      oSearchField.observe('blur', validate);
      
      // Toolbar buttons actions
      if (!this.bIsContextOwner) {
        buttons.owner.setOpacity(0.5);
        buttons.owner.src = "images/icons/user.png";
      }
      //buttons.owner.observe('click', autocomplete.activate.bind(autocomplete));
      buttons.down.observe('click', autocomplete.activate.bind(autocomplete));
      
      buttons.grid.observe('click', function(){
        gridMode(this.sObjectClass, this.iUserId, this.sSearchField, $V(form[this.sDependField]), $V(form[this.sDependField2]));
      }.bind(this));
      
      buttons.valid.observe('click', validate);
      buttons.create.observe('click', function(){
        AideSaisie.create(this.sObjectClass, this.getForm()[this.sTextField], null, this.sDependField, this.sDependField2);
      }.bind(this));
      
      // We wrap the textarea with the new container
      oSearchField.insert({after: container});
      container.insert({top: oSearchField.next('div.grippie-h')}).insert({top: oSearchField});
    }
  }),
  
  create: function (sClass, oField, sName, sDepend1, sDepend2) {
    var url = new Url("dPcompteRendu", "edit_aide");
    url.addParam("class"       , sClass);
    url.addParam("field"       , sName || oField.name);
    url.addParam("text"        , oField.value);
    url.addParam("depend_value_1", sDepend1 || null);
    url.addParam("depend_value_2", sDepend2 || null);
    url.popup(600, 300, "AidesSaisie");
  }
};

var addHelp = AideSaisie.create;

function pasteHelperContent(oHelpElement) {
  var aFound = oHelpElement.name.match(/_helpers_(.*)/);
  Assert.that(aFound.length == 2, "Helper element '%s' is not of the form '_helpers_propname'", oHelpElement.name);
  
  var sPropName = aFound[1].split("-")[0];
  var oAreaField = $(oHelpElement.form.elements[sPropName]);
  var sValue = oHelpElement.value;
  
  oHelpElement.value = "";
  var caret = oAreaField.caret();
  oAreaField.caret(caret.begin, caret.end, sValue + '\n');
  oAreaField.caret(oAreaField.value.length);
  oAreaField.scrollTop = oAreaField.scrollHeight;
}

function putHelperContent(oElem, sFieldSelect) {
  var oForm      = oElem.form;
  var sDependsOn = $V(oElem);

  // Search for helpers elements in same form
  for (var i=0; i< oForm.elements.length; i++) {
    var element = oForm.elements[i];
    
    // Filter helper elements
    var aFound = element.name.match(/_helpers_(.*)/);
    if (!aFound) continue;
    
    Assert.that(aFound.length == 2, "Helper field name '%s' incorrect", element.name);
    Assert.that(element.nodeName == "SELECT", "Helper field name '%s' should be a select", element.name);
    
    // Check correspondance
    var aHelperParts = aFound[1].split("-");
    Assert.that(aHelperParts[0] == sFieldSelect, "Helper Field '%s' should target '%s' field",  element.name, sFieldSelect);
    
    // Show/Hide helpers
    var sHelperDependsOn = aHelperParts[1]; 
    if (sHelperDependsOn == "no_enum") {
      sHelperDependsOn = "";
    }
    
    $(element).setVisible(sHelperDependsOn == sDependsOn);
  }
}