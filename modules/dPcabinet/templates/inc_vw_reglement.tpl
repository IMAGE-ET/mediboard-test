{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<script type="text/javascript">

Object.extend(Intermax.ResultHandler, {
  "Lire Vitale": function() {
    var oVitale = Intermax.oContent.VITALE;
    
    var msg = {{$patient->_id_vitale|json}} ?
    	"Vous êtes sur le point de mettre à jour le patient" :
    	"Vous êtes sur le point d'associer le patient";
    msg += printf("\n\t%s %s (%s)",
    	'{{$patient->nom|smarty:nodefaults|JSAttribute}}', 
    	'{{$patient->prenom|smarty:nodefaults|JSAttribute}}', 
    	'{{mb_value object=$patient field=naissance}}');
    msg += "\nAvec le bénéficiaire Vitale";
    msg += printf("\n\t%s %s (%s)", 
    	oVitale.VIT_NOM, 
    	oVitale.VIT_PRENOM, 
    	oVitale.VIT_DATE_NAISSANCE);
    msg += "\n\nVoulez-vous continuer ?";
        
    if (confirm(msg)) {
      Reglement.submit(document.BindVitale);
    }
  },
  
  "Lire CPS": function() {
    var oCPS = Intermax.oContent.CPS;
    
    var msg = {{$praticien->_id_cps|json}} ?
    	"Vous êtes sur le point de mettre à jour le praticien" :
    	"Vous êtes sur le point d'associer le pratcien";
    msg += printf("\n\t%s %s (%s)", 
    	'{{$praticien->_user_first_name|smarty:nodefaults|JSAttribute}}', 
    	'{{$praticien->_user_last_name|smarty:nodefaults|JSAttribute}}', 
    	'{{mb_value object=$praticien field=adeli}}');
    msg += "\nAvec la Carte Professionnelle de Santé de";
    msg += printf("\n\t%s %s (%s)", 
    	oCPS.CPS_PRENOM,
    	oCPS.CPS_NOM,
    	oCPS.CPS_ADELI_NUMERO_CPS);
    msg += "\n\nVoulez-vous continuer ?";

    if (confirm(msg)) {
      Reglement.submit(document.BindCPS);
    }
  },

  "Formater FSE": function() {
    Reglement.submit(document.BindFSE);
  },

  "Annuler FSE": function() {
    Reglement.reload();
  }  
} );

Intermax.ResultHandler["Consulter Vitale"] = Intermax.ResultHandler["Lire Vitale"];
Intermax.ResultHandler["Consulter FSE"] = Intermax.ResultHandler["Formater FSE"];

// Use single quotes or fails ?!!
Intermax.Triggers['Formater FSE'].aActes = {{$consult->_fse_intermax|@json}};


cancelTarif = function(action) {
  var oForm = document.tarifFrm;
  
  if(action == "delActes"){
    $V(oForm._delete_actes, 1);
    $V(oForm.tarif, "");
  }
  
  {{if $app->user_prefs.autoCloseConsult}}
  $V(oForm.chrono, "48");
  {{/if}}
  
  $V(oForm.valide, 0);
  $V(oForm._somme, 0);
  
  // On met à 0 les valeurs de tiers 
  $V(oForm.tiers_date_reglement, "");
  $V(oForm.patient_date_reglement, "");
  
  Reglement.submit(oForm);
}

validTarif = function(){
  var oForm = document.tarifFrm;
  
  $V(oForm.du_tiers,  $V(oForm._somme) - $V(oForm.du_patient));
  
  if($V(oForm.tarif) == ""){
    $V(oForm.tarif, "manuel");
    if($V(oForm._tokens_ccam)){
      oForm.tarif.value += " / "+$V(oForm._tokens_ccam);
    }
    if($V(oForm._tokens_ngap)){
      oForm.tarif.value += " / "+$V(oForm._tokens_ngap);
    }
  }
  Reglement.submit(oForm);
}

modifTotal = function(){
  var oForm = document.tarifFrm;
  var secteur1 = oForm.secteur1.value;
  var secteur2 = oForm.secteur2.value;

  $V(oForm._somme, Math.round(100*(parseFloat(secteur1) + parseFloat(secteur2))) / 100);
  $V(oForm.du_patient, $V(oForm._somme)); 
}

modifSecteur2 = function(){
  var oForm = document.tarifFrm;
  var secteur1 = oForm.secteur1.value;
  var somme = oForm._somme.value;
  
  $V(oForm.du_patient, somme);
  $V(oForm.secteur2, Math.round(100*(parseFloat(somme) - parseFloat(secteur1))) / 100);
}

putTiers = function() {
  var oForm = document.tarifFrm;
  oForm.du_patient.value = 0;
}

Main.add( function(){
  // Mise a jour de du_patient
  var oForm = document.tarifFrm;
  if(oForm && oForm.du_patient.value == "0"){
    $V(oForm.du_patient, $V(oForm._somme)); 
  }
} );


</script>

<table class="form">
  {{if !$noReglement}}
  {{mb_ternary var=gestionFSE test=$consult->sejour_id value=0 other=$app->user_prefs.GestionFSE}}
	<tr>
	  {{if $gestionFSE}}
    <th class="category">{{tr}}CLmFSE{{/tr}}</th>
	  {{/if}}
    
    <th {{if !$gestionFSE}}colspan="2"{{/if}} class="category">
      {{if $consult->valide}}
      <!-- Creation d'un nouveau tarif avec les actes NGAP de la consultation courante -->
      <form name="creerTarif" action="?m={{$m}}&amp;tab=vw_compta" method="post" style="float: right;">
        <input type="hidden" name="dosql" value="do_tarif_aed" />
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="_tab" value="vw_edit_tarifs" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_bind_consult" value="1" />
        <input type="hidden" name="_consult_id" value="{{$consult->_id}}" />
        <button class="submit" type="submit">Nouveau tarif</button>
      </form>
      {{/if}}
      Règlement
    </th>
  </tr>
  
	<tr>	
	
	  {{if $gestionFSE}}
    <!-- Feuille de soins -->
    <td class="text">
      <table class="form">
        <tr>
          <td class="text">
			      {{if !$patient->_id_vitale || !$praticien->_id_cps}}
			      <div class="warning">
			        Professionnel de Santé ou Bénéficiaire Vitale non identifié
			        <br/>
			        Merci d'associer la CPS et la carte Vitale pour permettre le formatage d'une FSE. 
			      </div>
			      {{else}}
			        
			      <form name="BindFSE" action="?m={{$m}}" method="post">
			
			      <input type="hidden" name="m" value="dPcabinet" />
			      <input type="hidden" name="dosql" value="do_consultation_aed" />
			      <input type="hidden" name="_delete_actes" value="1" />
			      <input type="hidden" name="_bind_fse" value="1" />
			      {{mb_field object=$consult field="consultation_id" hidden="1"}}
		      
			      </form>
			      {{/if}}
				  </td>
				</tr>
				
				<!-- Les FSE déjà associées -->
        {{foreach from=$consult->_ext_fses key=_id_fse item=_ext_fse}}
				<tr>
				  <td>
				  	<span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: 'CLmFSE', object_id: '{{$_id_fse}}' } })">
				  	  {{$_ext_fse->_view}}
				  	</span>
		      </td>
		      {{if $_ext_fse->_annulee}}
	        <td class="cancelled">
	          {{mb_value object=$_ext_fse field=S_FSE_ETAT}}
	        </td>
	        {{/if}}
		    </tr>
        {{if !$_ext_fse->_annulee}}
		    <tr>
		      <td class="button" colspan="2">
			      <button class="search" type="button" onclick="Intermax.Triggers['Consulter FSE']('{{$_id_fse}}');">
			        Consulter 
			      </button>
			      <button class="print" type="button" onclick="Intermax.Triggers['Editer FSE']('{{$_id_fse}}');">
			        Imprimer
			      </button>
			      <button class="cancel" type="button" onclick="Intermax.Triggers['Annuler FSE']('{{$_id_fse}}');">
			        Annuler
			      </button>
		      </td>
		    </tr>
	      {{/if}}
        {{foreachelse}}
				<tr>
				  <td>
				    <em>Aucune FSE associée</em>
		      </td>
		    </tr>
        {{/foreach}}

        {{if $patient->_id_vitale && $praticien->_id_cps}}
        <tr>
          <td class="button" colspan="2">
            {{if !$consult->_current_fse}}
			      <button class="new" type="button" onclick="Intermax.Triggers['Formater FSE']('{{$praticien->_id_cps}}', '{{$patient->_id_vitale}}');">
			        Formater FSE
			      </button>
			      {{/if}}
			      <button class="change intermax-result" type="button" onclick="Intermax.result(['Formater FSE', 'Consulter FSE', 'Annuler FSE']);">
			        Mettre à jour FSE
			      </button>
          </td>
        </tr>
        {{/if}}

      </table>
    </td>
	  {{/if}}

    <!-- Règlements -->  
    <td {{if !$gestionFSE}}colspan="2"{{/if}}>
    
    <form name="accidentTravail" action="" method="post">
      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="consultation_id" value="{{$consult->_id}}" />
      <table class="form">
        <tr>
          <th>
           {{mb_label object=$consult field="accident_travail"}}
         </th>
         <td class="date">
           {{mb_field object=$consult field="accident_travail" form="accidentTravail" onchange="submitFormAjax(this.form,'systemMsg');"}}
         </td>
         <td>
           {{if $patient->cmu}}
           <strong>Bénéficiaire d'une CMU</strong>
           {{/if}}
         </td>
       </tr>
      </table>  
    </form>
    <script type="text/javascript">
      Main.add( function(){
        prepareForm(document.accidentTravail);
        regFieldCalendar('accidentTravail', "accident_travail");
      });
    </script>
      
      
      <!-- Formulaire de selection de tarif -->
      <form name="selectionTarif" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

	      <input type="hidden" name="m" value="dPcabinet" />
	      <input type="hidden" name="del" value="0" />
	      <input type="hidden" name="dosql" value="do_consultation_aed" />
        <input type="hidden" name="_delete_actes" value="1" />
	      <input type="hidden" name="_bind_tarif" value="1" />
	      {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
	     
	      <table class="form">
	        {{if !$consult->tarif}}
	        <tr>
	          <th><label for="choix" title="Type de cotation pour la consultation. Obligatoire.">Cotation</label></th>
	          <td>
	            <select name="_tarif_id"  class="notNull str" onchange="submitFormAjax(this.form, 'systemMsg', { onComplete : Reglement.reload } );">
	              <option value="" selected="selected">&mdash; Choisir la cotation</option>
	              {{if $tarifsChir|@count}}
	              <optgroup label="Tarifs praticien">
	              {{foreach from=$tarifsChir item=curr_tarif}}
	                <option value="{{$curr_tarif->_id}}">{{$curr_tarif->_view}}</option>
	              {{/foreach}}
	              </optgroup>
	              {{/if}}
	              {{if $tarifsCab|@count}}
	              <optgroup label="Tarifs cabinet">
	              {{foreach from=$tarifsCab item=curr_tarif}}
	                <option value="{{$curr_tarif->_id}}">{{$curr_tarif->_view}}</option>
	              {{/foreach}}
	              </optgroup>
	              {{/if}}
	            </select>
	          </td>
	        </tr>
	        {{else}}
	        <tr>
	          <th>{{mb_label object=$consult field=tarif}}</th>
	          <td>{{mb_value object=$consult field=tarif}}</td>    
	        </tr>
	        {{/if}}
	      </table>
      </form>
      <!-- Fin formulaire de selection du tarif -->
      
      <hr />
      
      <!-- Formulaire de tarification -->
      <form name="tarifFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

      <input type="hidden" name="m" value="dPcabinet" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_consultation_aed" />
     {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
     {{mb_field object=$consult field="sejour_id" hidden=1 prop=""}}

      <table width="100%">
        <!-- A regler -->
        <tr>
          <th>{{mb_label object=$consult field="_somme"}}</th>
          <td>
            {{mb_field object=$consult field="tarif" hidden=1 prop=""}}
            <input type="hidden" name="patient_date_reglement" value="" />
            {{if $consult->valide}}
  	          {{mb_value object=$consult field="secteur1"}} (S1) +
  	          {{mb_value object=$consult field="secteur2"}} (S2) =
   			      {{mb_value object=$consult field="_somme" value=$consult->secteur1+$consult->secteur2 onchange="modifSecteur2()"}}
            {{else}}
              {{mb_label object=$consult field="secteur1"}}
  	          {{mb_field object=$consult field="secteur1" onchange="modifTotal()"}} +
  	          {{mb_label object=$consult field="secteur2"}}
  	          {{mb_field object=$consult field="secteur2" onchange="modifTotal()"}} =
 			      <input type="text" size="6" name="_somme" class="notNull currency" value="{{$consult->secteur1+$consult->secteur2}}" onchange="modifSecteur2()" /> &euro;
            {{/if}}
            {{if $consult->patient_date_reglement}}
              {{mb_field object=$consult field="du_patient" hidden=1 prop=""}}
              {{mb_field object=$consult field="du_tiers" hidden=1 prop=""}}
            
              {{mb_field object=$consult field="patient_date_reglement" hidden=1 prop=""}}
            {{/if}}
          </td>
        </tr>
        <tr>
          <th>Codes CCAM</th>
          <td>{{mb_field object=$consult field="_tokens_ccam" readonly="readonly" hidden=1 prop=""}}
            {{foreach from=$consult->_ref_actes_ccam item="acte_ccam"}}
              {{$acte_ccam->code_acte}}
            {{/foreach}}
          </td>
        </tr>
        <tr>
          <th>Codes NGAP</th>
          <td>{{mb_field object=$consult field="_tokens_ngap" readonly="readonly" hidden=1 prop=""}}
          {{foreach from=$consult->_ref_actes_ngap item=acte_ngap}}
            {{$acte_ngap->quantite}}-{{$acte_ngap->code}}-{{$acte_ngap->coefficient}}   
          {{/foreach}}
          </td>
        </tr>
        <!-- Suppression des actes associées a la consultation
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="tarif" value="{{$consult->tarif}}" />
            <button class="cancel" type="button" onclick="cancelTarif()">Annuler le réglement</button>
          </td>
        </tr>
        -->


        {{if $consult->tarif && $consult->patient_date_reglement == "" && $consult->valide == "1"}}
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="valide" value="1" />
            <input type="hidden" name="secteur1" value="{{$consult->secteur1}}" />
            <input type="hidden" name="secteur2" value="{{$consult->secteur2}}" />
            <input type="hidden" name="du_patient" value="{{$consult->du_patient}}" />
            <input type="hidden" name="du_tiers" value="{{$consult->du_tiers}}" />
            
            {{if $app->user_prefs.autoCloseConsult}}
            <input type="hidden" name="chrono" value="{{$consult->chrono}}" />
            {{/if}}
            
            {{if !$consult->_current_fse && $consult->_ref_reglements|@count == 0}}
            <button class="cancel" type="button" onclick="this.form.du_tiers.value = 0; this.form.du_patient.value = 0; cancelTarif()">Annuler la validation</button>
            {{/if}}
          </td>
        </tr>
        {{elseif !$consult->patient_date_reglement}}
          {{if !$consult->sejour_id}}
          <tr>
            <th>{{mb_label object=$consult field="du_patient"}}</th>
            <td>
              {{mb_field object=$consult field="du_patient"}}
              {{mb_field object=$consult field="du_tiers" hidden="1"}}
              <button type="button" class="tick" onclick="putTiers();">Tiers-payant total</button>   
            </td>
          </tr>
          {{/if}}
          <tr>
            <td colspan="2" class="button">
              <input type="hidden" name="_delete_actes" value="0" />
              <input type="hidden" name="valide" value="1" />
              
              {{if $app->user_prefs.autoCloseConsult}}
              <input type="hidden" name="chrono" value="64" />
              {{/if}}
              
              <button class="submit" type="button" onclick="validTarif();">Valider la cotation</button>
              <button class="cancel" type="button" onclick="cancelTarif('delActes')">Annuler la cotation</button>
            </td>
          </tr>
        {{/if}}
        {{/if}}
      </table>
      </form>
      <!-- Fin du formulaire de tarification -->
      
      
      <!-- Debut du formulaire de rajout de reglements -->
      {{if $consult->tarif && $consult->valide == "1"}}
        {{if $consult->sejour_id}}
         <div style="text-align: center; font-weight: bold;">
           ATU : Règlement à effectuer au bureau des sorties
         </div>
        {{else}}

         {{if $consult->du_patient}}
         
          <!-- Formulaire de suppression d'un reglement (car pas possible de les imbriquer) -->
          <form name="reglement-delete" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="dosql" value="do_reglement_aed" />
            <input type="hidden" name="reglement_id" value="" />
          </form>
         
          <script type="text/javascript">Main.add( function() { prepareForm(document.forms["reglement-add"]); } );</script>
          <form name="reglement-add" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete : Reglement.reload } );">
           <input type="hidden" name="m" value="dPcabinet" />
           <input type="hidden" name="del" value="0" />
           <input type="hidden" name="dosql" value="do_reglement_aed" />
           <input type="hidden" name="date" value="now" />
           <input type="hidden" name="emetteur" value="patient" />
          {{mb_field object=$consult field="consultation_id" hidden=1 prop=""}}
          
           <table style="width: 100%;">
              <tr>
                <th class="category">{{mb_label object=$reglement field=mode}}</th>
                <th class="category">{{mb_label object=$reglement field=montant}}</th>
                <th class="category">{{mb_label object=$reglement field=banque_id}}</th>
                <th class="category">{{mb_label object=$reglement field=date}}</th>
                <th class="category"></th>
              </tr>
              
              <!--  Liste des reglements deja effectués -->
              {{foreach from=$consult->_ref_reglements item=curr_reglement}}
              <tr>
                <td>{{mb_value object=$curr_reglement field=mode}}</td>
                <td>{{mb_value object=$curr_reglement field=montant}}</td>
                <td>
                {{if $curr_reglement->_ref_banque}}
                  {{mb_value object=$curr_reglement->_ref_banque field=_view}}
                {{/if}}
                </td>
                <td>{{mb_value object=$curr_reglement field=date}}</td>
                <td>
                  <button class="remove notext" onclick="return Reglement.cancel({{$curr_reglement->_id}});">-</button>
                </td>
              </tr>
              {{/foreach}}
             
              {{if $reglement->montant > 0}}
              <tr>
                <td>{{mb_field object=$reglement field="mode" defaultOption="&mdash; Mode de paiement &mdash;"}}</td>
                <td>{{mb_field object=$reglement field="montant"}}</td>
                <td colspan="2">
                  <select name="banque_id">
                     <option value="">&mdash; {{tr}}CReglement-banque_id{{/tr}} &mdash;</option> 
                     {{foreach from=$banques item=banque}}
                       <option value="{{$banque->_id}}">{{$banque->_view}}</option>
                     {{/foreach}}
                  </select>
                </td>
                <td><button class="add notext" type="submit" onclick="return this.form.onsubmit();">+</button></td>
              </tr>
              {{/if}}
              <tr>
                <td colspan="4" style="text-align: center;">
                	{{mb_value object=$consult field=_reglements_total_patient}} réglés, 
                	<strong>{{mb_value object=$consult field=_du_patient_restant}} restant</strong>
                </td>
              </tr>
              <tr>
                <td colspan="4" style="text-align: center;">
                	<strong>
	                  {{if $consult->patient_date_reglement}}
	                  {{mb_label object=$consult field=patient_date_reglement}}
	                  le 
	                  {{mb_value object=$consult field=patient_date_reglement}}
	                  {{/if}}
                  </strong>
                </td>
              </tr>
            </table>
          </form>
        
        {{/if}}
      {{/if}}
      <!-- Fin du formulaire de rajout de reglement -->
      
    </td>
  </tr>
  

  {{if $gestionFSE}}
  <!-- Patient Vitale et Professionnel de Santé -->
  <tr>
    <th class="category">Professionnel de santé</th>
    <th class="category">Patient Vitale</th>
  </tr>
  
  <tr>

    <!-- Professionnel de santé -->
    <td class="text">
      <form name="BindCPS" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="mediusers" />
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="_bind_cps" value="1" />
      {{mb_field object=$praticien field="user_id" hidden="1"}}
      
      </form>
    
      {{if !$praticien->_id_cps}}
      <div class="warning">
        Praticien non associé à une CPS. <br/>
        Merci d'effectuer une lecture de la CPS pour permettre le formatage d'une FSE. 
      </div>
      {{else}}
      <div class="message">
        Praticien correctement associé à une CPS. <br/>
        Formatage des FSE disponible pour ce praticien.
      </div>
      {{/if}}
    </td>

    <!-- Patient Vitale -->
    <td class="text">
      <form name="BindVitale" action="?m={{$m}}" method="post">

      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="dosql" value="do_patients_aed" />
      <input type="hidden" name="_bind_vitale" value="1" />
      {{mb_field object=$patient field="patient_id" hidden="1"}}
      
      </form>
            
      {{if !$patient->_id_vitale}}
      <div class="warning">
        Patient non associé à un bénéficiaire Vitale. <br/>
        Merci d'éffectuer une lecture de la carte pour permettre le formatage d'une FSE. 
      </div>
      {{else}}
      <div class="message">
        Patient correctement associé à un bénéficiaire Vitale. <br/>
        Formatage des FSE disponible pour ce patient.
      </div>
      {{/if}}
    </td>
    
  </tr> 
  
  <tr>

    <!-- Professionnel de santé -->
    <td class="button">
      {{if !$praticien->_id_cps}}
      <button class="search" type="button" onclick="Intermax.trigger('Lire CPS');">
        Lire CPS
      </button>
      <button class="change intermax-result" type="button" onclick="Intermax.result('Lire CPS');">
        Associer CPS
      </button>
      {{/if}}
    </td>

    <!-- Patient Vitale -->
    <td class="button">
      {{if $patient->_id_vitale}}
      <button class="search" type="button" onclick="Intermax.Triggers['Consulter Vitale']({{$patient->_id_vitale}});">
        Consulter Vitale
      </button>
      {{else}}
      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');">
        Lire Vitale
      </button>
      <button class="change intermax-result" type="button" onclick="Intermax.result();">
        Associer Vitale
      </button>
      {{/if}}
    </td>

  </tr>
  {{/if}}
  {{/if}}
 
</table>
