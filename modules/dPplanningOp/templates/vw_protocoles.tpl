<!-- $Id$ -->

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
  
  
  if(oForm.libelle.value){
    oForm.libelle.value = "Copie de "+oForm.libelle.value;
  } else {
    oForm.libelle.value = "Copie de "+ oForm.codes_ccam.value;
  }
  oForm.submit();
}


{{if $dialog}}
var aProtocoles = new Array();
{{foreach from=$protocoles item=curr_protocole}}
aProtocoles[{{$curr_protocole->protocole_id}}] = {
  protocole_id     : {{$curr_protocole->protocole_id}},
  chir_id          : {{$curr_protocole->chir_id}},
  codes_ccam       : "{{$curr_protocole->codes_ccam}}",
  DP               : "{{$curr_protocole->DP}}",
  libelle          : "{{$curr_protocole->libelle|smarty:nodefaults|escape:"javascript"}}",
  _hour_op         : "{{$curr_protocole->_hour_op}}",
  _min_op          : "{{$curr_protocole->_min_op}}",
  examen           : "{{$curr_protocole->examen|smarty:nodefaults|escape:"javascript"}}",
  materiel         : "{{$curr_protocole->materiel|smarty:nodefaults|escape:"javascript"}}",
  convalescence    : "{{$curr_protocole->convalescence|smarty:nodefaults|escape:"javascript"}}",
  depassement      : "{{$curr_protocole->depassement}}",
  forfait          : "{{$curr_protocole->forfait}}",
  fournitures      : "{{$curr_protocole->fournitures}}",
  type             : "{{$curr_protocole->type}}",
  duree_hospi      : {{$curr_protocole->duree_hospi}},
  rques_sejour     : "{{$curr_protocole->rques_sejour|smarty:nodefaults|escape:"javascript"}}",
  rques_operation  : "{{$curr_protocole->rques_operation|smarty:nodefaults|escape:"javascript"}}",
  protocole_prescription_anesth_id: "{{$curr_protocole->protocole_prescription_anesth_id}}",
  protocole_prescription_chir_id:   "{{$curr_protocole->protocole_prescription_chir_id}}"
}
{{/foreach}}

function setClose(protocole_id) {
  window.opener.ProtocoleSelector.set(aProtocoles[protocole_id]);
  window.close();
}
{{/if}}

</script>

<table class="main">
  <tr>
    <td colspan="2">
      <a class="buttonnew" href="?m={{$m}}&amp;tab=vw_edit_protocole&amp;protocole_id=0">Créer un nouveau protocole</a>
          
      <form name="selectFrm" action="?" method="get">
      
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" {{if $dialog}} name="a" {{else}} name="tab" {{/if}} value="vw_protocoles" />
      <input type="hidden" name="dialog" value="{{$dialog}}" />

      <table class="form">
        <tr>
          <th><label for="chir_id" title="Filtrer les protocoles d'un praticien">Praticien</label></th>
          <td>
            <select name="chir_id" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les chirurgiens</option>
              {{foreach from=$listPrat item=curr_prat}}
              {{if $curr_prat->_ref_protocoles|@count}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}" {{if $chir_id == $curr_prat->user_id}} selected="selected" {{/if}}>
                {{$curr_prat->_view}} ({{$curr_prat->_ref_protocoles|@count}})
              </option>
              {{/if}}
              {{/foreach}}
            </select>
          </td>
          <th><label for="code_ccam" title="Filtrer avec un code CCAM">Code CCAM</label></th>
          <td>
            <select name="code_ccam" onchange="this.form.submit()">
              <option value="" >&mdash; Tous les codes</option>
              {{foreach from=$listCodes|smarty:nodefaults key=curr_code item=code_nomber}}
              <option value="{{$curr_code}}" {{if $code_ccam == $curr_code}} selected="selected" {{/if}}>
                {{$curr_code}} ({{$code_nomber}})
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      </form>    
      
    </td>
  </tr>

  <tr>
    {{if $dialog}}
    <td class="greedyPane">
    {{else}}
    <td class="halfPane">
    {{/if}}

      <table class="tbl">
        <tr>
          <th>Chirurgien &mdash; Acte CCAM</th>
        </tr>
        
        {{foreach from=$protocoles item=curr_protocole}}
        <tr>    
          <td class="text">
            {{if $dialog}}
            <a href="#" onclick="setClose({{$curr_protocole->protocole_id}})">
            {{else}}
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;protocole_id={{$curr_protocole->protocole_id}}">
            {{/if}}
              <strong>
                {{$curr_protocole->_ref_chir->_view}} 
                {{foreach from=$curr_protocole->_ext_codes_ccam item=curr_code}}
                &mdash; {{$curr_code->code}}
                {{/foreach}}
                {{if $curr_protocole->DP}}
                &mdash; {{$curr_protocole->DP}}
                {{/if}}
              </strong>
            </a>
            {{if $curr_protocole->libelle}}
              <em>[{{$curr_protocole->libelle}}]</em>
              <br />
            {{/if}}
            {{foreach from=$curr_protocole->_ext_codes_ccam item=curr_code}}
            {{$curr_code->libelleLong}} <br />
            {{/foreach}}
          </td>
        </tr>
        {{/foreach}}

      </table>

    </td>
    <td class="halfPane">
      {{if $protSel->protocole_id && !$dialog}}
        <table class="form">
          <tr>
            <th class="category" colspan="2">Détails du protocole</th>
          </tr>

          <tr>
            <th>Chirurgien :</th>
            <td colspan="3"><strong>{{$protSel->_ref_chir->_view}}</strong></td>
          </tr>

          {{if $protSel->libelle}}
          <tr>
            <th>Libellé :</th>
            <td><em>{{$protSel->libelle}}</em></td>
          </tr>
          {{/if}}

          <tr>
            <th>Acte Médical :</th>
            <td class="text">
            {{foreach from=$protSel->_ext_codes_ccam item=curr_code}}
              <strong>{{$curr_code->code}}</strong>
              <br />
              {{$curr_code->libelleLong}}
              <br />
            {{/foreach}}
            </td>
          </tr>
          {{if $protSel->DP}}
          <tr>
            <th>Diagnostic Principal</th>
            <td>{{$protSel->DP}}</td>
          </tr>
          {{/if}}
          <tr>
            <th>Temps opératoire :</th>
            <td>{{$protSel->temp_operation|date_format:$dPconfig.time}}</td>
          </tr>

          {{if $protSel->depassement}}
          <tr>	
            <th>Dépassement d'honoraire:</th>
            <td>{{$protSel->depassement}}€</td>
		      </tr>
	     	  {{/if}}

          {{if $protSel->examen}}
          <tr>
            <th class="text" colspan="2">Bilan Pré-op</th>
          </tr>
                 
          <tr>
            <td class="text" colspan="2">{{$protSel->examen|nl2br}}</td>
          </tr>
          {{/if}}
        
          {{if $protSel->materiel}}
          <tr>
            <th class="text" colspan="2">Matériel à prévoir</th>
          </tr>
                 
          <tr>
            <td class="text" colspan="2">{{$protSel->materiel|nl2br}}</td>
          </tr>
          {{/if}}
        
	        {{if $protSel->convalescence}}
	        <tr>
	          <th class="text" colspan="2">Convalescence</th>
	        </tr>
	                 
	        <tr>
	          <td class="text" colspan="2">{{$protSel->convalescence|nl2br}}</td>
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
	          <th class="text" colspan="2">Remarques du séjour</th>
	        </tr>
	                 
	        <tr>
	          <td class="text" colspan="2">{{$protSel->rques_sejour|nl2br}}</td>
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
                <button class="submit" type="button" onclick="copieProt()">Dupliquer</button>
              </form>
              
	            <!-- Modification du protocole-->
	            <form name="modif" action="?" method="get">
	              <input type="hidden" name="m" value="{{$m}}" />
	              <input type="hidden" name="tab" value="vw_edit_protocole" />
	              {{mb_field object=$protSel field="protocole_id" hidden=1 prop=""}}
	              <button class="modify" type="submit">Modifier</button>
	            </form>
	          
	            <!-- Suppression du protocole -->
	            <form name="delProtocole" action="?m=dPplanningOp&amp;tab=vw_protocoles" method="post">
	              <input type="hidden" name="dosql" value="do_protocole_aed" />
	              <input type="hidden" name="del" value="1" />
	              <input type="hidden" name="protocole_id" value="{{$protSel->_id}}" />
	              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le {{$protSel->_view|smarty:nodefaults|JSAttribute}}'})">
	                Supprimer
	              </button>
	            </form>
	          </td>
	        </tr>
        {{/if}}
      </table>
      {{/if}} 
   </td>
  </tr>
</table>