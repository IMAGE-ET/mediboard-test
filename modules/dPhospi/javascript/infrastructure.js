/**
 * $Id$
 *
 * @category Hospi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

Infrastructure = window.Infrastructure || {
  modal_url : {},

  showInfrastructure :function (type_id, valeur_id, update_name) {
    var url = new Url("dPhospi", "inc_vw_infrastructure");
    url.addParam(type_id, valeur_id);
    url.requestUpdate(update_name);
  },

  addeditService :function (service_id) {
    var url = new Url("dPhospi", "ajax_addedit_service");
    url.addParam('service_id', service_id);
    url.requestModal(600, 500, {onClose: function () {
      var url = new Url("dPhospi", "ajax_list_infrastructure");
      url.addParam("type_name", 'services');
      url.requestUpdate('services');
    }});
  },

  addeditChambre: function (chambre_id, service_id) {
    var width = 900;
    var height = 600;
    if (chambre_id == 0) {
      width = 500;
      height = 300;
    }

    var url = new Url("dPhospi", "ajax_addedit_chambre");
    url.addParam('chambre_id', chambre_id);
    url.addParam('service_id', service_id);
    this.modal_url = url;
    url.requestModal(width, height, {onClose: function () {
      var url = new Url("dPhospi", "ajax_list_infrastructure");
      url.addParam("type_name", 'services');
      url.requestUpdate('list_services');
    }});
  },

  addeditChambreCallback: function(chambre_id, obj) {
  Infrastructure.addeditChambre(chambre_id, obj.service_id);
  },

  addLit: function(chambre_id, lit_id, update_name) {
    var url = new Url("dPhospi", "ajax_addedit_lit");
    url.addParam('chambre_id', chambre_id);
    url.addParam('lit_id', lit_id);
    url.requestUpdate(update_name, {insertion:"bottom"});
  },

  reloadLitLine: function (lit_id , chambre_id) {
    var container = "line_lit-CLit-"+lit_id;
    var url = new Url("dPhospi", "ajax_addedit_lit");
    url.addParam('chambre_id', chambre_id);
    url.addParam('lit_id', lit_id);
    (lit_id) ? url.requestUpdate(container) : this.modal_url.refreshModal();
  },
  confirmDeletionLit: function (form) {
    Modal.confirm(
      $T("CLit-confirm-Delete %s?", $V(form.nom)),
      {onOK: function() {
        Infrastructure.deleteLit(
          form,
          {onComplete: function() {
            Infrastructure.modal_url.refreshModal();
          }
          }
        );}
      }
    );
  },

  deleteLit: function(form, callback) {
    $V(form.del, "1");
    return onSubmitFormAjax(form, callback);
  },

  editLitLiaisonItem: function(lit_id) {
    var container = "edit_liaisons_items-" + lit_id;
    var url = new Url("dPhospi","ajax_edit_liaisons_items");
    url.addParam("lit_id", lit_id);
    url.requestUpdate(container);
  },

  setValueFormLit : function(name_input, value_input) {
  var form = getForm("editLit");
    $V(form[name_input], value_input);
  }

};