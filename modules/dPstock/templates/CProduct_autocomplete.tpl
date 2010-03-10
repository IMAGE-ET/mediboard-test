{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<span class="view" style="float: left;">{{if $show_view}}{{$match->_view}}{{else}}{{$match->$f|emphasize:$input}}{{/if}}</span>

<div style="color: #666; font-size: 0.8em; padding-left: 0.5em; clear: both;">
  <div style="float: right; color: #999; text-align: center;">
    {{$match->loadRefsFwd()}}
    {{$match->_ref_category}}
  </div>

  {{$match->code}}
</div>