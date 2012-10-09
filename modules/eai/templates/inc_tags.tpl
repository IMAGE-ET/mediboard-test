{{*
 * Actor tags EAI
 *  
 * @section EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<table class="main form">
  {{foreach from=$actor->_tags key=_tag_name item=_tag_value}}
  <tr>
    <th style="width: 50%" class="section">{{mb_label class=CInteropActor field=$_tag_name}}</th>
    <td>{{if $_tag_value}}{{$_tag_value}}{{else}}<div class="small-error">{{tr}}CInteropActor-no_tags{{/tr}}</div>{{/if}}</td>
  </tr>
  {{/foreach}}
</table>
