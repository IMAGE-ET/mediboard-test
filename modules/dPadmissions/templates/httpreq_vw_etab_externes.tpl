{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $listEtab|@count}}
<select name="etablissement_transfert_id">
<option value="">&mdash; Etab. de transfert</option>
{{foreach from=$listEtab item="etab"}}
<option value="{{$etab->_id}}" {{if $etab->_id == $etabSelected}}selected="selected"{{/if}}>
  {{$etab->_view}}
</option>
{{/foreach}}
</select>
{{/if}}