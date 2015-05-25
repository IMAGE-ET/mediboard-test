/**
 * $Id$
 *
 * @category dPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

Salutation = {
  editSalutation: function (salutation_id, object_class, object_id, callback) {
    var url = new Url('dPpatients', 'ajax_edit_salutation');
    url.addParam('salutation_id', salutation_id);
    url.addParam('object_class', object_class);
    url.addParam('object_id', object_id);
    url.requestModal(500, 300, callback);
  },

  submitSalutation: function (form) {
    return onSubmitFormAjax(form, {
      onComplete: function () {
        Control.Modal.close();
      }
    });
  },

  manageSalutations: function (object_class, object_id) {
    var url = new Url('dPpatients', 'vw_manage_salutations');
    url.addParam('object_class', object_class);
    url.addParam('object_id', object_id);
    url.requestModal(800, 600);
  },

  reloadList: function (form) {
    form = form || getForm('search_salutations');
    return form.onsubmit();
  },

  filterContent: function (input, classe) {
    tr = $$(classe);

    tr.each(
      function (e) {
        e.show();
      }
    );

    var terms = $V(input);
    if (!terms) {
      return;
    }

    tr.each(
      function (e) {
        e.hide();
      }
    );

    terms = terms.split(",");
    tr.each(function (e) {
      terms.each(function (term) {
        if (e.getText().like(term)) {
          e.show();
        }
      });
    });
  },

  onFilterContent: function (input, classe) {
    if (input.value == "") {
      // Click on the clearing button
      Salutation.filterContent(input, classe);
    }
  }
};