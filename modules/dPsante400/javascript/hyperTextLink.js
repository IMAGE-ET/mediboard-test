/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPsante400
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

HyperTextLink = {
  edit: function(object_id, object_class, link_id) {
    var url = new Url('sante400', 'ajax_edit_hypertext_link');
    url.addParam('object_id', object_id);
    url.addParam('object_class', object_class);
    if (link_id) {
      url.addParam('hypertext_link_id', link_id);
    }

    url.requestModal();
  },

  accessLink: function(name, link) {
    new Url().popup(1024, 768, name, null, null, link);
    return false;
  },

  getListFor: function(object_id, object_class) {
    var url = new Url('sante400', 'ajax_list_hypertextlinks');
    url.addParam('object_id', object_id);
    url.addParam('object_class', object_class);
    url.requestUpdate('list-hypertext_links');
  }
};