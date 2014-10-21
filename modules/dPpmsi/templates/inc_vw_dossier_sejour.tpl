{{*
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

{{if $sejour->_id}}
  {{mb_include module=pmsi template=inc_vw_dossier_sejour_pmsi object=$sejour}}
{{else}}
  <div class="big-info">
    Vous devez séléctionner un séjour pour accéder au dossier
  </div>
{{/if}}