<!-- $Id$ -->

<script type="text/javascript">
function checkCopieProtocole(chir_id){
  // Formulaire de selection du chir
  var oFormChir = document.selectFrm;
  oFormChir.chir_id.value = chir_id;

  // Non présent dans le select !
  if (oFormChir.chir_id.value != chir_id) {
    alert($T('CProtocole-msg-failed-noprat'));
    return;
  } 

  // Champs à modifier avant duplication
  var oForm = document.copieProtocole;
  oForm.protocole_id.value = "";
  oForm.chir_id.value = chir_id;
  oForm.libelle.value = "Copie de "+(oForm.libelle.value ? oForm.libelle.value : oForm.codes_ccam.value);
  oForm.submit();
}

</script>

<table class="form">
  <tr>
    <th class="category" colspan="2">Détails du protocole</th>
  </tr>

  <tr>
    <th>{{mb_label object=$protocole field=chir_id}}</th>
    <td><strong>{{mb_value object=$protocole field=chir_id}}</strong></td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$protocole field=for_sejour}}</th>
    <td>{{mb_value object=$protocole field=for_sejour}}</td>
  </tr>

  {{if $protocole->libelle}}
  <tr>
    <th>{{mb_label object=$protocole field=libelle}}</th>
    <td><em>{{mb_value object=$protocole field=libelle}}</em></td>
  </tr>
  {{/if}}

  <tr>
    <th>{{mb_label object=$protocole field=codes_ccam}}</th>
    <td class="text">
    {{foreach from=$protocole->_ext_codes_ccam item=curr_code}}
      <strong>{{$curr_code->code}}</strong><br />
      {{$curr_code->libelleLong}}<br />
    {{/foreach}}
    </td>
  </tr>
  
  {{if $protocole->DP}}
  <tr>
    <th>{{mb_label object=$protocole field=DP}}</th>
    <td>{{mb_value object=$protocole field=DP}}</td>
  </tr>
  {{/if}}
  
  {{if $protocole->temp_operation}}
  <tr>
    <th>{{mb_label object=$protocole field=temp_operation}}</th>
    <td>{{mb_value object=$protocole field=temp_operation}}</td>
  </tr>
	{{/if}}

  {{if $protocole->depassement}}
  <tr>	
    <th>{{mb_label object=$protocole field=depassement}}</th>
    <td>{{mb_value object=$protocole field=depassement}}</td>
	</tr>
  {{/if}}

  {{if $protocole->examen}}
  <tr>
    <th class="text" colspan="2"><strong>{{mb_label object=$protocole field=examen}}</strong></th>
  </tr>
         
  <tr>
    <td class="text" colspan="2">{{mb_value object=$protocole field=examen}}</td>
  </tr>
  {{/if}}

  {{if $protocole->materiel}}
  <tr>
    <th class="text" colspan="2"><strong>{{mb_label object=$protocole field=materiel}}</strong></th>
  </tr>
         
  <tr>
    <td class="text" colspan="2">{{mb_value object=$protocole field=materiel}}</td>
  </tr>
  {{/if}}

 {{if $protocole->convalescence}}
 <tr>
   <th class="text" colspan="2"><strong>{{mb_label object=$protocole field=convalescence}}</strong></th>
 </tr>
          
 <tr>
   <td class="text" colspan="2">{{mb_value object=$protocole field=convalescence}}</td>
 </tr>
 {{/if}}

 <tr>
   <th class="category" colspan="2">Détails de l'hospitalisation</th>
 </tr>
 
 <tr>
   <th>Admission en:</th>
   <td>
     {{tr}}CProtocole.type.{{$protocole->type}}{{/tr}}
   </td>
 </tr>

 <tr>
   <th>Durée d'hospitalisation:</th>
   <td>{{$protocole->duree_hospi}} nuits</td>
 </tr>

 {{if $protocole->rques_sejour}}
 <tr>
   <th class="text" colspan="2"><strong>{{mb_label object=$protocole field=rques_sejour}}</strong></th>
 </tr>
          
 <tr>
   <td class="text" colspan="2">{{mb_value object=$protocole field=convalescence}}</td>
 </tr>
 {{/if}}

 {{if $can->edit}}
 <tr>
   <td class="button" colspan="2">
   <!-- Formulaire permettant de dupliquer le protocole -->
   <form name="copieProtocole" action="?m={{$m}}" method="post" onsubmit="return checkCopieProtocole('{{$mediuser->_id}}')">
     <input type="hidden" name="dosql" value="do_protocole_aed" />
     <input type="hidden" name="del" value="0" />
		 {{foreach from=$protocole->_props key=propName item=_prop}}
		   {{if $propName.0 != "_"}}
       {{mb_field object=$protocole field=$propName hidden=1}}
			 {{/if}}
		 {{/foreach}}
     <button class="submit" type="button" onclick="this.form.onsubmit()">{{tr}}Duplicate{{/tr}}</button>
   </form>
   
   <!-- Modification du protocole-->
   <form name="modif" action="?" method="get">
     <input type="hidden" name="m" value="{{$m}}" />
     <input type="hidden" name="tab" value="vw_edit_protocole" />
     {{mb_key object=$protocole}}
     <button class="edit" type="submit">{{tr}}Modify{{/tr}}</button>
  </form>

  <!-- Suppression du protocole -->
  <form name="delProtocole" action="?m=dPplanningOp&amp;tab=vw_protocoles" method="post">
    <input type="hidden" name="dosql" value="do_protocole_aed" />
    <input type="hidden" name="del" value="1" />
     {{mb_key object=$protocole}}
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$protocole->_view|smarty:nodefaults|JSAttribute}}'})">
      {{tr}}Delete{{/tr}}
    </button>
  </form>
  
     </td>
   </tr>
  {{/if}}
</table>
      