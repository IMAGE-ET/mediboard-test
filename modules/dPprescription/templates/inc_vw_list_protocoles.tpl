{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
markAsSelected = function(element) {
  removeSelectedTr();
  $(element).up(1).addClassName('selected');
}

removeSelectedTr = function(){
  $("all_protocoles").select('.selected').each(function (e) {e.removeClassName('selected')});
}

Main.add(function(){
  Control.Tabs.create('list_protocoles_prescription');
});
</script>

<ul id="list_protocoles_prescription" class="control_tabs">
	{{foreach from=$protocoles key=owner item=_protocoles_by_owner}}
	<li><a href="#list_prot_{{$owner}}" {{if !$_protocoles_by_owner|@count}}class="empty"{{/if}}>{{tr}}CPrescription._owner.{{$owner}}{{/tr}}</a></li>
	{{/foreach}}
</ul>
<hr class="control_tabs" />

<table class="tbl" id="all_protocoles">
  {{foreach from=$protocoles key=owner item=_protocoles_by_owner}}
	<tbody id="list_prot_{{$owner}}" style="display: none;">
  {{if $_protocoles_by_owner|@count}}
  {{foreach from=$_protocoles_by_owner item=_protocoles_by_type key=class_protocole}}
  <tr>
    <th class="title">Contexte: {{tr}}CPrescription.object_class.{{$class_protocole}}{{/tr}}</th>
  </tr>
  {{foreach from=$_protocoles_by_type item=_protocoles key=type_protocole}}
  <tr>
    <th>Type: {{tr}}CPrescription.type.{{$type_protocole}}{{/tr}}</th>
  </tr>
  {{foreach from=$_protocoles item=protocole}}
  <tr {{if $protocole->_id == $protocoleSel_id}}class="selected"{{/if}}>
    <td>
      <div style="float:right">
	      <form name="delProt-{{$protocole->_id}}" action="?" method="post">
	        <input type="hidden" name="dosql" value="do_prescription_aed" />
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="del" value="1" />
	        <input type="hidden" name="prescription_id" value="{{$protocole->_id}}" />
	        <input type="hidden" name="callback" value="Prescription.reloadDelProt" />
	        <button class="trash notext" type="button" onclick="Protocole.remove(this.form)">Supprimer</button>
	      </form>
      </div>
      <a href="#{{$protocole->_id}}" onclick="markAsSelected(this); Protocole.edit('{{$protocole->_id}}','{{$protocole->praticien_id}}','{{$protocole->function_id}}')">
        {{$protocole->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/foreach}}
  {{else}}
	<tr>
		<td>Aucun protocole</td>
	</tr>
	{{/if}}
	</tbody>
  {{/foreach}}
</table>