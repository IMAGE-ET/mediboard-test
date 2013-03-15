/* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7795 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CdARR = {
  viewActiviteStats: function(code) {
    new Url('ssr', 'vw_activite_cdarr_stats') .
      addParam('code', code) .
      requestModal(700);
  }
};