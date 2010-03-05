{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $praticien_id || $function_id}}
	<table class="tbl">
	  {{foreach from=$packs key=owner item=_packs_by_owner}}
	  {{if $_packs_by_owner|@count}}
	  <tr>
	    <th class="title">Liste des packs {{tr}}CPrescription._owner.{{$owner}}{{/tr}}</th>
	  </tr>
	  {{foreach from=$_packs_by_owner item=_packs key=type_pack}}
	  <tr>
	    <th>{{tr}}CPrescription.object_class.{{$type_pack}}{{/tr}}</th>
	  </tr>
	  {{foreach from=$_packs item=_pack}}
	  <tr {{if $_pack->_id == $pack->_id}}class="selected"{{/if}}>
	    <td>
	      <div style="float:right">
		      <form name="delPack-{{$_pack->_id}}" action="?" method="post">
		        <input type="hidden" name="dosql" value="do_prescription_protocole_pack_aed" />
		        <input type="hidden" name="m" value="dPprescription" />
		        <input type="hidden" name="del" value="1" />
		        <input type="hidden" name="prescription_protocole_pack_id" value="{{$_pack->_id}}" />
		        <button class="trash notext" type="button" onclick="Protocole.removePack(this.form)">Supprimer</button>
		      </form>
	      </div>
	      <a href="#{{$_pack->_id}}" onclick="Protocole.viewPack('{{$_pack->_id}}','{{$_pack->praticien_id}}','{{$_pack->function_id}}')">
	        {{$_pack->_view}}
	      </a>
	    </td>
	  </tr>
	  {{/foreach}}
	  {{/foreach}}
	  {{/if}}
	  {{/foreach}}
	
	  {{if !$packs|@count}}
	  <tr>
	    <th class="title">Liste des packs</th>
	  </tr>
	  <tr>
	    <td>Aucun pack disponible</td>
	  </tr>
	  {{/if}}
	</table>
{{/if}}