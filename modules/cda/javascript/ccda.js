/**
 * $Id$
 *
 * JS function Ccda
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Ccda = {

  showxml : function(name) {
    new Url("cda", "ajax_show_xml_type")
      .addParam("name", name)
      .requestUpdate("xmltype-view");
  },

  highlightMessage : function(form) {
    return Url.update(form, "highlighted");
  },

  action : function(action) {
    new Url("cda", "vw_toolsdatatype")
      .addParam("action", action)
      .requestUpdate("resultAction");
  },

  actionTest : function(action) {
    new Url("cda", "vw_testdatatype")
      .addParam("action", action)
      .requestUpdate("resultAction");
  },

  submitSaisieInsc : function(form) {
    var birthDate = form["birthDate"].value;
    var firstName = form["firstName"].value;
    var nir       = form["nir"].value;
    var nirKey    = form["nirKey"].value;

    new Url("cda", "ajax_test_insc_saisi")
      .addParam("birthDate"  , birthDate)
      .addParam("firstName"  , firstName)
      .addParam("nir"        , nir)
      .addParam("nirKey"     , nirKey)
      .addParam("accept_utf8", 1)
      .requestUpdate("test_insc");

    return false;
  }
};