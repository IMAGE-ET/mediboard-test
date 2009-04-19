<!-- $Id: vw_protocoles.tpl 6101 2009-04-16 13:20:04Z phenxdesign $ -->

<script type="text/javascript">
function copieProt(){
  var oForm = document.copieProtocole;
  
  // Formulaire de selection du chir
  var oFormChir = document.selectFrm;
  
  
  // Test pour savoir si le mediuser est un chir
  oFormChir.chir_id.value = "{{$mediuser->user_id}}";
  
  if(oFormChir.chir_id.value != "{{$mediuser->user_id}}") {
    alert("Vous n\'êtes pas un praticien, vous ne pouvez pas dupliquer ce protocole");
    return;
  } else {
    // le mediuser est un chir
    oForm.chir_id.value = "{{$mediuser->user_id}}";
  }
  
  oForm.libelle.value = "Copie de "+(oForm.libelle.value ? oForm.libelle.value : oForm.codes_ccam.value);
  oForm.submit();
}

</script>

<table class="form">
  <tr>
    <th class="category" colspan="2">Détails du protocole</th>
  </tr>

  <tr>
    <th>{{mb_label object=$protSel field=chir_id}}</th>
    <td><strong>{{mb_value object=$protSel field=chir_id}}</strong></td>
  </tr>
  
  <tr>
    <th>{{mb_label object=$protSel field=for_sejour}}</th>
    <td>{{mb_value object=$protSel field=for_sejour}}</td>
  </tr>

  {{if $protSel->libelle}}
  <tr>
    <th>{{mb_label object=$protSel field=libelle}}</th>
    <td><em>{{mb_value object=$protSel field=libelle}}</em></td>
  </tr>
  {{/if}}

  <tr>
    <th>{{mb_label object=$protSel field=codes_ccam}}</th>
    <td class="text">
    {{foreach from=$protSel->_ext_codes_ccam item=curr_code}}
      <strong>{{$curr_code->code}}</strong><br />
      {{$curr_code->libelleLong}}<br />
    {{/foreach}}
    </td>
  </tr>
  
  {{if $protSel->DP}}
  <tr>
    <th>{{mb_label object=$protSel field=DP}}</th>
    <td>{{mb_value object=$protSel field=DP}}</td>
  </tr>
  {{/if}}
  
  {{if $protSel->temp_operation}}
  <tr>
    <th>{{mb_label object=$protSel field=temp_operation}}</th>
    <td>{{mb_value object=$protSel field=temp_operation}}</td>
  </tr>
	{{/if}}

  {{if $protSel->depassement}}
  <tr>	
    <th>{{mb_label object=$protSel field=depassement}}</th>
    <td>{{mb_value object=$protSel field=depassement}}</td>
	</tr>
  {{/if}}

  {{if $protSel->examen}}
  <tr>
    <th class="text" colspan="2"><strong>{{mb_label object=$protSel field=examen}}</strong></th>
  </tr>
         
  <tr>
    <td class="text" colspan="2">{{mb_value object=$protSel field=examen}}</td>
  </tr>
  {{/if}}

  {{if $protSel->materiel}}
  <tr>
    <th class="text" colspan="2"><strong>{{mb_label object=$protSel field=materiel}}</strong></th>
  </tr>
         
  <tr>
    <td class="text" colspan="2">{{mb_value object=$protSel field=materiel}}</td>
  </tr>
  {{/if}}

 {{if $protSel->convalescence}}
 <tr>
   <th class="text" colspan="2"><strong>{{mb_label object=$protSel field=convalescence}}</strong></th>
 </tr>
          
 <tr>
   <td class="text" colspan="2">{{mb_value object=$protSel field=convalescence}}</td>
 </tr>
 {{/if}}

 <tr>
   <th class="category" colspan="2">Détails de l'hospitalisation</th>
 </tr>
 
 <tr>
   <th>Admission en:</th>
   <td>
     {{tr}}CProtocole.type.{{$protSel->type}}{{/tr}}
   </td>
 </tr>

 <tr>
   <th>Durée d'hospitalisation:</th>
   <td>{{$protSel->duree_hospi}} nuits</td>
 </tr>

 {{if $protSel->rques_sejour}}
 <tr>
   <th class="text" colspan="2"><strong>{{mb_label object=$protSel field=rques_sejour}}</strong></th>
 </tr>
          
 <tr>
   <td class="text" colspan="2">{{mb_value object=$protSel field=convalescence}}</td>
 </tr>
 {{/if}}

 {{if $can->edit}}
 <tr>
   <td class="button" colspan="2">
     <!-- Formulaire permettant de dupliquer le protocole -->
   <form name="copieProtocole" action="?m={{$m}}" method="post">
     <input type="hidden" name="dosql" value="do_protocole_aed" />
     <input type="hidden" name="del" value="0" />
     <input type="hidden" name="protocole_id" value="" />
     <input type="hidden" name="chir_id" value="" />
     <input type="hidden" name="type" value="{{$protSel->type}}" />
     <input type="hidden" name="DP" value="{{$protSel->DP}}" />
     <input type="hidden" name="convalescence" value="{{$protSel->convalescence}}" />
     <input type="hidden" name="rques_sejour" value="{{$protSel->rques_sejour}}" />
     <input type="hidden" name="pathologie" value="{{$protSel->pathologie}}" />
     <input type="hidden" name="septique" value="{{$protSel->septique}}" />
     <input type="hidden" name="codes_ccam" value="{{$protSel->codes_ccam}}" />
     <input type="hidden" name="libelle" value="{{$protSel->libelle}}" />
     <input type="hidden" name="temp_operation" value="{{$protSel->temp_operation}}" />
     <input type="hidden" name="examen" value="{{$protSel->examen}}" />
     <input type="hidden" name="materiel" value="{{$protSel->materiel}}" />
     <input type="hidden" name="duree_hospi" value="{{$protSel->duree_hospi}}" />
     <input type="hidden" name="rques_operation" value="{{$protSel->rques_operation}}" />
     <input type="hidden" name="depassement" value="{{$protSel->depassement}}" />
     <input type="hidden" name="forfait" value="{{$protSel->forfait}}" />
     <input type="hidden" name="fournitures" value="{{$protSel->fournitures}}" />
     <button class="submit" type="button" onclick="copieProt()">{{tr}}Duplicate{{/tr}}</button>
   </form>
   
  <!-- Modification du protocole-->
  <form name="modif" action="?" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="vw_edit_protocole" />
    {{mb_field object=$protSel field="protocole_id" hidden=1 prop=""}}
    <button class="edit" type="submit">{{tr}}Modify{{/tr}}</button>
  </form>

  <!-- Suppression du protocole -->
  <form name="delProtocole" action="?m=dPplanningOp&amp;tab=vw_protocoles" method="post">
    <input type="hidden" name="dosql" value="do_protocole_aed" />
    <input type="hidden" name="del" value="1" />
    <input type="hidden" name="protocole_id" value="{{$protSel->_id}}" />
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$protSel->_view|smarty:nodefaults|JSAttribute}}'})">
      {{tr}}Delete{{/tr}}
    </button>
  </form>
  
     </td>
   </tr>
  {{/if}}
</table>
      