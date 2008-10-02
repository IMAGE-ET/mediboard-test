<table class="tbl">
  {{if $packs_praticien|@count}}
  <tr>
    <th>Liste des packs du praticien</th>
  </tr>
  {{foreach from=$packs_praticien item=_pack_praticien}}
  <tr {{if $_pack_praticien->_id == $pack->_id}}class="selected"{{/if}}>
    <td>
      <div style="float:right">
	      <form name="delPack-{{$_pack_praticien->_id}}" action="?" method="post">
	        <input type="hidden" name="dosql" value="do_prescription_protocole_pack_aed" />
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="del" value="1" />
	        <input type="hidden" name="prescription_protocole_pack_id" value="{{$_pack_praticien->_id}}" />
	        <button class="trash notext" type="button" onclick="Protocole.removePack(this.form)">Supprimer</button>
	      </form>
      </div>
      <a href="#{{$_pack_praticien->_id}}" onclick="Protocole.viewPack('{{$_pack_praticien->_id}}')">
        {{$_pack_praticien->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  
  {{if $packs_function|@count}}
  <tr>
    <th>Liste des packs du cabinet</th>
  </tr>
  {{foreach from=$packs_function item=_pack_cabinet}}
  <tr {{if $_pack_cabinet->_id == $pack->_id}}class="selected"{{/if}}>
    <td>
      <div style="float:right">
	      <form name="delPack-{{$_pack_cabinet->_id}}" action="?" method="post">
	        <input type="hidden" name="dosql" value="do_prescription_protocole_pack_aed" />
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="del" value="1" />
	        <input type="hidden" name="prescription_protocole_pack_id" value="{{$_pack_cabinet->_id}}" />
	        <button class="trash notext" type="button" onclick="Protocole.removePack(this.form)">Supprimer</button>
	      </form>
      </div>
      <a href="#{{$protocole->_id}}" onclick="Protocole.viewPack('{{$_pack_cabinet->_id}}','{{$_pack_cabinet->praticien_id}}','{{$_pack_cabinet->function_id}}')">
        {{$_pack_cabinet->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  {{/if}}
  
  {{if !$packs_function|@count && !$packs_praticien|@count}}
  <tr>
    <th>Liste des packs</th>
  </tr>
  <tr>
    <td>Aucun pack disponible</td>
  </tr>
  {{/if}}

</table>