/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CsARR = {
  viewCode: function(code) {
    new Url('ssr', 'vw_code_csarr') .
      addParam('code', code) .
      requestModal(500);
  },
  
  viewCodeStats: function(code) {
    new Url('ssr', 'vw_code_csarr_stats') .
      addParam('code', code) .
      requestModal();
  },
  
  viewHierarchie: function(hierarchie) {
  }
};