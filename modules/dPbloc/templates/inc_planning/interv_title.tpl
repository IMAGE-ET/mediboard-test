{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=colspan value=$_extra+$_duree+4}}
{{if !$_compact}}
  {{assign var=colspan value=$colspan+$_materiel}}
{{/if}}
<th class="title" colspan="{{$colspan}}">{{tr}}COperation{{/tr}}</th>