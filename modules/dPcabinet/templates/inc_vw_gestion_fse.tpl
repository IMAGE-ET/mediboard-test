{{assign var=patient value=$consult->_ref_patient}}
{{assign var=praticien value=$consult->_ref_chir}}

<script type="text/javascript">
Object.extend(Intermax.ResultHandler, {
  "Lire Vitale": function() {
    var oVitale = Intermax.oContent.VITALE;
    
    var sVitaleView = printf("\n\t%s %s (%s)", 
      oVitale.VIT_NOM, 
      oVitale.VIT_PRENOM, 
      oVitale.VIT_DATE_NAISSANCE);
      
    var oPatient = {
      nom       : '{{$patient->nom|smarty:nodefaults|JSAttribute}}',
      prenom    : '{{$patient->prenom|smarty:nodefaults|JSAttribute}}',
      naissance : '{{mb_value object=$patient field=naissance}}'
    }
    
    var sPatientView = printf("\n\t%s %s (%s)",
      oPatient.nom, 
      oPatient.prenom,
      oPatient.naissance);
      
      
    var msg = {{$patient->_id_vitale|json}} ?
      "Vous êtes sur le point de mettre à jour le patient" :
      "Vous êtes sur le point d'associer le patient";
    msg += sPatientView;
    msg += "\nAvec le bénéficiaire Vitale";
    msg += sVitaleView;
    msg += "\n\nNOUVEAU : La fiche patient de Mediboard sera mise à jour avec les informations de la carte Vitale.";
    msg += "\n\nVoulez-vous continuer ?";
        
    if (!confirm(msg)) {
    	return;
    }

    if (oVitale.VIT_DATE_NAISSANCE != oPatient.naissance && oPatient.naissance != '') {
    	msg = "ATTENTION : Les dates de naissance ne correspondent pas ! ";
    	msg += "\n\nEtes vous certain de vouloir remplacer ";
	    msg += sPatientView;
	    msg += "\nPar ";
	    msg += sVitaleView;
	    
      if (!confirm(msg)) {
        return;
			}
    }

    Reglement.submit(document.BindVitale, false);
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
      Reglement.submit(document.BindCPS, false);
    }
  },

  "Formater FSE": function() {
    Reglement.submit(document.BindFSE, true);
  },

  "Annuler FSE": function() {
    Reglement.reload(true);
  }  
} );

Intermax.ResultHandler["Consulter Vitale"] = Intermax.ResultHandler["Lire Vitale"];
Intermax.ResultHandler["Consulter FSE"] = Intermax.ResultHandler["Formater FSE"];

// Use single quotes or fails ?!!
Intermax.Triggers['Formater FSE'].aActes = {{$consult->_fse_intermax|@json}};
</script>

<table class="form">
  <tr>
    <th class="category" colspan="2">{{tr}}CLmFSE{{/tr}}</th>
  </tr>
  <tr>
    <td colspan="2">
    <!-- Feuille de soins -->
      <table class="form">
        <tr>
          <td class="text">
            {{if !$patient->_id_vitale || !$praticien->_id_cps}}
              <div class="small-warning">
                Merci d'associer <strong>la CPS et la carte Vitale</strong> pour formater une FSE. 
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
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_ext_fse->_guid}}')">
              {{$_ext_fse}}
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
              <button class="search" type="button singleclick" onclick="Intermax.Triggers['Consulter FSE']('{{$_id_fse}}');">
                Consulter 
              </button>
              <button class="print" type="button singleclick" onclick="Intermax.Triggers['Editer FSE']('{{$_id_fse}}');">
                Imprimer
              </button>
              <button class="cancel" type="button singleclick" onclick="Intermax.Triggers['Annuler FSE']('{{$_id_fse}}');">
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
            <button class="new" type="button singleclick" onclick="Intermax.Triggers['Formater FSE']('{{$praticien->_id_cps}}', '{{$patient->_id_vitale}}');">
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
  </tr>

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
        <div class="small-info">
          Praticien non associé à une CPS. <br/>
          Merci d'effectuer une lecture de la CPS pour permettre le formatage d'une FSE. 
        </div>
      {{else}}
        <div class="small-success">
          Praticien correctement associé à une CPS.
        </div>
      {{/if}}
    </td>

    <!-- Patient Vitale -->
    <td class="text">
      <form name="BindVitale" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpatients" />
        <input type="hidden" name="dosql" value="do_patients_aed" />
        {{mb_key object=$patient}}
        <input type="hidden" name="_bind_vitale" value="1" />
        <input type="hidden" name="_update_vitale" value="1" />
      </form>
            
      {{if !$patient->_id_vitale}}
        <div class="small-info">
          Patient non associé à un bénéficiaire Vitale. <br/>
          Merci d'effectuer une lecture de la carte pour permettre le formatage d'une FSE. 
        </div>
      {{else}}
        <div class="small-success">
          Patient correctement associé à un bénéficiaire Vitale.
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
        <button class="change notext intermax-result" type="button" onclick="Intermax.result('Lire CPS');">
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
        <button class="change notext intermax-result" type="button" onclick="Intermax.result();">
          Associer Vitale
        </button>
      {{/if}}
    </td>
  </tr>
</table>