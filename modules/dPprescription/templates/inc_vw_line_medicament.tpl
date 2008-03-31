<tr>
  <th colspan="5">
  <a href="#produit{{$curr_line->_id}}" onclick="viewProduit({{$curr_line->_ref_produit->code_cip}})">
    {{$curr_line->_view}}
  </a>
  </th>
</tr>
<tr>
  <td rowspan="3">
    <button type="button" class="trash notext" onclick="Prescription.delLine({{$curr_line->_id}})">
      {{tr}}Delete{{/tr}}
    </button>
  </td>
  <td rowspan="3">
  {{assign var="color" value=#ccc}}
    {{if $curr_line->_nb_alertes}}
      
      {{if $curr_line->_ref_alertes.IPC || $curr_line->_ref_alertes.profil}}
        {{assign var="image" value="note_orange.png"}}
        {{assign var="color" value=#fff288}}
      {{/if}}  
      {{if $curr_line->_ref_alertes.allergie || $curr_line->_ref_alertes.interaction}}
        {{assign var="image" value="note_red.png"}}
        {{assign var="color" value=#ff7474}}
      {{/if}}  
      <img src="images/icons/{{$image}}" title="" alt="" 
           onmouseover="$('line-{{$curr_line->_id}}').show();"
           onmouseout="$('line-{{$curr_line->_id}}').hide();" />
    {{/if}}
    <div id="line-{{$curr_line->_id}}" class="tooltip" style="display: none; background-color: {{$color}}; border-style: ridge; padding-right:5px; ">
    {{foreach from=$curr_line->_ref_alertes_text key=type item=curr_type}}
      {{if $curr_type|@count}}
        <ul>
        {{foreach from=$curr_type item=curr_alerte}}
          <li>
            <strong>{{tr}}CPrescriptionLineMedicament-alerte-{{$type}}-court{{/tr}} :</strong>
            {{$curr_alerte}}
          </li>
        {{/foreach}}
        </ul>
      {{/if}}
    {{/foreach}}
    </div>
  </td>
  <td>
    <form name="editDates-{{$curr_line->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
      <table>
        <tr>
		    {{assign var=curr_line_id value=$curr_line->_id}}
		    <td style="border:none">
		    {{mb_label object=$curr_line field=debut}}
		    </td>
		    <td class="date" style="border:none;">
		    {{mb_field object=$curr_line field=debut form=editDates-$curr_line_id onchange="submitFormAjax(this.form, 'systemMsg'); calculFin(this.form, $curr_line_id);"}}
		    </td>
		    <td style="border:none; padding-left: 40px;">
		     {{mb_label object=$curr_line field=duree}}
		    </td>
		    <td style="border:none">
		     {{mb_field object=$curr_line field=duree onchange="submitFormAjax(this.form, 'systemMsg'); calculFin(this.form, $curr_line_id);" size="3" }}
		     {{mb_field object=$curr_line field=unite_duree onchange="submitFormAjax(this.form, 'systemMsg'); calculFin(this.form, $curr_line_id);" defaultOption="&mdash; Unité"}}
		    </td>
		    <td style="border:none">
		     {{mb_label object=$curr_line field=_fin}} 
		    </td>
		    <td class="date" style="border:none">
		     <div id="editDates-{{$curr_line->_id}}_fin"></div>
		    </td>    
      </tr>
    </table>
  </form>
  </td>
  <td>
    <button type="button" class="change notext" onclick="EquivSelector.init('{{$curr_line->_id}}','{{$curr_line->_ref_produit->code_cip}}');">
      Equivalents
    </button>
    <script type="text/javascript">
      if(EquivSelector.oUrl) {
        EquivSelector.close();
      }
      EquivSelector.init = function(line_id, code_cip){
        this.sForm = "searchProd";
        this.sView = "produit";
        this.sCodeCIP = code_cip
        this.sLine = line_id;
        this.selfClose = false;
        this.pop();
      }
      EquivSelector.set = function(code, line_id){
        Prescription.addEquivalent(code, line_id);
      }
    </script>
  </td>
  <td>
    <form action="?" method="post" name="editLineALD-{{$curr_line->_id}}">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
      <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}"/>
      <input type="hidden" name="del" value="0" />
      {{mb_field object=$curr_line field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
      {{mb_label object=$curr_line field="ald" typeEnum="checkbox"}}
    </form>
  </td>
</tr>
<tr>  
  <td colspan="3">
    <table style="width:100%">
    <tr>
   <td style="border:none; border-right: 1px solid #999; width:5%; text-align: left;">
    <form action="?m=dPprescription" method="post" name="editLine-{{$curr_line->_id}}" onsubmit="return checkForm(this);">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
      <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}"/>
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="_code_cip" value="{{$curr_line->_ref_produit->code_cip}}" />
    
      <input type="hidden" name="_delete_prises" value="0" />
      
      {{assign var=posologies value=$curr_line->_ref_produit->_ref_posologies}}
      <select name="no_poso" onchange="submitPoso(this.form, '{{$curr_line->_id}}');" style="width: 300px;">
        <option value="">&mdash; Posologies </option>
        {{foreach from=$curr_line->_ref_produit->_ref_posologies item=curr_poso}}
        <option value="{{$curr_poso->code_posologie}}"
          {{if $curr_poso->code_posologie == $curr_line->no_poso}}selected="selected"{{/if}}>
          {{$curr_poso->_view}}
        </option>
        {{/foreach}}
      </select>  
    </form>
    <br />
      <div id="buttonAddPrise-{{$curr_line->_id}}" style="display:none">
     <form name="addPrise{{$curr_line->_id}}" action="?" method="post" >
		  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
		  <input type="hidden" name="del" value="0" />
		  <input type="hidden" name="m" value="dPprescription" />
		  <input type="hidden" name="prise_posologie_id" value="" />
		  <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
		  <!-- Formulaire de selection de la quantite -->
		  <button type="button" class="remove notext" onclick="this.form.quantite.value--;">Moins</button>
		  {{mb_field object=$prise_posologie field=quantite}}
		  <button type="button" class="add notext" onclick="this.form.quantite.value++;">Plus</button>    
		  <!-- Selection du moment -->
		  <select name="moment_unitaire_id" style="width: 150px">      
		  <option value="">&mdash; Sélection du moment</option>
		  {{foreach from=$moments key=type_moment item=_moments}}
		     <optgroup label="{{$type_moment}}">
		     {{foreach from=$_moments item=moment}}
		     <option value="{{$moment->_id}}">{{$moment->_view}}</option>
		     {{/foreach}}
		     </optgroup>
		  {{/foreach}}
		  </select>	
		  <button type="button" class="submit notext" onclick="submitPrise(this.form);">Enregistrer</button>
		</form>
		<br />
   </div>
   </td>
    <td style="border:none; padding: 0;"><img src="images/icons/a_right.png" title="" alt="" /></td>
   <td style="border:none; text-align: left;">
      <div id="prises-{{$curr_line->_id}}">
        <!-- Parcours des prises -->
        {{include file="inc_vw_prises.tpl"}}
      </div>
    </td>
    </tr>
    </table>
    </td>
  </tr>    
    <tr>
    <td colspan="3">
    {{mb_label object=$curr_line field="commentaire"}}
    <form name="addCommentMedicament-{{$curr_line->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
      <input type="text" name="commentaire" size="80" value="{{$curr_line->commentaire}}" onchange="this.form.onsubmit();" />
    </form>
    
    <!-- Formulaire permettant de stopper la prise (seulement si type == "sejour")-->
    {{if $curr_line->type == "sejour"}}
    <form name="stopMedicament-{{$curr_line->_id}}" method="post" action="">
      <input type="hidden" name="m" vaue="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
      <input type="hidden" name="stoppe" value="{{$curr_line->stoppe}}" />
      {{if $curr_line->stoppe == "1"}}
      <button type="button" class="cancel" onclick="this.form.stoppe.value = 0; Prescription.submitFormStop(this.form)">Annuler l'arrêt</button>
      {{else}}
      <button type="button" class="tick" onclick="this.form.stoppe.value = 1; Prescription.submitFormStop(this.form))">Arrêter la ligne</button>
      {{/if}}
    </form>
    {{/if}}
    
	<!-- Formulaire permettant de créer une ligne contigue (dans tous les cas de prescriptions)-->
    
  </td>
</tr>
