{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
if(!self.changeEtablissementId) {
  changeEtablissementId = function(oForm) {return false;}
}
</script>

{{if $listEtab|@count}}
<select name="etablissement_transfert_id" onchange="changeEtablissementId(this.form);">
<option value="">&mdash; Etab. de transfert</option>
{{foreach from=$listEtab item="etab"}}
<option value="{{$etab->_id}}" {{if $etab->_id == $etabSelected}}selected="selected"{{/if}}>
  {{$etab->_view}}
</option>
{{/foreach}}
</select>
{{/if}}