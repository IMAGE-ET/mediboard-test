/**
 * $Id$
 *
 * @category Dossier patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

DocumentV2 = {
  refresh: function(container) {
    var matches = container.className.match(/documentsV2-(\w+)-(\d+) patient-(\d+) praticien-(\d*)/);

    if (!matches) {
      console.warn(printf("'%s' is not a valid document container", container.className));
      return;
    }

    var url = new Url("patients", "ajax_widget_documents");
    url.addParam("object_class", matches[1]);
    url.addParam("object_id"   , matches[2]);
    url.addParam("patient_id"  , matches[3]);
    url.addParam("praticien_id", matches[4]);
    url.addParam("_dummyarg_"  , container.identify());
    url.requestUpdate(container.down("div.count"));
  },

  viewDocs: function(patient_id, object_id, object_class) {
    var url = new Url("patients", "vw_all_docs");
    url.addParam("patient_id", patient_id);
    url.addParam("context_guid", object_class + "-" + object_id);
    url.requestModal("80%", "80%", {onClose: function() {
      console.log(object_class);
      console.log(object_id);
      var selector = printf("div.documentsV2-%s-%s", object_class, object_id);
      $$(selector).each(function(elt) {
        DocumentV2.refresh(elt);
      });
    }});
  }
};