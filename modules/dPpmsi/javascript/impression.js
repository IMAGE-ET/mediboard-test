/**
 * $Id$
 *
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org */

impression =  window.impression || {

  checkPrint: function(form) {
    if (checkForm(form)) {
      this.popPrint(form);
    }
    else {
      return;
    }
  },

  popPrint: function(form) {
    var url = new Url('pmsi', 'ajax_print_planning');
    url.addFormData(form);
    url.popup(900, 550, 'Planning');
  }
};