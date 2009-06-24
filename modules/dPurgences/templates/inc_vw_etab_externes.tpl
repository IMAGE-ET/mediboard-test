{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $listEtab|@count}}
<select name="etablissement_transfert_id" onchange="submitSejour(this.form)">
<option value="">&mdash; Etablissement de transfert</option>
{{foreach from=$listEtab item="etab"}}
<option value="{{$etab->_id}}" {{if $etab->_id == $_transfert_id}}selected="selected"{{/if}}>{{$etab->_view}}</option>
{{/foreach}}
</select>
{{/if}}