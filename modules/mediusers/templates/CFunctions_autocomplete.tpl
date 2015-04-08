{{*
 * $Id$
 *  
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{* Do not add carriage returns or it will add whitespace in the input *}}
<div style="border-left: 3px solid #{{$match->color}}; padding-left: 2px; margin: -1px;">
  <span class="view" {{if $match->actif == 0}}style="text-decoration: line-through;"{{/if}}>{{if $show_view || !$f}}{{$match}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>
</div>