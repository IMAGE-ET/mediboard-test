{{*
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<ul style="text-align: left;">
  {{foreach from=$codes item=_code}}
    <li>
      <span class="code">{{$_code->code}}</span>
      <div style="color: #888">
        {{$_code->short_name|spancate:40}}
      </div>
      <span id="type" class="informal" style="display:none">
         {{$_code->type}}
      </span>
    </li>
  {{/foreach}}
</ul>