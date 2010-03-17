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

<script type="text/javascript">

reloadPatient = function(patient_id, link){
	{{if $board}}
    window.location="?m=dPpatients&tab=vw_full_patients&patient_id="+patient_id;
  {{else}}
    var url = new Url("dPpatients", "httpreq_vw_patient");
    url.addParam("patient_id", patient_id);
    url.requestUpdate("vwPatient", { onComplete: function(){ if(link){ markAsSelected(link); } }  } );
  {{/if}}
}

</script>

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

<form name="find" action="?" method="get" {{if $board}}onsubmit="return updateListPatients()"{{/if}}>

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="tab" value="{{$tab}}" />
<input type="hidden" name="new" value="1" />
<input type="hidden" name="useVitale" value="{{$useVitale}}" />

<table class="form">
  <tr>
    <th class="title" colspan="4">Recherche d'un dossier patient</th>
  </tr>

  <tr>
    <th><label for="nom" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
    <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
    <th><label for="cp" title="Code postal du patient à rechercher">Code postal</label></th>
    <td><input tabindex="6" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
  </tr>
  
  <tr>
    <th><label for="prenom" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
    <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
    <th><label for="ville" title="Ville du patient à rechercher">Ville</label></th>
    <td><input tabindex="7" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
  </tr>
    
  <tr>
    <th>
      <label for="Date_Day" title="Date de naissance du patient à rechercher">
        Date de naissance
      </label>
    </th>
    <td>
    	{{mb_include module=dPpatients template=inc_select_date date=$naissance tabindex=3}}
    </td>

    {{if $dPconfig.dPpatients.CPatient.tag_ipp && $dPsanteInstalled}}
    <th>IPP</th>
    <td>
      <input tabindex="8" type="text" name="patient_ipp" value="{{$patient_ipp}}"/>
    </td>
    {{else}}
    <td colspan="2" />
    {{/if}}
  </tr>
  
  <tr>
    <td class="button" colspan="4">
      <button class="search" tabindex="10" type="submit">{{tr}}Search{{/tr}}</button>
      
      {{if !$board}}
        {{if $app->user_prefs.GestionFSE}}
          {{if $app->user_prefs.VitaleVision}}
    	      <button class="search singleclick" type="button" tabindex="11" onclick="VitaleVision.read();">
    	        Lire Vitale
    	      </button>
          {{else}}
    	      <button class="search singleclick" type="button" tabindex="11" onclick="Intermax.trigger('Lire Vitale');">
    	        Lire Vitale
    	      </button>
    	      <button class="change intermax-result notext" tabindex="12" type="button" onclick="Intermax.result('Lire Vitale');">
    	        Résultat Vitale
    	      </button>
          {{/if}}
        {{/if}}
        
        {{if $can->edit}}
          {{if $nom || $prenom || $patient_ipp || $naissance}}
          <button class="new" type="button" tabindex="15" onclick="Patient.create(this.form);">
            {{tr}}Create{{/tr}}
            {{if $useVitale}}avec Vitale{{/if}}
          </button>
          {{/if}}
        {{/if}}
      {{/if}}
    </td>
  </tr>
</table>
</form>

{{if $dPconfig.dPpatients.CPatient.limit_char_search && ($nom != $nom_search || $prenom != $prenom_search)}}
<div class="small-info">
	La recherche est volontairement limitée aux {{$dPconfig.dPpatients.CPatient.limit_char_search}} premiers caractères 
  <ul>
	  {{if $nom != $nom_search}}
	  <li>pour le <strong>nom</strong> : '{{$nom_search}}'</li>
    {{/if}}  	
    {{if $prenom != $prenom_search}}
    <li>pour le <strong>prénom</strong> : '{{$prenom_search}}'</li>
    {{/if}}   
  </ul>
</div>
{{/if}}

<form name="fusion" action="?" method="get">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="a" value="fusion_pat" />
<input type="hidden" name="readonly_class" value="1" />
<input type="hidden" name="objects_class" value="CPatient" />

<table class="tbl" id="list_patients">
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
  
  {{foreach from=$patients item=_patient}}
  {{mb_include module=dPpatients template=inc_list_patient_line}}
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
  {{foreach from=$patientsSoundex item=_patient}}
  {{mb_include module=dPpatients template=inc_list_patient_line}}
  {{/foreach}}
  
</table>
</form>
      