/**
 * $Id$
 *
 * @category Etablissement
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Group = window.Group || {
  addedit: function(group_id) {
    var url = new Url('etablissement', 'ajax_vw_groups');
    url.addParam('group_id', group_id);
    url.requestModal("50%", "90%");
  },

  viewStructure: function(group_id) {
    var url = new Url('etablissement', 'vw_structure');
    url.addParam('group_id', group_id);
    url.popup(500, 500, 'structure_etab');
  },

  addeditLegalEntity: function(legal_entity_id) {
    var url = new Url('etablissement', 'ajax_vw_legal_entity');
    url.addParam('legal_entity_id', legal_entity_id);
    url.requestModal("50%", "70%");
  },

  editCEtabExterne: function(etab_id) {
    var url = new Url("etablissement", "ajax_etab_externe");
    url.addParam("etab_id", etab_id);
    url.requestModal("50%", "70%");
  },

  uploadSaveUID: function(uid) {
    var uploadForm = getForm("upload-import-file-form");

    var url = new Url("etablissement", "ajax_import_group");
    url.addParam("uid", uid);
    url.requestUpdate("import-steps");

    uploadForm.down(".upload-ok").show();
    uploadForm.down(".upload-error").hide();
  },
  uploadError: function() {
    var uploadForm = getForm("upload-import-file-form");

    uploadForm.down(".upload-ok").hide();
    uploadForm.down(".upload-error").show();
  },
  uploadReset: function() {
    var uploadForm = getForm("upload-import-file-form");

    uploadForm.down(".upload-ok").hide();
    uploadForm.down(".upload-error").hide();
  }
};