{{*
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_include module=system template=inc_pagination step=100 current=$page total=$total change_page=changePage}}

{{foreach from=$list item=_first}}
  <li>
    <a href="#" onclick="editFS('{{$_first->_id}}');">
      {{$_first}} (<strong style="color:#{{if $_first->sex == "f"}}ff1493{{/if}}{{if $_first->sex == "m"}}4682b4{{/if}};">{{$_first->sex}}</strong>)
    </a>
  </li>
{{/foreach}}

<div style="clear:both"></div>
{{mb_include module=system template=inc_pagination step=100 current=$page total=$total change_page=changePage}}
