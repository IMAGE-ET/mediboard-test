{{*
 * Show list tags EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<select name="tag">
  {{foreach from=$tags item=_tag}}
    <option value="{{$_tag}}">{{$_tag}}</option>
  {{/foreach}}
</select>