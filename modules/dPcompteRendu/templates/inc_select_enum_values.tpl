{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$list key=_key item=_locale}}
  <option value="{{if $_key !== 0}}{{$_key}}{{/if}}">{{$_locale}}</option>
{{/foreach}}