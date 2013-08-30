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

  testInsc : function(action) {
    var page = "";
    switch (action) {
      case "auto" :
        page = "ajax_test_insc_auto";
        break;
      case "saisi" :
        page = "ajax_test_insc_saisi";
        break;
      case "manuel" :
        Ccda.readCarte(function (data) {
          new Url("cda", "ajax_test_insc_manuel")
            .addParam("listPerson", data)
            .requestUpdate("test_insc");
        });
        break;
    }

    new Url("cda", page)
      .requestUpdate("test_insc");

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
      var amo = listBeneficiaires[i].getElementsByTagName("amo")[0];
      person["date"]        = getNodeValue("dateEnCarte", ident);

      if (person["date"].length === 0) {
        person["date"]        = getNodeValue("date", ident);
      }

      person["prenom"]      = getNodeValue("prenomUsuel", ident);
      person["nirCertifie"] = getNodeValue("nirCertifie", ident);
      var qualBenef         = getNodeValue("qualBenef"  , amo);

      if (person["nirCertifie"].length === 0 && qualBenef === '0') {
        person["nirCertifie"] = getNodeValue("nir", ident);
      }

      person["nom"]         = getNodeValue("nomUsuel"   , ident);
      listPerson.push(person);
    }
      callback(Object.toJSON(listPerson));
    }, 1000);
  }
};