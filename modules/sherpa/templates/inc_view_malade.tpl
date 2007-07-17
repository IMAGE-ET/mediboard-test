      {{if $can->edit}}
      <form name="editMalade" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_SpMalade_aed" />
      <input type="hidden" name="malnum" value="{{$malade->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $malade->_id}}
          <th class="title modify" colspan="2">
     	 				Affichage des informations du malade {{$malade->malnom}}
          </th>
          {{else}}
          <th class="title" colspan="2">
      				Affichage des informations d'un malade
          </th>
          {{/if}}
        </tr>
        {{if $malade->_id}}
        <tr>
      		<th>{{mb_label object=$malade field="malnom"}}</th>
      		<td>{{mb_value object=$malade field="malnom"}}</td>
        </tr>
        <tr>
      		<th>{{mb_label object=$malade field="malpre"}}</th>
      		<td>{{mb_value object=$malade field="malpre"}}</td>
        </tr>
        <tr>
      		<th>{{mb_label object=$malade field="datnai"}}</th>
      		<td>{{mb_value object=$malade field="datnai"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $malade->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le malade',objName:'{{$malade->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
        {{/if}}        
      </table>
      </form>
      {{/if}}
