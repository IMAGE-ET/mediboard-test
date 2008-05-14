<table class="tbl">

  <!-- Affichage des protocoles du praticien selectionne -->
  {{if $protocoles|@count}}
  <tr>
    <th>Liste des protocoles du praticien sélectionné</th>
  </tr>
  {{foreach from=$protocoles item=_protocoles key=type_protocole}}
  <tr>
    <th>{{tr}}CPrescription.object_class.{{$type_protocole}}{{/tr}}</th>
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
      <a href="#{{$protocole->_id}}" onclick="Protocole.edit('{{$protocole->_id}}','{{$protocole->praticien_id}}','{{$protocole->function_id}}')">
        {{$protocole->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}
  
  
  <!-- Affichage des protocoles de la fonction selectionne ou de la fonction du praticien -->
  {{if $protocoles_function|@count}}
  <tr>
    <th>Liste des protocoles du cabinet</th>
  </tr>
  {{foreach from=$protocoles_function item=_protocoles_function key=type_protocole_func}}
  <tr>
    <th>{{tr}}CPrescription.object_class.{{$type_protocole_func}}{{/tr}}</th>
  </tr>
   {{foreach from=$_protocoles_function item=protocole_func}}
  <tr {{if $protocole_func->_id == $protocoleSel_id}}class="selected"{{/if}}>
    <td>
      <div style="float:right">
	      <form name="delProt-{{$protocole_func->_id}}" action="?" method="post">
	        <input type="hidden" name="dosql" value="do_prescription_aed" />
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="del" value="1" />
	        <input type="hidden" name="prescription_id" value="{{$protocole_func->_id}}" />
	        <input type="hidden" name="callback" value="Prescription.reloadDelProt" />
	        <button class="trash notext" type="button" onclick="Protocole.remove(this.form)">Supprimer</button>
	      </form>
      </div>
      <a href="#{{$protocole_func->_id}}" onclick="Protocole.edit('{{$protocole_func->_id}}','{{$protocole_func->praticien_id}}','{{$protocole_func->function_id}}')">
        {{$protocole_func->_view}}
      </a>
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
  {{/if}}
</table>