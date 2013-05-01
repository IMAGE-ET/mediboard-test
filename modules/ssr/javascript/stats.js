/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

Stats = {
  reeducateurs: function(button) {
    var form = button.form;
    new Url('ssr', 'reeducateur_stats') .
      addElement(form.date) .
      addElement(form.type) .
      addElement(form.period) .
      requestModal(900, 800);
  }
};
