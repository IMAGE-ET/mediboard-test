{{*
 * $Id$
 *  
 * @category Etablissements
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<span class="view">{{if $show_view || !$f}}{{$match}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>