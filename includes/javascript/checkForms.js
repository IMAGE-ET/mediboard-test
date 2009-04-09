/* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

var ElementChecker = {
  aProperties    : {},
  oElement       : null,
  oForm          : null,
  oLabel         : null,
  sLabel         : null,
  
  sTypeSpec      : null,
  oTargetElement : null,
  oCompare       : null,
  oErrors        : [],
  sValue         : null,

  prepare : function(oElement){
    this.oElement = oElement;
    
    var isArray  = (!oElement.options && (Object.isArray(oElement) || Object.isElement(oElement[0])));
    oElement = $(isArray?oElement[0]:oElement);

    this.oForm = oElement.form;
    this.oProperties = oElement.getProperties();
    
    this.oLabel = oElement.getLabel();
    this.sLabel = this.oLabel ? this.oLabel.innerHTML : oElement.name;
    
    if (this.oProperties.mask) {
      this.oProperties.mask = this.oProperties.mask.gsub('S', ' ').gsub('P', '|');
    }
    this.oErrors = [];
    this.sValue = (this.oProperties.mask ? 
                     this.oElement.getFormatted(this.oProperties.mask, this.oProperties.format) : 
                     $V(this.oElement));
                     
    Object.extend(this.check, this);
  },
  
  //---- Assertion functions, to check the number of arguments for each property type
  assertMultipleArgs: function(prop, multiplicity) {
    if (Object.isUndefined(this.oProperties[prop])) return false;
    Assert.that(this.oProperties[prop] !== true, '"'+prop+'" nécessite '+multiplicity+((multiplicity != null) ? multiplicity : 'un ou plusieurs')+' argument(s)');
    return this.oProperties[prop] = [this.oProperties[prop]].flatten();
  },
  
  assertSingleArg: function(prop) {
    if (Object.isUndefined(this.oProperties[prop])) return false;
    Assert.that(((typeof this.oProperties[prop] != "boolean") && !Object.isArray(this.oProperties[prop])), '"'+prop+'" nécessite un et un seul argument');
    return this.oProperties[prop] = [this.oProperties[prop]].flatten().reduce();
  },
  
  assertNoArg: function(prop) {
    if (Object.isUndefined(this.oProperties[prop])) return false;
    Assert.that(this.oProperties[prop] == true, '"'+prop+'" ne doit pas avoir d\'arguments');
    return this.oProperties[prop] = true;
  },
  //---------------------------------------------------------------------------------
  
  getCastFunction: function() {
  	if (this.oProperties["num"])   return function(value) { return parseInt(value, 10); }
		if (this.oProperties["float"]) return function(value) { return parseFloat(value, 10); }
		if (this.oProperties["date"])  return function(value) { return Date.fromDATE(value); }
		return Prototype.K;
  },
  
  castCompareValues: function(sTargetElement) {
    this.oTargetElement = this.oElement.form.elements[sTargetElement];
    if (!this.oTargetElement)
      printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
    
    var fCaster = this.getCastFunction();
  	this.oCompare = {
      source : this.sValue               ? fCaster(this.sValue) : null,
      target : this.oTargetElement.value ? fCaster(this.oTargetElement.value) : null
  	}
  	return null;
  },
  
  addError: function(prop, message) {
    if (!message) return true;
    if (!this.oErrors.find(function (e) {return e.type == prop})) {
      this.oErrors.push({type: prop, message: message});
    }
    return false;
  },

  getErrorMessage: function() {
    var msg = '';
    this.oErrors.each(function (error) {
      msg += "   - "+error.message+"\n";
    });
    return msg;
  },
  
  checkElement : function() {
    var that = this;
    
    if (this.oProperties.notNull || (this.sValue && !this.oProperties.notNull)) {
      $H(this.oProperties).each(function (prop) {
        if (that.check[prop.key])
          that.addError(prop.key, that.check[prop.key]());
      });
    }

    // Free DOM element references
    this.oElement = null;
    this.oForm = null;
    this.oLabel = null;
    this.oTargetElement = null;
    this.oCompare = null;
    
    return this.oErrors;
  }
}

Object.extend(ElementChecker, {
  check: {
    // isNumeric
    isNumeric: function () {
      if (isNaN(this.sValue))
        this.addError("isNumeric", "N'est pas dans un format numérique valide");
    },
    
    // notNull
    notNull: function () {
      this.assertNoArg("notNull");
      if (this.sValue == "")
        this.addError("notNull", "Ne doit pas être vide");
    },
    
    // xor
    xor: function () {
      var sTargetElement = this.assertMultipleArgs("xor");
      var iNbElements = this.sValue != "";
      var sListElements = this.sLabel;
      var message = "";
  
      var that = this;
      this.oProperties["xor"].each(function(sTargetElement) {
        var oTargetElement = that.oForm.elements[sTargetElement];
        if (!oTargetElement) {
          message += printf("Elément cible invalide ou inexistant (nom = %s)", sTargetElement);
        }
        else {
          var oTargetLabel = getLabelFor(oTargetElement);
          var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : sTargetElement;
          iNbElements += (oTargetElement.value != "");
          sListElements += ", " + sTargetLabel;
        }
      });
      this.addError("xor", message);
      if (iNbElements != 1) 
        this.addError("xor", printf("Vous devez choisir une et une seule valeur parmi '%s", sListElements));  
    },
    
    // moreThan
    moreThan: function () {
      sTargetElement = this.assertSingleArg("moreThan");
      this.addError("moreThan", this.castCompareValues(sTargetElement));
      
      if (this.oCompare.source && this.oCompare.target && (this.oCompare.source <= this.oCompare.target))
        this.addError("moreThan", "'%s' n'est pas strictement supérieur à '%s'", this.sValue,  this.oTargetElement.value);
    },
    
    // moreEquals
    moreEquals: function () {
      sTargetElement = this.assertSingleArg("moreEquals");
      this.addError("moreEquals", this.castCompareValues(sTargetElement));
      
      if (this.oCompare.source && this.oCompare.target && (this.oCompare.source < this.oCompare.target))
        this.addError("moreEquals", printf("'%s' n'est pas supérieur ou égal à '%s'", this.sValue,  this.oTargetElement.value));
    },
    
    // sameAs
    sameAs: function () {
      sTargetElement = this.assertSingleArg("sameAs");
      this.addError("sameAs", this.castCompareValues(sTargetElement));
      
      if (this.oCompare.source && this.oCompare.target && (this.oCompare.source != this.oCompare.target)) {
        var oTargetLabel = getLabelFor(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : this.oTargetElement.name;
        this.addError("sameAs", printf("Doit être identique à [%s]", sTargetLabel.strip()));
      }
    },
    
    // notContaining
    notContaining: function () {
      sTargetElement = this.assertSingleArg("notContaining");
      this.addError("notContaining", this.castCompareValues(sTargetElement));

      if (this.oCompare.source && this.oCompare.target && this.oCompare.source.match(this.oCompare.target)) {
        var oTargetLabel = getLabelFor(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : '"'+this.oCompare.target+'"';
        this.addError("notContaining", printf("Ne doit pas contenir [%s]", sTargetLabel.strip()));
      }
    },
    
    // notNear
    notNear: function () {
      sTargetElement = this.assertSingleArg("notNear");
      this.addError("notNear", this.castCompareValues(sTargetElement));
      
      if (this.oCompare.source && this.oCompare.target && levenshtein(this.oCompare.target, this.oCompare.source) < 3) {
        var oTargetLabel = getLabelFor(this.oTargetElement);
        var sTargetLabel = oTargetLabel ? oTargetLabel.innerHTML : '"'+this.oCompare.target+'"';
        this.addError("notNear", printf("Ressemble trop à [%s]", sTargetLabel.strip()));
      }
    },
    
    // alphaAndNum
    alphaAndNum: function () {
      this.assertNoArg("alphaAndNum");
      if (!this.sValue.match(/[A-z]/) || !this.sValue.match(/\d+/))
        this.addError("alphaAndNum", "Doit contenir au moins une lettre et un chiffre");
    },
    
    // length
    length: function () { 
      this.assertSingleArg("length");
      iLength = parseInt(this.oProperties["length"], 10);
      
      if (iLength < 1 || iLength > 255)
        Console.error(printf("Spécification de longueur invalide (longueur = %s)", iLength));
  
      if (this.sValue.length != iLength)
        this.addError("length", printf("N'a pas la bonne longueur (longueur souhaitée : %s)", iLength));
    },
    
    // minLength
    minLength: function () { 
      this.assertSingleArg("minLength");
      iLength = parseInt(this.oProperties["minLength"], 10);
      
      if (iLength < 1 || iLength > 255)
        Console.error(printf("Spécification de longueur minimale invalide (longueur = %s)", iLength));
  
      if (this.sValue.length < iLength)
        this.addError("minLength", printf("N'atteint pas la bonne longueur (longueur souhaitée : %s)", iLength));
    },
    
    // maxLength
    maxLength: function () { 
      this.assertSingleArg("maxLength");
      iLength = parseInt(this.oProperties["maxLength"], 10);
      
      if (iLength < 1 || iLength > 255)
        Console.error(printf("Spécification de longueur maximale invalide (longueur = %s)", iLength));
  
      if (this.sValue.length > iLength)
        this.addError("maxLength", printf("Dépasse la bonne longueur (longueur souhaitée : %s)", iLength));
    },
    
    // pos
    pos: function () {
      this.assertNoArg("pos");
      this.isNumeric();
      if (this.sValue <= 0)
        this.addError("pos", "Doit être une valeur positive");
    },
    
    // min
    min: function () {
      this.assertSingleArg("min");
      this.isNumeric();
      
      iMin = parseInt(this.oProperties["min"], 10);
      if (this.sValue < iMin)
        this.addError("min", printf("Doit avoir une valeur minimale de %s", iMin));
    },
    
    // max
    max: function () {
      this.assertSingleArg("max");
      this.isNumeric();
      
      iMax = parseInt(this.oProperties["max"], 10);
      if (this.sValue > iMax)
        this.addError("max", printf("Doit avoir une valeur maximale de %s", iMax));
    },
    
    minMax: function () {
      this.assertMultipleArgs("minMax", 2);
      var min = parseInt(this.oProperties.minMax[0]);
      var max = parseInt(this.oProperties.minMax[1]);
      if (this.sValue < min || 
          this.sValue > max)
        this.addError(printf("N'est pas compris entre %i et %i", min, max));
    },
    
    // ccam
    ccam: function() {
      this.assertNoArg("ccam");
      if (!this.sValue.match(/^([A-Z]){4}[0-9]{3}(-[0-9](-[0-9])?)?$/i))
        this.addError("ccam", "Code CCAM incorrect");
    },
    
    // cim10
    cim10: function () {
      this.assertNoArg("cim10");
      if (!this.sValue.match(/^([a-z0-9]){0,5}$/i))
        this.addError("cim10", "Code CIM incorrect, doit contenir 5 lettres maximum");
    },
    
    // adeli
    adeli: function() {
      this.assertNoArg("adeli");
      if (!this.sValue.match("/^([0-9]){9}$/i"))
        this.addError("adeli", "Code Adeli incorrect, doit contenir exactement 9 chiffres");
    },
    
    // insee
    insee: function () {
      this.assertNoArg("insee");
      if (this.sValue.match(/^([0-9]{7,8}[A-Z])$/i))
        return;
      
      if (aMatches = this.sValue.match(/^([1278][0-9]{2}[0-9]{2}[0-9]{2}[0-9]{3}[0-9]{3})([0-9]{2})$/i)) {
        nCode = parseInt(aMatches[1], 10);
        nCle  = parseInt(aMatches[2], 10);
        if (97 - (nCode % 97) != nCle)
          this.addError("insee", "Matricule incorrect, la clé n'est pas valide");
        else return;
      }
  
      this.addError("insee", "Matricule incorrect");
    },
    
    // order number
    product_order: function () {
      this.assertNoArg("product_order");
      if (this.sValue.indexOf("%id") == -1)
        this.addError("produc_order", "Le numéro de commande doit contenir %id");
    },
    
    // siret
    siret: function () {
      this.assertNoArg("siret");
      if (!luhn(this.sValue))
        this.addError("siret", "Code SIRET incorrect");
    },
    
    // rib
    rib: function () {
      this.assertNoArg("rib");
      // TODO: implement this
    },
    
    // list
    list: function() {
      var list = this.assertMultipleArgs("list");

      if (!this.sValue || (this.sValue && list.indexOf(this.sValue) == -1))
        this.addError("list", "N'est pas une valeur possible");
    },
    
    ///////// Data types ////////////
    // ref
    ref: function() {
      this.notNull();
      this.pos();
    },
    
    // str
    str: function () {},
    
    // numchar
    numchar: function() {
      this.num();
    },
    
    // num
    num: function() {
      this.isNumeric();
    },
    
    // bool
    bool: function() {
      this.isNumeric();
      if(this.sValue != 0 && this.sValue != 1)
        this.addError("bool", "Ne peut être différent de 0 ou 1");
    },
    
    // enum (must be surrounded by quotes, IE bug)
    "enum": function() {
      if (!this.oProperties.list) {
        Console.error("Spécification 'list' manquante pour le champ " + this.sLabel);
        return;
      }
    },
    
    birthDate: function() {
      this.date();
      var values = null;
      if (values = this.sValue.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/)) {
	      if (parseInt(values[3]) > 31 || parseInt(values[2]) > 12) {
	        var msg = printf("Le champ '%s' correspond à une date au format lunaire (jour '%s' et mois '%s')",
	          this.sLabel,
	          values[3],
	          values[2]
	        );
	         
	        // Attention, un seul printf() ne fonctionne pas
	        msg += ".\n\nVoulez vous néanmoins sauvegarder ?";
	        
	        if (!confirm(msg)) {
	          this.addError("birthDate", "N'a pas un format de date correct");
	        }
	      }
	    }
    },
    
    // date
    date: function() {
    	if (["now", "current"].include(this.sValue)) {
    		return;
    	}

      if (!this.sValue.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/))
        this.addError("date", "N'a pas un format de date correct");
    },
    
    // time
    time: function() {
    	if (["now", "current"].include(this.sValue)) {
    		return;
    	}

      if(!this.sValue.match(/^(\d{1,2}):?(\d{1,2}):?(\d{1,2})?$/))
        this.addError("time", "N'a pas un format d'heure correct");
    },
    
    // dateTime
    dateTime: function() {
    	if (["now", "current"].include(this.sValue)) {
    		return;
    	}
    	
      if (!this.sValue.match(/^(\d{4})-(\d{1,2})-(\d{1,2})[ \+](\d{1,2}):(\d{1,2}):(\d{1,2})$/))
        this.addError("dateTime", "N'a pas un format de date/heure correct");
    },
    
    // float
    'float': function() {
      this.sValue = this.sValue.toString().replace(',', '.');
      this.isNumeric();
      
      if (parseFloat(this.sValue) != this.sValue)
        this.addError("float", "N'est pas une valeur décimale (utilisez le . pour la virgule)");
    },
    
    // currency
    currency: function() {
      this['float']();
    },
    
    // pct
    pct: function() {
      if (!this.sValue.match(/^(\d+)(\.\d{1,4})?$/))
        this.addError("pct", "N'est pas une valeur décimale (utilisez le . pour la virgule)");
    },
    
    // text
    text: function() {
      this.str();
    },
  
    // html
    html: function() {
      this.str();
    },
    
    // url // (http|https|ftp)?(www\.)?([\w*])\.[a-zA-Z]{2,3}[/]?$
    url: function() {
      var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
      if (!this.sValue.match(regexp))
         this.addError("url", "Le format de l'url n'est pas valide");
    },
    
    // mask
    mask: function() {
      this.str();
    },
    
    // email
    email: function() {
      if (!this.sValue.match(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/))
        this.addError("email", "Le format de l'email n'est pas valide");
    },
    
    // code
    code: function() {
      if (!(this.oProperties.ccam || this.oProperties.cim10 || this.oProperties.adeli || this.oProperties.insee || 
            this.oProperties.product_order || this.oProperties.siret || this.oProperties.rib))
      this.addError("code", "Spécification de code invalide");
    },
    
    // password
    password: function() {
      this.str();
    }
  }
});

/***************/

function checkForm(oForm) {
  oForm = $(oForm);
  
  var oElementFirstFailed = null;
  var oFormErrors = [];
  var i = 0;
  
  // For each element in the form
  oForm.getElementsEx().each(function (oElement) {
    if (oElement) { // && (!oElement.tagName.match(/button/i) || Object.isArray(oElement))) { // If the element is not a button
      var isArray  = (!oElement.options && (Object.isArray(oElement) || Object.isElement(oElement[0])));
      var oFirstElement = (isArray?oElement[0]:oElement);
      
      // Element checker preparing and error checking
      ElementChecker.prepare(oElement);
      var sMsgFailed = ElementChecker.sLabel ? ElementChecker.sLabel : printf("%s (val:'%s', spec:'%s')", oFirstElement.name, $V(oElement), oFirstElement.className);
      var oErrors = ElementChecker.checkElement();
      
      // If errors, we append them to the error object
      if (oErrors.length) {
        oFormErrors.push({
          title: sMsgFailed,
          element: oFirstElement.name, 
          errors: oErrors
        });
        if (!oElementFirstFailed) oElementFirstFailed = oFirstElement;
        if (oLabel) oLabel.style.color = "#f00";
      }
      else {
        if (oLabel) oLabel.style.color = "#000";
      }
    }
  });
  
  if (oFormErrors.length) {
    var sMsg = "Merci de remplir/corriger les champs suivants : \n";
    oFormErrors.each(function (formError) {
      var oElement = oForm[formError.element];
      
      sMsg += "  "+String.fromCharCode(8226)+" "+formError.title.strip()+":\n";
      formError.errors.each(function (error) {
        sMsg += "     - "+error.message.strip()+"\n";
      });
    });
    alert(sMsg);
    
    if (oElementFirstFailed && oElementFirstFailed.type != "hidden") {
      oElementFirstFailed.select();
    }
    return false;
  }
  FormObserver.changes = 0;
  return true;
}

/** Validation d'un element de formulaire. 
  * Est utile pour la validation lors de la saisie du formulaire.
  */
function checkFormElement(oElement) {
	ElementChecker.prepare(oElement);
	
	// Recuperation de l'element HTML qui accueillera le message.
	var oMsg = $(oElement.id+'_message');
	if (oMsg && ElementChecker.oProperties.password) {
    ElementChecker.checkElement();
		if (ElementChecker.oErrors.length) {
			oMsg.innerHTML = 'Sécurité trop faible : <br />'+ElementChecker.getErrorMessage().gsub("\n", "<br />");
			oMsg.style.backgroundColor = '#FF7A7A';
		} 
		else {
			oMsg.innerHTML = 'Sécurité correcte';
			oMsg.style.backgroundColor = '#33FF66';
		}
	}
	if (oElement.value == '') {
		oMsg.innerHTML = '';
		oMsg.style.background = 'none';
	}
	return true;
}