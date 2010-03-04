/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
ElementSelector = {
  sForm     : null,
  sLibelle  : null,
  sType     : null,
  sElement_id : null,
  options : {
    width : 350,
    height: 450
  },
  prepared : {
    element_id: null
  },
  pop: function() {
    var oForm = getForm(this.sForm);
    this.oUrl = new Url("dPprescription", "element_selector");
    this.oUrl.addParam("libelle", oForm[this.sLibelle].value);
    this.oUrl.addParam("type"  , this.sType);
    this.oUrl.popup(this.options.width, this.options.height, "Element Prescription Selector");
  },
  
  set: function(element_id) { 
    this.prepared.element_id = element_id;
    
    // Lancement de l'execution du set
    window.setTimeout( window.ElementSelector.doSet , 1);
  },
  
  doSet: function(){
    var oForm = getForm(ElementSelector.sForm);
    $V(oForm[ElementSelector.sElement_id], ElementSelector.prepared.element_id);
  },
  
  // Peut être appelé sans contexte : ne pas utiliser this
  close: function() {
    ElementSelector.oUrl.close();
  }
};
