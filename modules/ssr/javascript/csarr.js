/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CsARR = {
  viewActivite: function(code) {
    new Url('ssr', 'vw_activite_csarr') .
      addParam('code', code) .
      requestModal(600);
  },
  
  viewActiviteStats: function(code) {
    new Url('ssr', 'vw_activite_csarr_stats') .
      addParam('code', code) .
      requestModal();
  },
  
  viewHierarchie: function(code) {
    new Url('ssr', 'vw_hierarchie_csarr') .
      addParam('code', code) .
      requestModal(600);
  }
};