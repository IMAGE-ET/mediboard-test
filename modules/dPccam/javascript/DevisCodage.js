/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

DevisCodage = {
  list: function(object_class, object_id) {
    var url = new Url('ccam', 'ajax_list_devis');
    url.addParam('object_class', object_class);
    url.addParam('object_id', object_id);
    url.requestUpdate('view-devis');
  },

  edit: function(devis_id, object_class, object_id) {
    var url = new Url('ccam', 'ajax_edit_devis');
    url.addParam('devis_id', devis_id);
    url.addParam('action', 'open');
    url.modal({
      height : -100,
      width: -50,
      onClose: function() {
        DevisCodage.list(object_class,object_id);
    }.bind(this)});
  },

  refresh: function(devis_id) {
    var url = new Url('ccam', 'ajax_edit_devis');
    url.addParam('devis_id', devis_id);
    url.addParam('action', 'refresh');
    url.requestUpdate('modalDevisContainer');
  },

  syncField: function(field, devis_id) {
    var form = getForm('editDevis-' + devis_id);

    if (form[field.name]) {
      $V(form[field.name], $V(field));
    }
  },

  print: function(devis_id) {
    var url = new Url('ccam', 'ajax_print_devis');
    url.addParam('devis_id', devis_id);
    url.pop()
  }
};