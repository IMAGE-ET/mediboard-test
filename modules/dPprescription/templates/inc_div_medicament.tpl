<script type="text/javascript">

// Calcul de la fin d'un traitement
calculFin = function(oForm, curr_line_id){

  var sDate = oForm.debut.value;
  var nDuree = parseInt(oForm.duree.value, 10);
  var nType = oForm.unite_duree.value;
    
  oDiv = $('editDates-'+curr_line_id+'_fin');
  
  if (!sDate || !nDuree) {
    oDiv.innerHTML = "";  
    return;
  }
  
  var dDate = Date.fromDATE(sDate);  
  if(nType == "jour"){
    dDate.addDays(nDuree);
  }
  
  // TODO: A modifier
  if(nType == "minute" || nType == "heure" || nType == "demi_journee"){
    // ne rien faire
  }
  if(nType == "semaine"){ dDate.addDays(nDuree*7); }
  if(nType == "quinzaine"){ dDate.addDays(nDuree*14); }
  if(nType == "mois"){dDate.addDays(nDuree*30); }
  if(nType == "trimestre"){dDate.addDays(nDuree*90); }
  if(nType == "semestre"){dDate.addDays(nDuree*180); }
  if(nType == "an"){dDate.addDays(nDuree*365); }
  
   // Update fields
	Form.Element.setValue(oForm._fin, dDate.toDATE());
  oDiv.innerHTML = dDate.toLocaleDate();
}

viewButtonAddPrise = function(curr_line_id){
  $('addPriseForm'+curr_line_id).show();
}


// Fonction lancée lors de la modfication de la posologie
submitPoso = function(oForm, curr_line_id){
  // On affiche ou on cache le bouton ajouter une prise
  initPoso(oForm, curr_line_id);
  
  // Suppression des prises de la ligne de prescription
  oForm._delete_prises.value = "1";
  submitFormAjax(oForm, 'systemMsg', { onComplete: 
    function(){
      // Preparation des prises pour la nouvelle posologie selectionnée
      var url = new Url;
      url.setModuleAction("dPprescription", "httpreq_prescription_prepare");
      url.addParam("prescription_line_id", curr_line_id);
      url.addParam("no_poso", oForm.no_poso.value);
      url.addParam("code_cip", oForm._code_cip.value);
      url.requestUpdate('prises-'+curr_line_id, { waitingText: null });
    } 
   }
  );
}

initPoso = function(oForm, curr_line_id){
  if(oForm.no_poso.value == ''){
    $('buttonAddPrise-'+curr_line_id).hide(); 
  } else {
    $('buttonAddPrise-'+curr_line_id).show();
  }
}


reloadPrises = function(prescription_line_id){
  url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_prises");
  url.addParam("prescription_line_id", prescription_line_id);
  url.requestUpdate('prises-'+prescription_line_id, { waitingText: null });
}

submitPrise = function(oForm){
  submitFormAjax(oForm, 'systemMsg', { onComplete:
    function(){
      reloadPrises(oForm.prescription_line_id.value);
      oForm.quantite.value = 0;
      oForm.moment_unitaire_id.value = "";
  } });
}


{{assign var=nb_med value=$prescription->_ref_lines_med_comments.med|@count}}
{{assign var=nb_comment value=$prescription->_ref_lines_med_comments.comment|@count}}
{{assign var=nb_total value=$nb_med+$nb_comment}}

Prescription.refreshTabHeader("div_medicament","{{$nb_total}}");

</script>

<!-- Formulaire d'ajout de ligne dans la prescription -->
<form action="?m=dPprescription" method="post" name="addLine" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPprescription" />
  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
  <input type="hidden" name="prescription_line_id" value=""/>
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="prescription_id" value="{{$prescription->_id}}"/>
  <input type="hidden" name="code_cip" value=""/>
</form>


<!-- Affichage des div des medicaments et autres produits -->
  <form action="?" method="get" name="searchProd" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLine(this.value); this.value = '';">
      <option value="">&mdash; Médicaments les plus utilisés</option>
      {{foreach from=$listFavoris.medicament item=curr_prod}}
      <option value="{{$curr_prod->code_cip}}">
        {{$curr_prod->libelle}}
      </option>
      {{/foreach}}
    </select>
    <button class="new" onclick="$('add_line_comment_med').show();">Ajouter une ligne de commentaire</button>
    <br />
	  <input type="text" name="produit" value=""/>
	  <div style="display:none;" class="autocomplete" id="produit_auto_complete"></div>
	  <button type="button" class="search" onclick="MedSelector.init('produit');">Produits</button>
	  <button type="button" class="search" onclick="MedSelector.init('classe');">Classes</button>
	  <button type="button" class="search" onclick="MedSelector.init('composant');">Composants</button>
	  <button type="button" class="search" onclick="MedSelector.init('DC_search');">DCI</button>
	  <input type="hidden" name="code_cip" onchange="Prescription.addLine(this.value);"/>
	  <script type="text/javascript">
		  MedSelector.init = function(onglet){
		    this.sForm = "searchProd";
		    this.sView = "produit";
		    this.sCode = "code_cip";
		    this.sSearch = document.searchProd.produit.value;
		    this.sOnglet = onglet;
		    this.selfClose = false;
		    this.pop();
		  }
	</script>
  </form>
  <br />
  <div id="add_line_comment_med" style="display: none">
   <button class="cancel notext" type="button" onclick="$('add_line_comment_med').hide();">Cacher</button>
   <form name="addLineCommentMed" method="post" action="">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_comment_id" value="" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
    
      <input type="hidden" name="chapitre" value="medicament" />
      <input name="commentaire" type="text" size="98" />
      <button class="submit notext" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function(){ Prescription.reload('{{$prescription->_id}}',null,'medicament')} } )">Ajouter</button>
    </form>
 </div> 

{{if $prescription->_ref_lines_med_comments.med || $prescription->_ref_lines_med_comments.comment}}
<table class="tbl">
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    
  <tbody id="line_medicament_{{$curr_line->_id}}" class="hoverable">
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
    </td>
  </tr>
  </tbody>
   
  {{/foreach}}
    <!-- Parcours des commentaires --> 
 {{foreach from=$prescription->_ref_lines_med_comments.comment item=_line_comment}}
   <tbody class="hoverable">
    <tr>
      <td colspan="2">
        <form name="delLineCommentMed-{{$_line_comment->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
          <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'medicament') } } );">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
      </td>
      <td colspan="2">
        {{$_line_comment->commentaire}}
      </td>
      <td>
	      <form action="?" method="post" name="editLineCommentALD-{{$_line_comment->_id}}">
	        <input type="hidden" name="m" value="dPprescription" />
	        <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	        <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}"/>
	        <input type="hidden" name="del" value="0" />
	        {{mb_field object=$_line_comment field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
	        {{mb_label object=$_line_comment field="ald" typeEnum="checkbox"}}
	      </form>
      </td>
    </tr>
  </tbody>
  {{/foreach}}
 </table> 
{{else}}
  <div class="big-info"> 
     Il n'y a aucun médicament dans cette prescription.
  </div>
{{/if}}



<script type="text/javascript">

/*
{{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
  var oForm = document.forms["editLine-{{$curr_line->_id}}"];
  prepareForm(oForm);  
{{/foreach}}
*/
prepareForms();


Main.add( function(){
  {{foreach from=$prescription->_ref_lines_med_comments.med item=curr_line}}
    regFieldCalendar('editDates-{{$curr_line->_id}}', "debut", false);
    regFieldCalendar('editDates-{{$curr_line->_id}}', "_fin", false);
  
     initPoso(document.forms["editLine-{{$curr_line->_id}}"],{{$curr_line->_id}});
     calculFin(document.forms["editDates-{{$curr_line->_id}}"], {{$curr_line->_id}});
  {{/foreach}}
} );

// UpdateFields de l'autocomplete de medicaments
updateFieldsMedicament = function(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  Prescription.addLine(dn[0].firstChild.nodeValue);
  $('searchProd_produit').value = "";
}

// Preparation des formulaire
prepareForm(document.addLine);
prepareForm(document.searchProd);

// Autocomplete des medicaments
urlAuto = new Url();
urlAuto.setModuleAction("dPmedicament", "httpreq_do_medicament_autocomplete");
urlAuto.addParam("produit_max", 10);
urlAuto.autoComplete("searchProd_produit", "produit_auto_complete", {
  minChars: 3,
  updateElement: updateFieldsMedicament
} );


</script>