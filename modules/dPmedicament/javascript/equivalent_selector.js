/* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

var EquivSelector = {
  sForm     : null,
  sView     : null,
  sSearch   : null,
  sCodeCIP  : null,
  sInLivret : null,
  sLine     : null,
  oUrl      : null,
  selfClose : true,
  options : {
    width : 700,
    height: 400
  },

  pop: function() {
    var oForm = document[this.sForm];
    this.oUrl = new Url();
    this.oUrl.setModuleAction("dPmedicament", "vw_equivalents");
    this.oUrl.addParam("code_cip", this.sCodeCIP);
    this.oUrl.addParam("line_id", this.sLine);
    this.oUrl.addParam("inLivret", this.sInLivret);
    this.oUrl.popup(this.options.width, this.options.height, "Equivalent Selector");
  },
  
  set: function(code, line) {
    var oForm = document[this.sForm];
    oForm[this.sView].value = nom;
  },
  
  // Peut être appelé sans contexte : ne pas utiliser this
  close: function() {
    EquivSelector.oUrl.close();
  }
  
}
