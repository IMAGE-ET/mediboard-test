<tbody id="line_medicament_{{$curr_line->_id}}" class="hoverable">
  <tr>
    <th colspan="5" id="th_line_{{$curr_line->_id}}" {{if $curr_line->date_arret}}style="background-color:#aaa";{{/if}}{{if $curr_line->_traitement == 1}}style="background-color:#7c7"{{/if}}>
    <div style="float:left">
    {{if !$curr_line->_traitement}}
       {{if !$curr_line->signee}}
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
          {{if $prescription->type == "sejour"}}
          this.sInLivret = "1";
          {{else}}
          this.sInLivret = "0";
          {{/if}}
          this.selfClose = false;
          this.pop();
        }
        EquivSelector.set = function(code, line_id){
          Prescription.addEquivent(code, line_id);
        }
      </script>
      {{/if}}
    
     {{if $curr_line->_ref_prescription->object_id}}
      {{if !$curr_line->signee}}
	      <form action="?" method="post" name="editLineALD-{{$curr_line->_id}}">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	        <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
	        <input type="hidden" name="del" value="0" />
	        {{mb_field object=$curr_line field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
	        {{mb_label object=$curr_line field="ald" typeEnum="checkbox"}}
	      </form> 
      {{else}}
        {{mb_label object=$curr_line field="ald" typeEnum="checkbox"}}:
        {{if $curr_line->ald}}
          Oui
        {{else}}
          Non
        {{/if}} 
      {{/if}}
	   {{/if}}
	  {{/if}}
	     {{if !$curr_line->signee && $curr_line->_ref_prescription->object_id}} 
	      <form action="?" method="post" name="editLineTraitement-{{$curr_line->_id}}">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_traitement_aed" />
	        <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}"/>
	        <input type="hidden" name="del" value="0" />
	        <input type="hidden" name="prescription_id" value="{{$prescription_reelle->_id}}" />
	        <input type="hidden" name="type" value="{{$prescription_reelle->type}}" />
	        {{mb_field object=$curr_line field=_traitement typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
	        {{mb_label object=$curr_line field=_traitement typeEnum="checkbox"}}
	      </form>
	      {{/if}}  
    </div>
      <!-- AFfichage du praticien -->
      <div style="float: right">
      {{if !$curr_line->_traitement}}
      {{if $curr_line->_ref_praticien->_id}}
        {{$curr_line->_ref_praticien->_view}}
        {{if $prescription->object_id}}  
	        {{if $curr_line->signee}}
	          {{if $curr_line->_ref_praticien->_id == $app->user_id}}
	            <form name="delValidation-{{$curr_line->_id}}" action="" method="post">
	              <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
	              <input type="hidden" name="signee" value="0" />
	              <button type="button" class="cancel" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}','','medicament') } }  )">Annuler la validation</button>
	            </form>
	          {{/if}}
	        {{else}}
	          {{if $curr_line->_ref_praticien->_id == $app->user_id}}
	            <form name="validation-{{$curr_line->_id}}" action="" method="post">
	              <input type="hidden" name="dosql" value="do_valide_all_lines_aed" />
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
	              <button type="button" class="tick" onclick="submitFormAjax(this.form,'systemMsg');">Signer</button>
	            </form>
	          {{/if}}
	        {{/if}}
	      {{/if}}
      {{/if}}
      {{else}}
      Médecin traitant
      {{/if}}
      </div>
    <a href="#produit{{$curr_line->_id}}" onclick="viewProduit({{$curr_line->_ref_produit->code_cip}})">
      {{$curr_line->_view}}
    </a>
    </th>
  </tr>
  {{if !$curr_line->_traitement && $curr_line->_ref_prescription->object_id}}
  <tr>  
    <td style="text-align: center">
    {{if !$curr_line->_ref_produit->inLivret && $prescription->type == "sejour"}}
        <img src="images/icons/warning.png" alt="Produit non présent dans le livret Thérapeutique" title="Produit non présent dans le livret Thérapeutique" />
        <br />
    {{/if}}  
    </td>
    <td colspan="2">
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
				    {{if $curr_line->signee == 0}}
				    <td class="date" style="border:none;">
				      {{mb_field object=$curr_line field=debut form=editDates-$curr_line_id onchange="syncDateSubmit(this.form, $curr_line_id, this.name);"}}
				    </td>
				    {{else}}
				    <td style="border:none">
				      {{if $curr_line->debut}}
				        {{$curr_line->debut|date_format:"%d/%m/%Y"}}
				      {{else}}
				        -
				      {{/if}}				   
				    </td>
				    {{/if}}
				    <td style="border:none;">
				     {{mb_label object=$curr_line field=duree}}
				    </td>
				    <td style="border:none">
				    {{if $curr_line->signee == 0}}
				     {{mb_field object=$curr_line field=duree onchange="syncDateSubmit(this.form, $curr_line_id, this.name);" size="3" }}
				     {{mb_field object=$curr_line field=unite_duree onchange="syncDateSubmit(this.form, $curr_line_id, this.name);"}}
				    {{else}}
				      {{if $curr_line->duree}}
				        {{$curr_line->duree}}
				      {{else}}
				        -
				      {{/if}}
				      {{if $curr_line->unite_duree}}
				        {{tr}}CPrescriptionLineMedicament.unite_duree.{{$curr_line->unite_duree}}{{/tr}}	      
				      {{/if}}
				    {{/if}}
				    </td>
				    <td style="border:none">
				     {{mb_label object=$curr_line field=_fin}} 
				    </td>
				    {{if $curr_line->signee == 0}}
				    <td class="date" style="border:none;">
				      {{mb_field object=$curr_line field=_fin form=editDates-$curr_line_id onchange="syncDateSubmit(this.form, $curr_line_id, this.name);"}}
				    </td>
				    {{else}}
				    <td style="border:none">
				      {{if $curr_line->_fin}}
				        {{$curr_line->_fin|date_format:"%d/%m/%Y"}}
				      {{else}}
				        -
				      {{/if}}				   
				    </td>
				    {{/if}}
        </tr>
      </table>
    </form>
    </td>
    <td>
      <!-- Formulaire permettant de stopper la prise (seulement si type == "sejour" ou si type == "pre_admission" )-->
      {{if $prescription->type == "sejour" || $prescription->type == "pre_admission"}}
        <div id="stop_{{$curr_line->_id}}">
          {{include file="inc_vw_stop_medicament.tpl"}}
        </div>
      {{/if}}
    </td>
  </tr>
  {{/if}}  
  <tr>  
	  <td style="text-align: center">
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
        {{if !$curr_line->signee}}
        <select name="no_poso" onchange="submitPoso(this.form, '{{$curr_line->_id}}');" style="width: 230px;">
          <option value="">&mdash; Posologies automatiques</option>
          {{foreach from=$curr_line->_ref_produit->_ref_posologies item=curr_poso}}
          <option value="{{$curr_poso->code_posologie}}"
            {{if $curr_poso->code_posologie == $curr_line->no_poso}}selected="selected"{{/if}}>
            {{$curr_poso->_view}}
          </option>
          {{/foreach}}
        </select>  
        {{else}}
          {{if $curr_line->no_poso}}
            {{$curr_line->_ref_posologie->_view}}
          {{else}}
            Aucune posologie sélectionnée
          {{/if}}
        {{/if}}
      </form>
      {{if !$curr_line->signee}}
        {{assign var=curr_line_id value=$curr_line->_id}}
				  <select name="selShowDivPoso" onchange="selDivPoso(this.value,'{{$curr_line->_id}}');">
				    <option value="">&mdash; Posologies manuelles</option>
				    <option value="moment">Moment</option>
				    <option value="foisPar">x fois par y</option>
				    <option value="tousLes">tous les x y</option>
				  </select>
				  <br />
				  <div id="foisPar{{$curr_line->_id}}">
						<form name="addPriseFoisPar{{$curr_line->_id}}" action="?" method="post" >
					    <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
					    <input type="hidden" name="del" value="0" />
					    <input type="hidden" name="m" value="dPprescription" />
					    <input type="hidden" name="prise_posologie_id" value="" />
					    <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
						  Quantité: 
						  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseFoisPar$curr_line_id}}
						  {{$curr_line->_unite_prise}}(s)
						  {{mb_field object=$prise_posologie field=nb_fois size=3 increment=1 form=addPriseFoisPar$curr_line_id}} fois par 
						  {{mb_field object=$prise_posologie field=unite_fois}}
					    <button type="button" class="submit notext" onclick="submitPrise(this.form);">Enregistrer</button>
						</form>
				  </div>
				<div id="moment{{$curr_line->_id}}" style="display: none">
				  <form name="addPriseMoment{{$curr_line->_id}}" action="?" method="post" >
					  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
					  <input type="hidden" name="del" value="0" />
					  <input type="hidden" name="m" value="dPprescription" />
					  <input type="hidden" name="prise_posologie_id" value="" />
					  <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
					  Quantité: 
					  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseMoment$curr_line_id}}
					  {{$curr_line->_unite_prise}}(s)
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
				</div>
          <div id="tousLes{{$curr_line->_id}}" style="display: none">
          	<form name="addPriseTousLes{{$curr_line->_id}}" action="?" method="post" >
					    <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
					    <input type="hidden" name="del" value="0" />
					    <input type="hidden" name="m" value="dPprescription" />
					    <input type="hidden" name="prise_posologie_id" value="" />
					    <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
		          Quantité: 
						  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseTousLes$curr_line_id}}
		          {{$curr_line->_unite_prise}}(s)
		           tous les
						  {{mb_field object=$prise_posologie field=nb_tous_les size=3 increment=1 form=addPriseTousLes$curr_line_id}}				   
						  {{mb_field object=$prise_posologie field=unite_tous_les}}
				      <button type="button" class="submit notext" onclick="submitPrise(this.form);">Enregistrer</button>
				    </form>  
				  </div>
				{{/if}}
	    </td>
      <td style="border:none; padding: 0;"><img src="images/icons/a_right.png" title="" alt="" /></td>
	    <td style="border:none; text-align: left;">
	      {{if !$curr_line->signee}}
        <div id="prises-{{$curr_line->_id}}">
          <!-- Parcours des prises -->
          {{include file="inc_vw_prises.tpl"}}
        </div>
        {{else}}
          {{foreach from=$curr_line->_ref_prises item=prise}}
            {{if $prise->quantite}}
              {{$prise->_view}}, 
            {{/if}}
          {{/foreach}}
        {{/if}}
      </td>
      </tr>
      </table>
      </td>
    </tr>    
    <tr>  
      <td>
      {{if !$curr_line->signee}}  
        <button type="button" class="trash notext" onclick="Prescription.delLine({{$curr_line->_id}})">
          {{tr}}Delete{{/tr}}
        </button>
      {{/if}}
      </td>
      <td colspan="4">
      {{if !$curr_line->_traitement}}
        <div style="float: right">
        <!-- Creation d'une ligne avec des dates contiguës -->
        <form name="addLineCont-{{$curr_line->_id}}" method="post" action="">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_add_line_contigue_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
          <button type="button" class="new" onclick="submitFormAjax(document.forms['addLineCont-{{$curr_line->_id}}'],'systemMsg')">Ajouter une ligne</button>
        </form>
        </div>
        {{/if}}
      {{mb_label object=$curr_line field="commentaire"}}
      {{if !$curr_line->signee}}
      <form name="addCommentMedicament-{{$curr_line->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_id" value="{{$curr_line->_id}}" />
        <input type="text" name="commentaire" size="90" value="{{$curr_line->commentaire}}" onchange="this.form.onsubmit();" />
      </form>
      {{else}}
        {{$curr_line->commentaire}}
      {{/if}}
      </td>
    </tr>
</tbody>