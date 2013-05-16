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
    var url = new Url("cda", "ajax_show_xml_type");
    url.addParam("name", name);
    url.requestUpdate("xmltype-view");
  },

  highlightMessage : function(form) {
    return Url.update(form, "highlighted");
  },

  action : function(action) {
    var url = new Url("cda", "vw_toolsdatatype");
    url.addParam("action", action);
    url.requestUpdate("resultAction");
  },

  actionTest : function(action) {
    var url = new Url("cda", "vw_testdatatype");
    url.addParam("action", action);
    url.requestUpdate("resultAction");
  },

  testInsc : function(action) {
    switch (action) {
      case "auto" :
        new Url("cda", "ajax_test_insc_auto")
          .requestUpdate("test_insc");
        break;
      case "manuel" :
        Ccda.readCarte(function (data) {
          new Url("cda", "ajax_test_insc_manuel")
            .addParam("listPerson", data)
            .requestUpdate("test_insc");
        });
        break;
      case "saisi" :
        new Url("cda", "ajax_test_insc_saisi")
          .requestUpdate("test_insc");
        break;
    }
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
  },

  readCarte : function(callback) {
    VitaleVision.getContent(VitaleVision.parseContent);
    setTimeout(function(){
    var listBeneficiaires = VitaleVision.xmlDocument.getElementsByTagName("listeBenef")[0].childNodes;
    var listPerson = [];
    for (var i = 0; i < listBeneficiaires.length; i++) {
      var person = {};
      var ident = listBeneficiaires[i].getElementsByTagName("ident")[0];
      person["date"] = getNodeValue("dateEnCarte", ident);
      person["prenom"] = getNodeValue("prenomUsuel", ident);
      person["nir"] = getNodeValue("nir", ident);

      listPerson.push(person);
    }
      callback(Object.toJSON(listPerson));
    }, 1000);
  }
};