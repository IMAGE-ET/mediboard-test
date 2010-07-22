/**
 * Provides auto-completion to helped fields
 */

var AideSaisie = {
  AutoComplete: Class.create({
    initialize: function(element, options){
      this.element = $(element);
      this.options = Object.extend({
        dependField1: null, 
        dependField2: null, 
        searchField: null, 
        objectClass: null, 
        userId: User.id,
        userView: User.view,
        contextUserId: User.id,
        contextUserView: User.view,
        validate: null,//element.form.onsubmit.bind(element.form),
        validateOnBlur: true,
        resetSearchField: true,
        resetDependFields: true,
        defaultUserId: null,
        defaultUserView: null,
        timestamp: "-- %n %p - dd/MM/y HH:mm"
      }, options);
      this.init();
    },
    init: function(){
      this.options.defaultUserId = this.options.contextUserId;
      this.options.defaultUserView = this.options.contextUserView;
      this.searchField = $(this.options.searchField || this.element);
      this.isContextOwner = this.options.userId == this.options.contextUserId;
      this.list = this.createListContainer();
      
      var url = new Url("dPcompteRendu", "httpreq_do_aide_autocomplete");
      url.addParam("property", this.element.name);
      url.addParam("object_class", this.options.objectClass);
      url.addParam("user_id", this.options.defaultUserId);
      
      // If it is a textarea
      if (/^textarea$/i.test(this.searchField.tagName)) {
        this.buildAdvancedUI(url);
      }
      else {
        url.autoComplete(this.searchField, this.list, {
          minChars: 2,
          updateElement: this.update.bind(this),
          paramName: "_search"
        });
      }
    },
    
    // Create div to feed
    createListContainer: function(){
      var list = new Element("div", {
        id: this.searchField.id + "_auto_complete"
      }).addClassName("autocomplete").setStyle({
        width: "400px"
      }).hide();
      
      this.searchField.insert({after: list});
      return list;
    },
    
    getSelectedData: function(selected){
      var oDepend1 = selected.down(".depend1");
      var oDepend2 = selected.down(".depend2");
      var oText = selected.down(".text");
      
      return {
        depend1: oDepend1 ? (oDepend1.textContent || oDepend1.innerText) : "",
        depend2: oDepend2 ? (oDepend2.textContent || oDepend2.innerText) : "",
        text: oText.textContent || oText.innerText
      };
    },
    
    // Update field after selection
    update: function(selected){
      var data = this.getSelectedData(selected);
      
      $V(this.options.dependField1, data.depend1);
      $V(this.options.dependField2, data.depend2);
      $V(this.element, data.text.strip());
      this.element.tryFocus();
    },
    
    // Update depend fields after selection
    updateDependFields: function(input, selected){
      var data = this.getSelectedData(selected);
      
      if($V(input).charAt($V(input).length - 1) != '\n') {
        $V(input, $V(input) + ' ');
      }
      input.tryFocus();
      $V(this.options.dependField1, data.depend1);
      $V(this.options.dependField2, data.depend2);
    },
    
    buildAdvancedUI: function(url){
      var throbber, list, toolbar, 
          options = this.options,
          buttons = {};
      
      var container = 
        DOM.div({className: "textarea-helped"},
        toolbar = DOM.div({className: "toolbar"},
          
          DOM.div({className: "throbber-background"}), 
          throbber = DOM.div({className: "throbber"}).hide(),
          //buttons.grid   = DOM.a({href: "#1"}, DOM.img({src: "images/icons/grid.png", title: "Mode grille"})),
          buttons.create = DOM.a({href: "#1"}, DOM.img({src: "images/icons/new.png", title: "Nouvelle aide"})),
          buttons.down   = DOM.a({href: "#1"}, DOM.img({src: "style/mediboard/images/buttons/down.png", title: "Voir tous les choix"})),
          buttons.owner  = DOM.a({href: "#1", title: this.options.defaultUserView}, DOM.img({src: "images/icons/user-glow.png"})),
          buttons.timestamp = DOM.a({href: "#1"}, DOM.img({src: "images/icons/timestamp.png", title: "Ajouter un horodatage"})),
          buttons.valid  = DOM.a({href: "#1"}, DOM.img({src: "style/mediboard/images/buttons/submit.png", title: "Valider"})).setVisible(this.options.validate)
        ).hide(),
        list = $(this.searchField.id + "_auto_complete").setStyle({marginLeft: "-2px"})
      );
      
      this.searchField.up().
        observe('mousemove', function(){toolbar.show()}).
        observe('mouseout',  function(){toolbar.hide()})/*.
        observe('click',     function(){toolbar.hide()}).
        observe('keydown',   function(){toolbar.hide()})*/;
      
      //buttons.invoke('observe', 'mouseover', Event.stop);
      
      var validate = this.options.validate ? function(){
        this.text = $V(this.searchField);
        this.options.validate(this.text);
        
        if (this.options.resetDependFields) {
          $V(this.options.dependField1, '');
          $V(this.options.dependField2, '');
        }
        if (this.options.resetSearchField) {
          $V(this.searchField, '');
        }
      }.bind(this) : Prototype.emptyFunction;
      
      // Setup the autocompleter
      var autocomplete = url.autoComplete(this.searchField, list, {
        minChars: 2,
        tokens: "\n",
        indicator: throbber,
        select: "text", 
        paramName: "_search",
        caretBounds: true,
        callback: function(input, query){
          query += options.dependField1 ? ("&depend_value_1="+($V(options.dependField1) || "")) : '';
          query += options.dependField2 ? ("&depend_value_2="+($V(options.dependField2) || "")) : '';
          return query+"&hide_empty_list=1&hide_exact_match=1";
        },
        dontSelectFirst: true,
        onAfterShow: function(element, update){
          if (update.select("li").length == 0) {
            autocomplete.active = false;
            autocomplete.hasFocus = false;
            //update.hide();
            autocomplete.hide();
          }
        },
        afterUpdateElement: this.updateDependFields.bind(this)
      });
      
      // The blur event must not hide the list
      Event.stopObserving(this.element, 'blur');
      Event.observe(this.element, 'blur', function() {
        // needed to make click events working
        //setTimeout(this.hide.bind(this), 2500);
        this.hasFocus = false;
        this.active = false;
      }.bindAsEventListener(autocomplete));
      
      document.observe("click", function(e){
        // if click outside the container
        if (!Event.element(e).descendantOf(container)) {
          autocomplete.hasFocus = false;
          autocomplete.active = false;
          list.hide();
        }
      });
      
      // Grid mode 
      var gridMode = function(e) {
        var options = this.options, 
            fragment = "", 
            dependValue,
            url = new Url('dPcompteRendu', 'aides_saisie_grid');
        
        dependValue = $V(options.dependField1);
        if (dependValue) {
          fragment += options.objectClass+"-"+dependValue;
        }
        
        dependValue = $V(options.dependField2);
        if (dependValue) {
          fragment += (fragment ? "," : "") + options.objectClass+"-"+dependValue;
        }
        
        url.addParam('object_class', options.objectClass);
        url.addParam('user_id', options.defaultUserId);
        url.addParam('property', this.element.name);
        url.setFragment(fragment);
        url.popup(900, 600, "Grille d'aides à la saisie");
        
        url.oWindow.applyHelper = function(title, text){
          this.element.value += text+"\n";
        }.bind(this);
      }.bindAsEventListener(this);
      
      // Toolbar buttons actions
      if (!this.isContextOwner) {
        buttons.owner.observe('click', function (e) {
          if(this.options.defaultUserId == this.options.userId) {
            this.options.defaultUserId = this.options.contextUserId;
            this.options.defaultUserView = this.options.contextUserView;
            buttons.owner.down().src = "images/icons/user-glow.png";
            buttons.owner.title = this.options.contextUserView;
          } else {
            this.options.defaultUserId = this.options.userId;
            this.options.defaultUserView = this.options.userView;
            buttons.owner.down().src = "images/icons/user.png";
            buttons.owner.title = this.options.userView;
          }

          var params = autocomplete.url.toQueryParams();
          params.user_id = this.options.defaultUserId;
          autocomplete.url = "?"+Object.toQueryString(params);
          autocomplete.hide();

        }.bind(this));
      }
      
      var activate = function(){
        this.changed = false;
        this.hasFocus = true;
        // We save the default params, change it so that _search 
        // is empty to have all the entries and restore it after
        var oldDefaultParams = this.options.defaultParams;
        this.options.defaultParams = "_search=";
        this.getUpdatedChoices();
        this.options.defaultParams = oldDefaultParams;
      }.bind(autocomplete);
      
      buttons.down.observe('click', activate);
      //buttons.grid.observe('mousedown', gridMode);
      buttons.valid.observe('click', validate);
      buttons.create.observe('click', function(e){
        AideSaisie.create(
          this.options.objectClass, 
          this.element, 
          null, 
          $V(this.options.dependField1), 
          $V(this.options.dependField2), 
          this.text,
          this.options.defaultUserId
        );
      }.bindAsEventListener(this));

      buttons.timestamp.observe('click', function(){
    	var timestamp = DateFormat.format(new Date(), this.options.timestamp);
    	
    	var parts = this.options.userView.split(" ");

    	timestamp = timestamp.replace(/%p/g, parts[1]);
    	timestamp = timestamp.replace(/%n/g, parts[0]);
    	timestamp = timestamp.replace(/%i/g, parts[1].charAt(0) + ". " + parts[0].charAt(0) + ". ");
    	
    	if(this.element.value[this.element.value.length -1] != '\n' && this.element.value.length != 0) {
    	  timestamp = '\n' + timestamp;
    	}
    	
    	this.element.value += timestamp + '\n';
    	this.element.scrollTop = this.element.scrollHeight;
    	this.element.tryFocus();
      }.bindAsEventListener(this));
      
      // We wrap the textarea with the new container
      this.searchField.insert({after: container});
      
      // We simulate the blur catch
      if (this.options.validateOnBlur) {
        document.observe("click", function(e){
          // if click outside the container
          if (this.searchField.value && !Event.element(e).descendantOf(container))
            validate();
        }.bindAsEventListener(this));
        
        document.observe("keydown", function(e){
          // if TAB key pressed
          if (this.searchField.value && Event.key(e) == 9)
            validate();
        }.bindAsEventListener(this));
      }
    }
  }),
  
  create: function (objectClass, field, name, dependValue1, dependValue2, text, userId) {
    var url = new Url("dPcompteRendu", "edit_aide");
    url.addParam("user_id"     , userId);
    url.addParam("class"       , objectClass);
    url.addParam("field"       , name || field.name);
    url.addParam("text"        , text || field.value);
    url.addParam("depend_value_1", dependValue1 || null);
    url.addParam("depend_value_2", dependValue2 || null);
    url.popup(600, 300, "AidesSaisie");
  }
};

var addHelp = AideSaisie.create;

function pasteHelperContent(oHelpElement, fireOnchange) {
  var aFound = oHelpElement.name.match(/_helpers_(.*)/);
  Assert.that(aFound.length == 2, "Helper element '%s' is not of the form '_helpers_propname'", oHelpElement.name);
  
  var sPropName = aFound[1].split("-")[0];
  var oAreaField = $(oHelpElement.form.elements[sPropName]);
  var sValue = oHelpElement.value;
  
  oHelpElement.value = "";
  var caret = oAreaField.caret();
  
  if(caret.begin != 0 && oAreaField.value[caret.begin -1] != '\n' && oAreaField.value.length != 0) {
    sValue = '\n' + sValue;
  }

  oAreaField.caret(caret.begin, caret.end, sValue + '\n');
  oAreaField.caret(oAreaField.value.length);
  oAreaField.scrollTop = oAreaField.scrollHeight;
  
  if (fireOnchange && oAreaField.onchange) {
    oAreaField.onchange();
  }
}
