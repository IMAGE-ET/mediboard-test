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
    $("all_packs").select('.selected').each(function (e) {e.removeClassName('selected')});
  }
  Main.add(function(){
    if($('list_packs_prescription')){
      Control.Tabs.create('list_packs_prescription', true);
    }
  });
</script>

{{if $praticien_id || $function_id || $group_id}}
  <ul id="list_packs_prescription" class="control_tabs">
    {{foreach from=$packs key=owner item=_packs_by_owner}}
    <li><a href="#list_packs_{{$owner}}" {{if !$_packs_by_owner|@count}}class="empty"{{/if}}>{{tr}}CPrescription._owner.{{$owner}}{{/tr}}</a></li>
    {{/foreach}}
  </ul>
  <hr class="control_tabs" />
  {{else}}
  <div class="small-info">
    Veuillez sélectionner un praticien, un cabinet ou un établissement pour visualiser les packs
  </div>
{{/if}}

<table class="tbl" id="all_packs">
  {{foreach from=$packs key=owner item=_packs_by_owner}}
    <tbody id="list_packs_{{$owner}}" style="display: none;">
    {{if $_packs_by_owner|@count}}
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
          <a href="#{{$_pack->_id}}" onclick="markAsSelected(this); Protocole.viewPack('{{$_pack->_id}}')">
            {{$_pack->_view}}
          </a>
        </td>
      </tr>
      {{/foreach}}
      {{/foreach}}
    {{else}}
      <tr>
        <td>Aucun pack</td>
      </tr>
    {{/if}}
    </tbody>
  {{/foreach}}
</table>

