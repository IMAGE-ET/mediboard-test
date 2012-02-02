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
  if($('list_protocoles_prescription')){
    Control.Tabs.create('list_protocoles_prescription', true);
  }
});
</script>

<ul id="list_protocoles_prescription" class="control_tabs small">
	{{foreach from=$protocoles key=owner item=_protocoles_by_owner}}
	<li><a href="#list_prot_{{$owner}}" {{if !$_protocoles_by_owner|@count}}class="empty"{{/if}}>{{tr}}CPrescription._owner.{{$owner}}{{/tr}}</a></li>
	{{/foreach}}
</ul>
<hr class="control_tabs" />


<form name="delProt" action="?" method="post" class="prepared">
  <input type="hidden" name="dosql" value="do_prescription_aed" />
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="prescription_id" value="" />
  <input type="hidden" name="callback" value="Prescription.reloadDelProt" />
</form>

{{assign var=browser value=$smarty.session.browser}}

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
    <th>Type: {{tr}}CPrescription.type.{{$type_protocole}}{{/tr}} <small>({{$_protocoles|@count}})</small></th>
  </tr>
  {{foreach from=$_protocoles item=protocole}}
  <tr {{if $protocole->_id == $protocoleSel_id}}class="selected"{{/if}}>
    <td class="text">
      <div style="float:right">
      	{{* On n'affiche pas les boutons sous ie, probleme de performance avec beaucoup de protocoles *}}
      	{{if !($browser.name == "msie" && $browser.majorver <= 8)}}
		      {{if $can->admin}}
				    <button class="tick notext" type="button" onclick="Protocole.exportProtocole('{{$protocole->_id}}')">{{tr}}CPrescription.export_protocole{{/tr}}</button>
				  {{/if}}
				  <button class="print notext" type="button" onclick="Prescription.printPrescription('{{$protocole->_id}}')">{{tr}}Print{{/tr}}</button>
					
				  {{if $owner != "prat" || $app->user_id == $praticien_id || !$is_praticien}}
				    <button class="trash notext" type="button" onclick="if (confirm('{{tr}}CProtocole-confirm-deletion{{/tr}}{{$protocole->libelle|smarty:nodefaults|JSAttribute}}?'))Protocole.remove('{{$protocole->_id}}')">Supprimer</button>
				  {{/if}}
				{{/if}}
      </div>
      <a href="#{{$protocole->_id}}" onclick="markAsSelected(this); Protocole.edit('{{$protocole->_id}}','{{$protocole->praticien_id}}','{{$protocole->function_id}}')">
        {{$protocole->libelle}}
      </a>

			{{if $search}}
			  <div class="compact">
			  {{if $protocole->praticien_id}}
				  {{$protocole->_ref_praticien->_view}}
				{{/if}}
				{{if $protocole->function_id}}
          {{$protocole->_ref_function->_view}}
        {{/if}}
				{{if $protocole->group_id}}
          {{$protocole->_ref_group->_view}}
        {{/if}}
				</div>
			{{/if}}
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