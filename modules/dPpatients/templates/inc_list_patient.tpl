{{if !$board}}

{{if $app->user_prefs.VitaleVision}}
  {{include file="../../dPpatients/templates/inc_vitalevision.tpl" debug=false keepFiles=true}}
{{else}}
  {{include file="../../dPpatients/templates/inc_intermax.tpl" debug=false}}
	
	<script type="text/javascript">
    Intermax.ResultHandler["Consulter Vitale"] =
    Intermax.ResultHandler["Lire Vitale"] = function() {
      var url = new Url;
      url.setModuleTab("dPpatients", "vw_idx_patients");
      url.addParam("useVitale", 1);
      url.redirect();
    }
	</script>
{{/if}}

<script type="text/javascript">
var Patient = {
  create : function(form) {
    var url = new Url;
    url.setModuleTab("dPpatients", "vw_edit_patients");
    url.addParam("patient_id", 0);
    url.addParam("useVitale", $V(form.useVitale));
    url.addParam("name",      $V(form.nom));
    url.addParam("firstName", $V(form.prenom));
    url.addParam("naissance_day",  $V(form.Date_Day));
    url.addParam("naissance_month",$V(form.Date_Month));
    url.addParam("naissance_year", $V(form.Date_Year));
    url.redirect();
  }
}
</script>
{{/if}}

<div id="modal-beneficiaire" style="display:none; text-align:center;">
  <p id="msg-multiple-benef">
    Cette carte vitale semble contenir plusieurs bénéficiaires, merci de sélectionner la personne voulue :
  </p>
  <p id="msg-confirm-benef" style="display: none;"></p>
	<p id="benef-nom">
	  <select id="modal-beneficiaire-select"></select>
    <span></span>
  </p>
  <div>
  	<button type="button" class="tick" onclick="VitaleVision.search(getForm('find'), $V($('modal-beneficiaire-select'))); VitaleVision.modalWindow.close();">{{tr}}Choose{{/tr}}</button>
	  <button type="button" class="cancel" onclick="VitaleVision.modalWindow.close();">{{tr}}Cancel{{/tr}}</button>
  </div>
</div>

<form name="find" action="?" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="new" value="1" />
<input type="hidden" name="useVitale" value="{{$useVitale}}" />

<table class="form">
  <tr>
    <th class="category" colspan="4">Recherche d'un dossier patient</th>
  </tr>

  <tr>
    <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
    <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
    <th><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
    <td><input tabindex="4" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
  </tr>
  
  <tr>
    <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
    <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
    <th><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
    <td><input tabindex="5" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
  </tr>
  
  <tr>
    <th><label for="jeuneFille" title="Nom de naissance">Nom de naissance</label></th>
    <td><input tabindex="3" type="text" name="jeuneFille" value="{{$jeuneFille|stripslashes}}" /></td>
    {{if $dPconfig.dPpatients.CPatient.tag_ipp && $dPsanteInstalled}}
    <th>IPP</th>
    <td>
      <input tabindex="6" type="text" name="patient_ipp" value="{{$patient_ipp}}"/>
    </td>
    {{else}}
    <td colspan="2" />
    {{/if}}
  </tr>
  
  <tr>
    <th colspan="2">
      <label for="Date_Day" title="Date de naissance du patient à rechercher">
        Date de naissance
      </label>
      <input type="hidden" name="naissance" value="{{$useNaissance}}" />
    </th>
    <td colspan="2">
    	{{if $naissance}}
			  {{assign var=naiss value=$naissance}}
			{{else}}
			  {{assign var=naiss value='0000-00-00'}}
			{{/if}}
         {{html_select_date
           time=$naiss
           start_year=1900
           field_order=DMY
           day_empty="Jour"
           month_empty="Mois"
           year_empty="Année"
           day_extra="tabindex='7'"
           month_extra="tabindex='8'"
           year_extra="tabindex='9'"
           all_extra="style='display:inline;'"}}
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="4">
      {{if $board}}
      <button class="search" type="button" onclick="updateListPatients()">
        {{tr}}Search{{/tr}}
      </button>
      {{else}}
      <button class="search" type="submit">
        {{tr}}Search{{/tr}}
      </button>
      {{if $app->user_prefs.GestionFSE}}
        {{if $app->user_prefs.VitaleVision}}
  	      <button class="search" type="button" onclick="VitaleVision.read();">
  	        Lire Vitale
  	      </button>
        {{else}}
  	      <button class="search" type="button" onclick="Intermax.trigger('Lire Vitale');">
  	        Lire Vitale
  	      </button>
  	      <button class="change intermax-result" type="button" onclick="Intermax.result('Lire Vitale');">
  	        Résultat Vitale
  	      </button>
        {{/if}}
      {{/if}}
      
      {{if $can->edit}}
      <button class="new" type="button" onclick="Patient.create(this.form);">
        {{tr}}Create{{/tr}}
        {{if $useVitale}}avec Vitale{{/if}}
      </button>
      {{/if}}
      
      {{/if}}
    </td>
  </tr>
</table>
</form>

<form name="fusion" action="?" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="a" value="fusion_pat" />
<table class="tbl">
  <tr>
    {{if ((!$dPconfig.dPpatients.CPatient.merge_only_admin || $can->admin)) && $can->edit}}
    <th style="width: 0.1%;">
    	<button type="submit" class="change notext" title="{{tr}}Merge{{/tr}}">
    		{{tr}}Merge{{/tr}}
    	</button>
    </th>
    {{/if}}
    <th>
      {{mb_title class=CPatient field=nom}}
      ({{$patientsCount}} {{tr}}found{{/tr}})
    </th>
    <th>{{mb_title class=CPatient field=naissance}}</th>
    <th>{{mb_title class=CPatient field=adresse}}</th>
    <th style="width: 0.1%;"></th>
  </tr>

  {{mb_ternary var="tabPatient" test=$board 
     value="vw_full_patients&patient_id=" 
     other="vw_idx_patients&patient_id="}}
  
  {{foreach from=$patients item=curr_patient}}
  <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
    {{if (!$dPconfig.dPpatients.CPatient.merge_only_admin || $can->admin) && $can->edit}}
    <td style="text-align: center;"><input type="checkbox" name="patients_id[]" value="{{$curr_patient->_id}}" /></td>
    {{/if}}
    <td class="text">
      {{if $curr_patient->_id == $patVitale->_id}}
      <div style="float:right;">
        <img src="images/icons/carte_vitale.png" alt="lecture vitale" title="Bénéficiaire associé à la carte Vitale" />
      </div>
      {{/if}}
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}&amp;useVitale={{$useVitale}}">
        {{mb_value object=$curr_patient field="_view"}}
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}&amp;useVitale={{$useVitale}}">
      	<span onmouseover="ObjectTooltip.createEx(this, '{{$curr_patient->_guid}}')">
          {{mb_value object=$curr_patient field="naissance"}}
      	</span>
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}&amp;useVitale={{$useVitale}}">
        {{$curr_patient->adresse}}
        {{$curr_patient->cp}}
        {{$curr_patient->ville}}
      </a>
    </td>
    <td>
      <a class="button search notext" href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$curr_patient->_id}}" title="Afficher le dossier complet">
        Afficher
      </a>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="100"><em>Aucun résultat exact</em></td>
  </tr>
  {{/foreach}}
  {{if $patientsSoundex|@count}}
  <tr>
    <th colspan="5">
      Résultats proches
      ({{$patientsSoundexCount}} {{tr}}found{{/tr}})
    </th>
  </tr>
  {{/if}}
  {{foreach from=$patientsSoundex item=curr_patient}}
  <tr {{if $patient->_id == $curr_patient->_id}}class="selected"{{/if}}>
    {{if (!$dPconfig.dPpatients.CPatient.merge_only_admin || $can->admin) && $can->edit}}
    <td style="text-align: center;"><input type="checkbox" name="patients_id[]" value="{{$curr_patient->_id}}" /></td>
    {{/if}}
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}&amp;useVitale={{$useVitale}}">
        {{mb_value object=$curr_patient field="_view"}}
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}&amp;useVitale={{$useVitale}}">
        {{mb_value object=$curr_patient field="naissance"}}
      </a>
    </td>
    <td class="text">
      <a href="?m={{$m}}&amp;tab={{$tabPatient}}{{$curr_patient->_id}}&amp;useVitale={{$useVitale}}">
        {{$curr_patient->adresse}}
        {{$curr_patient->cp}}
        {{$curr_patient->ville}}
      </a>
    </td>
    <td>
      <a class="button search notext" href="?m=dPpatients&tab=vw_full_patients&patient_id={{$curr_patient->_id}}" title="Afficher">
        Afficher
      </a>
    </td>
  </tr>
  {{/foreach}}
  
</table>
</form>
      