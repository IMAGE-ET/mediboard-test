<script language="Javascript" type="text/javascript">
function annuleFiche(oForm,annulation){
  oForm.annulee.value = annulation;
  oForm._validation.value = 1;
  oForm.submit();
}

function refusMesures(oForm){
  if(oForm.remarques.value == ""){
    alert("Veuillez saisir vos remarques dans la zone 'Remarques'.");
    oForm.remarques.focus();
  }else{
    oForm.service_date_validation.value = "";
    oForm._validation.value= 1;
    oForm.submit();
  }
}

function saveVerifControle(oForm){
  oForm._validation.value= 1;
  oForm.submit();  
}

function printIncident(ficheId){
  var url = new Url;
  url.setModuleAction("dPqualite", "print_fiche"); 
  url.addParam("fiche_ei_id", ficheId);
  url.popup(700, 500, "printFicheEi");
  return;
}

{{if  $canAdmin && $fiche->qualite_date_validation && (!$fiche->qualite_date_verification || !$fiche->qualite_date_controle)}}
function pageMain() {
  {{if !$fiche->qualite_date_verification}}
  regFieldCalendar("ProcEditFrm", "qualite_date_verification");
  {{else}}
  regFieldCalendar("ProcEditFrm", "qualite_date_controle");
  {{/if}}
}
{{/if}}
</script>
<table class="main">
  <tr>
    <td class="halfPane">
      {{if $listFichesAttente|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            Fiche d'EI en Attente
          </th>
        </tr>
        <tr>
          <th class="category">Date de l'événement</th>
          <th class="category">Auteur de la fiche</th>
        </tr>
        {{foreach from=$listFichesAttente item=currFiche}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->date_incident|date_format:"%A %d %B %Y à %Hh%M"}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_ref_user->_view}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table><br /><br />
      {{/if}}
        
      <table class="form">
        <tr>
          <th class="category" colspan="4">Fiches d'EI en cours de traitement</th>
        </tr>
        <tr>
          <th class="category">Date de l'événement</th>
          <th class="category">Auteur</th>
          <th class="category">Urgence</th>
          <th class="category">Etat</th>
        </tr>        
        {{foreach from=$listFichesEnCours item=currFiche}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->date_incident|date_format:"%d %b %Y à %Hh%M"}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_ref_user->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->degre_urgence}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_etat_actuel}}
            </a>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="4">
            Aucune Fiche traitée actuellement
          </td>
        </tr>
        {{/foreach}}
      </table>
      
      {{if $listFichesTermine|@count}}
        <br />
        {{if !$ficheTermineVisible}}
        <a class="buttonsearch" href="index.php?m={{$m}}&amp;ficheTermineVisible=1">
        Afficher les Fiches Terminées
        </a><br />
        {{else}}
        <a class="buttoncancel" href="index.php?m={{$m}}&amp;ficheTermineVisible=0">
        Cacher les Fiches Terminées
        </a>
        <table class="form">
          <tr>
            <th class="category" colspan="2">Fiches d'EI Traitées</th>
          </tr>
          <tr>
            <th class="category">Fiches</th>
            <th class="category">Auteur de la fiche</th>
          </tr>
          {{foreach from=$listFichesTermine item=currFiche}}
          <tr>
            <td class="text">
              <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
                {{$currFiche->_view}}
              </a>
            </td>
            <td class="text">
              <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
                {{$currFiche->_ref_user->_view}}
              </a>
            </td>
          </tr>
          {{/foreach}}
          </table>
          {{/if}}
      {{/if}}
      
      {{if $listFichesAnnulees|@count}}
        <br />
        {{if !$ficheAnnuleVisible}}
        <a class="buttonsearch" href="index.php?m={{$m}}&amp;ficheAnnuleVisible=1">
        Afficher les Fiches Annulées
        </a>
        {{else}}
        <a class="buttoncancel" href="index.php?m={{$m}}&amp;ficheAnnuleVisible=0">
        Cacher les Fiches Annulées
        </a>
        <table class="form">
          <tr>
            <th class="category" colspan="2">Fiches d'EI Annulées</th>
          </tr>
          <tr>
            <th class="category">Date de l'événement</th>
            <th class="category">Auteur de la fiche</th>
          </tr>
          {{foreach from=$listFichesAnnulees item=currFiche}}
          <tr>
            <td class="text">
              <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
                {{$currFiche->date_incident|date_format:"%A %d %B %Y à %Hh%M"}}
              </a>
            </td>
            <td class="text">
              <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
                {{$currFiche->_ref_user->_view}}
              </a>
            </td>
          </tr>
          {{/foreach}}
          </table>
          {{/if}}
      {{/if}}
    </td>
    <td class="halfPane">
      {{if $fiche->fiche_ei_id}}
      
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="annulee" value="{{$fiche->annulee}}" />
      <input type="hidden" name="fiche_ei_id" value="{{$fiche->fiche_ei_id}}" />
      <input type="hidden" name="_validation" value="0" />
      <input type="hidden" name="service_date_validation" value="{{$fiche->service_date_validation}}" />
            
      <table class="form">
        {{include file="inc_incident_infos.tpl"}}
        
      {{if $canAdmin && !$fiche->date_validation &&!$fiche->annulee}}
        <tr>
          <th><label for="degre_urgence" title="Veuillez sélectionner le degré d'urgence">Degré d'Urgence</label></th>
          <td>
            <select name="degre_urgence" title="{{$fiche->_props.degre_urgence}}|notNull">
            <option value="">&mdash; Veuillez Choisir</option>
            {{html_options options=$fiche->_enumsTrans.degre_urgence}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="service_valid_user_id" title="Veuillez sélectionner le chef de service à qui transmettre la fiche">Chef de Service à qui transmettre la fiche</label></th>
          <td>
            <select name="service_valid_user_id" title="{{$fiche->_props.service_valid_user_id}}|notNull">
            <option value="">&mdash; Veuillez Choisir &mdash;</option>
            {{foreach from=$listUsersEdit item=currUser}}
            <option value="{{$currUser->user_id}}">{{$currUser->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="valid_user_id" value="{{$user_id}}" />
            <button class="edit" type="button" onclick="window.location.href='index.php?m={{$m}}&amp;tab=vw_incident&amp;fiche_ei_id={{$fiche->fiche_ei_id}}';">
              Editer la Fiche
            </button>
            <button class="modify" type="submit">
              Transmettre
            </button>
            <button class="cancel" type="button" onclick="annuleFiche(this.form,1);" title="Annuler la Fiche d'EI">
              Annuler
            </button>
          </td>
        </tr>
      {{/if}}
      
      {{if $fiche->service_valid_user_id && $fiche->service_valid_user_id==$user && !$fiche->service_date_validation}}
        <tr>
          <th colspan="2" class="category">
            Validation du Chef de Service
          </th>
        </tr>
        {{if $fiche->remarques}}
        <tr>
          <th><strong>Mesures refusé par</strong></th>
          <td class="text">
            {{$fiche->_ref_qualite_valid->_view}}
          </td>
        </tr>
        <tr>
          <th><strong>Remarques</strong></th>
          <td class="text" style="color:#f00;">
            <strong>{{$fiche->remarques|nl2br}}</strong>
          </td>
        </tr>
        {{/if}}
        <tr>
          <th>
            <label for="service_actions" title="Veuillez décrire les actions mises en place">Actions mises en Place</label>
          </th>
          <td>
            <textarea name="service_actions" title="{{$fiche->_props.service_actions}}|notNull">{{$fiche->service_actions}}</textarea>
          </td>
        </tr>
        <tr>
          <th>
            <label for="service_descr_consequences" title="Veuillez décrire les conséquences">Description des conséquences</label>
          </th>
          <td>
            <textarea name="service_descr_consequences" title="{{$fiche->_props.service_descr_consequences}}|notNull">{{$fiche->service_descr_consequences}}</textarea>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="remarques" value="" />
            <button class="modify" type="submit">
              Transmettre
            </button>
          </td>
        </tr>
      {{/if}}
      {{if $canAdmin && $fiche->service_date_validation}}
        {{if !$fiche->qualite_date_validation}}
        <tr>
          <td colspan="2" class="button">
            <input type="hidden" name="qualite_user_id" value="{{$user_id}}" />
            <button class="modify" type="submit">
              Valider ces mesures
            </button>
            <button class="cancel" type="button" onclick="refusMesures(this.form);">
              Refuser ces mesures
            </button>
          </td>
        </tr>
        <tr>
          <th>
            <label for="remarques" title="Veuillez saisir vos remarques en cas de refus de ces mesures">
              Remarques en cas de refus
            </label>
          </th>
          <td>
            <textarea name="remarques" title="{{$fiche->_props.remarques}}"></textarea>
          </td>
        </tr>
        {{else}}
        {{if !$fiche->qualite_date_verification}}
        <tr>
          <th><label for="qualite_date_verification" title="Veuillez saisir la date de vérification">Date de Vérification</label></th>
          <td class="date">
            <div id="ProcEditFrm_qualite_date_verification_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="qualite_date_verification" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <img id="ProcEditFrm_qualite_date_verification_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de vérification" />
          </td>
        </tr>
        {{elseif !$fiche->qualite_date_controle}}
        <tr>
          <th><label for="qualite_date_controle" title="Veuillez saisir la date de contrôle">Date de Contrôle</label></th>
          <td class="date">
            <div id="ProcEditFrm_qualite_date_controle_da">{{$today|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="qualite_date_controle" value="{{$today|date_format:"%Y-%m-%d"}}" />
            <img id="ProcEditFrm_qualite_date_controle_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de contrôle" />
          </td>
        </tr>
        {{/if}}
        {{if !$fiche->qualite_date_verification || !$fiche->qualite_date_controle}}
        <tr>
          <td colspan="2" class="button">
            <button class="modify" type="button" onclick="saveVerifControle(this.form);">
              Enregister la date
            </button>
          </td>
        </tr>
        {{/if}}
        {{/if}}
      {{/if}}
      
      {{if $canAdmin && $fiche->valid_user_id}}
        <tr>
          <td colspan="2" class="button">
            {{if $fiche->annulee}}
            <button class="change" type="button" onclick="annuleFiche(this.form,0);" title="Rétablir la Fiche d'EI">
              Rétablir
            </button>
            {{else}}
            <button class="print" type="button" onclick="printIncident({{$fiche->fiche_ei_id}});">
              Imprimer la fiche
            </button>
            {{/if}}
          </td>
        </tr>
      {{/if}}

      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>