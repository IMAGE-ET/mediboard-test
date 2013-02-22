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
    var url = new Url('cda', 'ajax_show_xml_type');
    url.addParam("name", name);
    url.requestUpdate("xmltype-view");
  },

  highlightMessage : function(form) {
    return Url.update(form, "highlighted");
  }
};