var AideSaisie = {
  AutoComplete: Class.create()
}

/**
 * Provides auto-completion to aided fields
 */
Class.extend(AideSaisie.AutoComplete, {

  // Form anf field names to autocomplete
  sForm: null,
  sTextField: null,
  sDependField: null,
  sSearchField: null,
  
  // Constructor
  initialize: function(sForm, sTextField, sDependField, sSearchField, object_class, user_id) {
    this.sForm = sForm;
    this.sTextField   = sTextField  ;
    this.sDependField = sDependField;
    this.sSearchField = sSearchField;
    
    url = new Url();
    url.setModuleAction("dPcompteRendu", "httpreq_do_aide_autocomplete");
    url.addParam("property", sTextField);
    url.addParam("object_class", object_class);
    url.addParam("user_id", user_id);

		this.createDiv();

    var oForm = document[sForm];
    var oSearchField = oForm[sSearchField];

    url.autoComplete(oSearchField.id, oSearchField.id + '_auto_complete', {
      minChars: 2,
      updateElement: this.update.bind(this)
    } );
  },

  // Create div to feed
  createDiv: function() {
    var oForm = document[this.sForm];
    var oSearchField = oForm[this.sSearchField];
    
	  var oDiv = $(document.createElement("div"));
	  oDiv.id = oSearchField.id + "_auto_complete";
	  oDiv.addClassName("autocomplete");
	  oDiv.setStyle( {
	    display: "none",
	    width: "400px"
	  } );
	  
	  oSearchField.parentNode.appendChild(oDiv);
  },
  
  // Update field after selection
  update: function(selected) {
    var oDepend = selected.select(".depend")[0];
    var sDepend = oDepend ? oDepend.textContent : "";
    var sText   = selected.select(".text"  )[0].textContent;
    
    var oForm = document[this.sForm];

    if (this.sDependField) {
	    Form.Element.setValue(oForm[this.sDependField], sDepend);
    }

    Form.Element.setValue(oForm[this.sTextField  ], sText  );
    oForm[this.sTextField  ].focus();
    oForm[this.sSearchField].value = "";
  }
  

} );

