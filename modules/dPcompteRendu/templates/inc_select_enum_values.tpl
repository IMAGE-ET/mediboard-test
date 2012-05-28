{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage compteRendu
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{foreach from=$list key=_key item=_locale}}
  {{if $_key == '0'}}
  <option value="">&mdash; {{tr}}None{{/tr}}
</option>
  {{else}}
  <option value="{{if $_key !== '0'}}{{$_key}}{{/if}}">{{$_locale}}</option>
  {{/if}}
{{/foreach}}