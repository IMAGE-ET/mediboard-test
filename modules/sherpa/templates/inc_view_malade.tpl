<form name="editMalade" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_SpMalade_aed" />
<input type="hidden" name="malnum" value="{{$malade->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $malade->_id}}
    <th class="title modify" colspan="2">
 		  Affichage des informations du malade {{$malade->malnom}} {{$malade->malpre}}
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
  
  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le malade',objName:'{{$malade->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$malade->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'établissement courant</th>
  </tr>
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=patient value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>
      <a href="?m=dPpatients&tab=vw_full_patients&patient_id={{$patient->_id}}">
        {{$patient->_view}}
      </a>
    </td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
  <tr>
</table>

{{/if}}
