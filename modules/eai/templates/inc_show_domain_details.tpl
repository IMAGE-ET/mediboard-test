{{*
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{if $domain->_count_objects > 0}}
  <ul>
    {{foreach from=$domain->_detail_objects item=_detail_object}}
      <li><strong>{{tr}}{{$_detail_object.object_class}}{{/tr}}</strong> : {{$_detail_object.total}}</li>
    {{/foreach}}
  </ul>
{{/if}}