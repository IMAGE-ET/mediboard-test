{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=colspan value=3}}
{{if $prestation->_id}}
  {{assign var=colspan value=$colspan+1}}
{{/if}}
{{if $_show_comment_sejour}}
  {{assign var=colspan value=$colspan+1}}
{{/if}}

<th class="title" colspan="{{$colspan}}">Sejour</th>