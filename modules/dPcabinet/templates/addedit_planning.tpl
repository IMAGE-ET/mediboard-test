<!-- $Id$ -->

{{mb_script module=patients    script=pat_selector    ajax=true}}
{{mb_script module=cabinet     script=plage_selector  ajax=true}}
{{mb_script module=cabinet     script=file            ajax=true}}
{{mb_script module=compteRendu script=document        ajax=true}}
{{mb_script module=compteRendu script=modele_selector ajax=true}}

{{if $consult->_id}}
  {{mb_ternary var=object_consult test=$consult->_is_anesth value=$consult->_ref_consult_anesth other=$consult}}
  {{mb_include module="dPfiles" template="yoplet_uploader" object=$object_consult}}
{{/if}}

{{assign var=attach_consult_sejour value=$conf.dPcabinet.CConsultation.attach_consult_sejour}}

{{if "maternite"|module_active}}
  {{assign var=maternite_active value="1"}}
{{else}}
  {{assign var=maternite_active value="0"}}
{{/if}}

<script type="text/javascript">
Medecin = {
  form: null,
  edit : function() {
    this.form = getForm("editFrm");
    var url = new Url("dPpatients", "vw_medecins");
    url.popup(700, 450, "Medecin");
  },
  
  set: function(id, view) {
    $('_adresse_par_prat').show().update('Autres : '+view);
    $V(this.form.adresse_par_prat_id, id);
    $V(this.form._correspondants_medicaux, '', false);
  }
};

refreshListCategorie = function(praticien_id){
  var url = new Url("dPcabinet", "httpreq_view_list_categorie");
  url.addParam("praticien_id", praticien_id);
  url.requestUpdate("listCategorie");
}

refreshFunction = function(chir_id) {
  {{if !$consult->_id && $conf.dPcabinet.CConsultation.create_consult_sejour}}
    var url = new Url("dPcabinet", "ajax_refresh_secondary_functions");
    url.addParam("chir_id", chir_id);
    url.requestUpdate("secondary_functions");
  {{/if}}
}

changePause = function(){
  var oForm = getForm("editFrm");
  if(oForm._pause.checked){
    oForm.patient_id.value = "";
    oForm._pat_name.value = "";
    $("viewPatient").hide();
    $("infoPat").update("");
  }else{
    $("viewPatient").show();
  }
}

requestInfoPat = function() {
  var oForm = getForm("editFrm");
  if(!oForm.patient_id.value){
    return false;
  }
  var url = new Url("patients", "httpreq_get_last_refs");
  url.addElement(oForm.patient_id);
  url.addElement(oForm.consultation_id);
  url.requestUpdate("infoPat");
}

ClearRDV = function(){
  var oForm = getForm("editFrm");
  $V(oForm.plageconsult_id, "", true);
  $V(oForm._date, "");
  $V(oForm.heure, "");
  if (Preferences.choosePatientAfterDate == 1) {
    PlageConsultSelector.init();
  }
}

checkFormRDV = function(form){
  if(!form._pause.checked && form.patient_id.value == ""){
    alert("Veuillez sélectionner un patient");
    PatSelector.init();
    return false;
  }
  else{
    var infoPat = $('infoPat');
    var operations = infoPat.select('input[name=_operation_id]');
    var checkedOperation = operations.find(function (o) {return o.checked});
    if (checkedOperation) {
      form._operation_id.value = checkedOperation.value;
    }
    
    return checkForm(form);
  }
}

submitRDV = function() {
  var form = getForm('editFrm');
  if (checkFormRDV(form)) {
    form.submit(); 
  }
}

printForm = function() {
  var url = new Url("dPcabinet", "view_consultation"); 
  url.addElement(getForm("editFrm").consultation_id);
  url.popup(700, 500, "printConsult");
  return;
}

printDocument = function(iDocument_id) {
  var form = getForm("editFrm");
  if (iDocument_id.value != 0) {
    var url = new Url("dPcompteRendu", "edit_compte_rendu");
    url.addElement(form.consultation_id, "object_id");
    url.addElement(iDocument_id, "modele_id");
    url.popup(700, 600, "Document");
    return true;
  }
  return false;
}

checkCorrespondantMedical = function(){
  var form = getForm("editFrm");
  var url = new Url("dPplanningOp", "ajax_check_correspondant_medical");
  url.addParam("patient_id", $V(form.patient_id));
  url.addParam("object_id" , $V(form.consultation_id));
  url.addParam("object_class", '{{$consult->_class}}');
  url.requestUpdate("correspondant_medical");
}

Main.add(function () {
  var form = getForm("editFrm");

  requestInfoPat();
  
  {{if $plageConsult->_id && !$consult->_id && !$consult->heure}}
    $V(form.chir_id, '{{$plageConsult->chir_id}}', false);
    $V(form.plageconsult_id, '{{$plageConsult->_id}}');
    refreshListCategorie({{$plageConsult->chir_id}});
    PlageConsultSelector.init();
  {{elseif ($pat->_id || $date_planning) && !$consult->_id && !$consult->heure}}
    if($V(form.chir_id)) {
      PlageConsultSelector.init();
    }
  {{/if}}
  
  {{if $consult->_id && $consult->patient_id}}
    $("print_fiche_consult").disabled = "";
  {{/if}}
  
});

Main.add(function() {
  var form = getForm("editFrm");
  var url = new Url("system", "ajax_seek_autocomplete");
  url.addParam("object_class", "CPatient");
  url.addParam("field", "patient_id");
  url.addParam("view_field", "_pat_name");
  url.addParam("input_field", "_seek_patient");
  url.autoComplete(form.elements._seek_patient, null, {
    minChars: 3,
    method: "get",
    select: "view",
    dropdown: false,
    width: "300px",
    afterUpdateElement: function(field,selected){
      $V(field.form.patient_id, selected.getAttribute("id").split("-")[2]);
      $V(field.form.elements._pat_name, selected.down('.view').innerHTML);
      $V(field.form.elements._seek_patient, "");
    }
  });
  Event.observe(form.elements._seek_patient, 'keydown', PatSelector.cancelFastSearch);
});

</script>

<form name="editFrm" action="?m={{$m}}" class="watched" method="post" onsubmit="return checkFormRDV(this)"> 
<input type="hidden" name="dosql" value="do_consultation_aed" />
<input type="hidden" name="del" value="0" />
{{mb_key object=$consult}}
{{if $dialog}}
  <input type="hidden" name="postRedirect" value="m=cabinet&a=edit_planning&dialog=1" />
{{/if}}
<input type="hidden" name="adresse_par_prat_id" value="{{$consult->adresse_par_prat_id}}" />
<input type="hidden" name="annule" value="{{$consult->annule|default:"0"}}" />
<input type="hidden" name="arrivee" value="" />
<input type="hidden" name="chrono" value="{{$consult|const:'PLANIFIE'}}" />
<input type="hidden" name="_operation_id" value="" />

{{if !$dialog}}
  <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;consultation_id=0">
    {{tr}}CConsultation-title-create{{/tr}}
  </a>
{{/if}}

{{if $consult->_id}}
<a class="button search" href="?m={{$m}}&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}" style="float: right;">
  {{tr}}CConsultation-title-access{{/tr}}
</a>
{{/if}}

<table class="form">  
  <tr>
    {{if $consult->_id}}
      <th class="title modify" colspan="5">
        {{mb_include module=system template=inc_object_notes      object=$consult}}
        {{mb_include module=system template=inc_object_idsante400 object=$consult}}
        {{mb_include module=system template=inc_object_history    object=$consult}}
        {{tr}}CConsultation-title-modify{{/tr}}
        {{if $pat->_id}}de {{$pat->_view}}{{/if}}
        par le Dr {{$chir}}
      </th>
    {{else}}
      <th class="title" colspan="5">{{tr}}CConsultation-title-create{{/tr}}</th>
    {{/if}}
  </tr>
  {{if $consult->annule == 1}}
  <tr>
    <th class="category cancelled" colspan="3">{{tr}}CConsultation-annule{{/tr}}</th>
  </tr>
  {{/if}}
  
  {{if $consult->_locks}}
    <tr>
      <td colspan="3">
          {{if $can->admin}}
        <div class="small-warning">
          Attention, vous êtes en train de modifier une consultation ayant :
          {{else}}
        <div class="small-info">
          <input type="hidden" name="_locked" value="1" />
          Vous ne pouvez pas modifier la consultation pour les raisons suivantes  (consulter un administrateur pour plus de renseignements) :
          {{/if}}
        
          <ul>
            {{if in_array("datetime", $consult->_locks)}} 
            <li>le rendez-vous <strong>passé de {{mb_value object=$consult field=_datetime format=relative}}</strong></li>
            {{/if}}

            {{if in_array("termine", $consult->_locks)}} 
            <li>la consultation <strong>notée terminée</strong></li>
            {{/if}}
            
            {{if in_array("valide", $consult->_locks)}} 
            <li>la cotation <strong>validée</strong></li>
            {{/if}}

          </ul>
        </div>
      </td>
    </tr>
  {{elseif $consult->_id && $consult->_datetime|iso_date == $today}}
    <tr>
      <td colspan="3">
        <div class="small-warning">
          Attention, vous êtes en train de modifier 
          <strong>une consultation du jour</strong>.
        </div>
      </td>
    </tr>
  {{/if}}


  <tr>
    <td style="width: 50%;">
      <fieldset>
        <legend>Informations sur la consultation</legend>
          <table class="form">
    
            <tr>
              <th class="narrow">
                <label for="chir_id" title="Praticien pour la consultation">Praticien</label>
              </th>
              <td>
                <select name="chir_id" style="width: 15em;" class="notNull"
                  onChange="ClearRDV(); refreshListCategorie(this.value); refreshFunction(this.value);
                    if (this.value != '') { 
                      $V(this.form._function_id, '');
                      var facturable = this.options[this.selectedIndex].get('facturable');
                      this.form.___facturable.checked = facturable ? 'checked' : '';
                      $V(this.form._facturable, facturable);
                    }">
                  <option value="">&mdash; Choisir un praticien</option>
                  {{foreach from=$listPraticiens item=curr_praticien}}
                  <option class="mediuser" style="border-color: #{{$curr_praticien->_ref_function->color}};" value="{{$curr_praticien->user_id}}"
                    {{if $chir->_id == $curr_praticien->user_id}} selected="selected" {{/if}} data-facturable="{{$curr_praticien->_ref_function->facturable}}">
                    {{$curr_praticien->_view}}
                    {{if $app->user_prefs.viewFunctionPrats}}
                      - {{$curr_praticien->_ref_function->_view}}
                    {{/if}}
                  </option>
                 {{/foreach}}
                </select>
                <input type="checkbox" name="_pause" value="1" onclick="changePause()" {{if $consult->_id && $consult->patient_id==0}} checked="checked" {{/if}} {{if $attach_consult_sejour && $consult->_id}}disabled="disabled"{{/if}}/>
                <label for="_pause" title="Planification d'une pause">Pause</label>
              </td>
            </tr>
            {{if !$consult->_id && $conf.dPcabinet.CConsultation.create_consult_sejour}}
              <tr>
                <th>
                  {{mb_label object=$consult field=_function_secondary_id}}
                </th>
                <td id="secondary_functions">
                  {{mb_include module=cabinet template=inc_refresh_secondary_functions}}
                </td>
              </tr>
            {{/if}}
            <tr id="viewPatient" {{if $consult->_id && $consult->patient_id==0}}style="display:none;"{{/if}}>
              <th>
                {{mb_label object=$consult field="patient_id"}}
              </th>
              <td>
                {{mb_field object=$pat field="patient_id" hidden=1 ondblclick="PatSelector.init()" onchange="requestInfoPat(); $('button-edit-patient').setVisible(this.value);"}}
                <input type="text" name="_pat_name" style="width: 15em;" value="{{$pat->_view}}" readonly="readonly" onfocus="PatSelector.init()" onchange="checkCorrespondantMedical()"/>
                <button class="search notext" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
                <script type="text/javascript">
                  PatSelector.init = function(){
                    this.sForm      = "editFrm";
                    this.sId        = "patient_id";
                    this.sView      = "_pat_name";
                    var seekResult  = $V(getForm(this.sForm)._seek_patient).split(" ");
                    this.sName      = seekResult[0] ? seekResult[0] : "";
                    this.sFirstName = seekResult[1] ? seekResult[1] : "";
                    {{if "maternite"|module_active && !$consult->_id}}
                      this.sSexe = "_patient_sexe";
                    {{/if}}
                    this.pop();
                  }
                </script>
                <button id="button-edit-patient" type="button" 
                        onclick="location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id='+this.form.patient_id.value" 
                        class="edit notext" {{if !$pat->_id}}style="display: none;"{{/if}}>
                  {{tr}}Edit{{/tr}}
                </button>
                <br />
                <input type="text" name="_seek_patient" style="width: 13em;" placeholder="{{tr}}fast-search{{/tr}}" "autocomplete" onblur="$V(this, '')" />
              </td>
            </tr>         
            
            <tr>
              <th>{{mb_label object=$consult field="motif"}}</th> 
              <td>
                {{mb_field object=$consult field="motif" class="autocomplete" rows=5 form="editFrm"}}
              </td>
            </tr>
            
            <tr>
              <th>{{mb_label object=$consult field="rques"}}</th> 
              <td>
                {{mb_field object=$consult field="rques" class="autocomplete" rows=5 form="editFrm"}}
              </td>
            </tr>
            
            {{if $consult->sejour_id}}
              <tr>
                <th>{{mb_label object=$consult field="brancardage"}}</th> 
                <td>
                  {{mb_field object=$consult field="brancardage" class="autocomplete" rows=5 form="editFrm"}}
                </td>
              </tr>
            {{/if}}
          </table>
       </fieldset>
    </td>
    <td style="width: 50%;">
      <fieldset>
        <legend>Rendez-vous</legend>
            
        <table class="form">
          <tr>
            <th>{{mb_label object=$consult field="plageconsult_id"}}</th>
            <td>
              <input type="text" name="_date" style="width: 15em;" value="{{$consult->_date|date_format:"%A %d/%m/%Y"}}" onfocus="PlageConsultSelector.init()" readonly="readonly" onchange="if (this.value != '') $V(this.form._function_id, '')"/>
              <input type="hidden" name="_date_planning" value="{{$date_planning}}" />
              {{mb_field object=$consult field="plageconsult_id" hidden=1 ondblclick="PlageConsultSelector.init()"}}
              <script type="text/javascript">
                PlageConsultSelector.init = function(){
                  this.sForm            = "editFrm";
                  this.sHeure           = "heure";
                  this.sPlageconsult_id = "plageconsult_id";
                  this.sDate            = "_date";
                  this.sChir_id         = "chir_id";
                  this.sFunction_id     = "_function_id";
                  this.sDatePlanning    = "_date_planning";
                  this.options          = {width: 1200, height: 900};
                  this.modal();
                }
             </script> 
             <button class="search notext" type="button" onclick="PlageConsultSelector.init()">Choix de l'horaire</button>
            </td>
          </tr>
  
          <tr>
            <th>{{mb_label object=$consult field="heure"}}</th>
            <td>
              <input type="text" name="heure" value="{{$consult->heure}}" style="width: 15em;" onfocus="PlageConsultSelector.init()" readonly="readonly" />
              {{if $consult->patient_id}}
              ({{$consult->_etat}})
              <br />
              <a class="button new" href="?m=dPcabinet&tab=edit_planning&pat_id={{$consult->patient_id}}&consultation_id=0&date_planning={{$consult->_date}}&chir_id={{$chir->_id}}">Nouveau RDV pour ce patient</a>
              {{/if}}
            </td>
          </tr>
            
          <tr>
            <th>{{mb_label object=$consult field="premiere"}}</th>
            <td>{{mb_field object=$consult field="premiere" typeEnum=checkbox}}</td>
          </tr>
          
          {{if $conf.dPcabinet.CConsultation.use_last_consult}}
          <tr>
            <th>{{mb_label object=$consult field="derniere"}}</th>
            <td>{{mb_field object=$consult field="derniere" typeEnum=checkbox}}</td>
          </tr>
          {{/if}}
  
          <tr>
            <th>{{mb_label object=$consult field="adresse"}}</th>
            <td>
              <input type="checkbox" name="_check_adresse" value="1"
                {{if $consult->_check_adresse}} checked="checked" {{/if}}
                onchange="$('correspondant_medical').toggle();
                $('_adresse_par_prat').toggle();
                if (this.checked) {
                  this.form.adresse.value = 1;
                } else {
                  this.form.adresse.value = 0;
                  this.form.adresse_par_prat_id.value = '';
                }" />
              {{mb_field object=$consult field="adresse" hidden="hidden"}}
            </td>
          </tr>
          
          {{if $maternite_active && @$modules.maternite->_can->read}}
            <tr>
              <th>{{tr}}CGrossesse{{/tr}}</th>
              <td>
                 {{mb_include module=maternite template=inc_input_grossesse object=$consult patient=$pat}}
              </td>
            </tr>
          {{/if}}
          
          <tr id="correspondant_medical" {{if !$consult->_check_adresse}}style="display: none;"{{/if}}>
            {{assign var="object" value=$consult}}
            {{mb_include module=planningOp template=inc_check_correspondant_medical}}
          </tr>
          
          <tr>
            <td></td>
            <td colspan="3">
              <div id="_adresse_par_prat" style="{{if !$medecin_adresse_par}}display:none{{/if}}; width: 300px;">
                {{if $medecin_adresse_par}}Autres : {{$medecin_adresse_par->_view}}{{/if}}
              </div>
            </td>
          </tr>
          
          <tr>
            <th>{{mb_label object=$consult field="si_desistement"}}</th>
            <td>{{mb_field object=$consult field="si_desistement" typeEnum="checkbox"}}</td>
          </tr>
          
          {{if $attach_consult_sejour}}
          <tr>
            <th>{{mb_label object=$consult field="_forfait_se"}}</th>
            <td>{{mb_field object=$consult field="_forfait_se" typeEnum="checkbox"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$consult field="_forfait_sd"}}</th>
            <td>{{mb_field object=$consult field="_forfait_sd" typeEnum="checkbox"}}</td>
          </tr>
          <tr>
            <th>{{mb_label object=$consult field="_facturable"}}</th>
            <td>{{mb_field object=$consult field="_facturable" typeEnum="checkbox"}}</td>
          </tr>
          {{/if}}
          
          <tr>
            <th>{{mb_label object=$consult field="duree"}}</th>
            <td>
              <select name="duree">
                {{foreach from=1|range:15 item=i}}
                  {{if $plageConsult->_id}}
                    {{assign var=freq value=$plageConsult->_freq}}
                    {{math equation=x*y x=$i y=$freq assign=duree_min}}
                    {{math equation=floor(x/60) x=$duree_min assign=duree_hour}}
                    {{math equation=(x-y*60) x=$duree_min y=$duree_hour assign=duree_min}}
                  {{/if}}
                  <option value="{{$i}}" {{if $consult->duree == $i}}selected{{/if}}>
                    x{{$i}} {{if $plageConsult->_id}}({{if $duree_hour}}{{$duree_hour}}h{{/if}}{{if $duree_min}}{{$duree_min}}min{{/if}}){{/if}}</option>
                {{/foreach}}
              </select>
            </td>
          </tr>
          <tbody id="listCategorie">
            {{if $consult->_id || $chir->_id}}
              {{mb_include template="httpreq_view_list_categorie" 
                categorie_id=$consult->categorie_id 
                categories=$categories
                listCat=$listCat}}
            {{/if}}
          </tbody>
          <tr>
            <th>Recherche par groupe</th>
            <td>
              <select name="_function_id" style="width: 15em;" onchange = "if (this.value != '') { $V(this.form.chir_id, ''); $V(this.form._date, '');}">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                {{foreach from=$listFunctions item=_function}}
                <option value="{{$_function->_id}}" class="mediuser" style="border-color: #{{$_function->color}};">
                  {{$_function->_view}}
                </option>
                {{/foreach}}
              </select>
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
</table>

</form>

<table class="form">
  <tr>
    <td colspan="2" class="button">

      {{if $consult->_id}}
        {{if !$consult->_locks || $can->admin}}
          <button class="modify" type="submit" onclick="submitRDV();">
            {{tr}}Save{{/tr}}
          </button>

        {{/if}}

        {{mb_include template=inc_cancel_planning}}              
                      
        
        <button class="print" id="print_fiche_consult" type="button" onclick="printForm();"
          {{if !$consult->patient_id}} disabled="disabled" {{/if}}
        >
          {{tr}}Print{{/tr}}
        </button>
      {{else}}
        <button class="submit" type="submit" onclick="submitRDV();">
          {{tr}}Create{{/tr}}
        </button>
      {{/if}}
    
    </td>
  </tr>
</table>

<table class="form">
  <tr>
    <td class="halfPane" style="width: 50%;">
      <fieldSet>
        <legend>Infos patient</legend>
        <div class="text" id="infoPat">
          <div class="empty">Aucun patient sélectionné</div>
        </div>
      </fieldSet>
    </td>
    <td class="halfPane">
      {{if $consult->_id}}
      <fieldset>
        <legend>{{tr}}CCompteRendu{{/tr}} - {{tr}}{{$object_consult->_class}}{{/tr}}</legend>
        <div id="documents">
          <script type="text/javascript">
            Document.register('{{$object_consult->_id}}','{{$object_consult->_class}}','{{$consult->_praticien_id}}','documents');
          </script>
        </div>
      </fieldset>
      <fieldset>
        <legend>{{tr}}CFile{{/tr}} - {{tr}}{{$consult->_class}}{{/tr}}</legend>            
        <div id="files">
          <script type="text/javascript">
            File.register('{{$consult->_id}}','{{$consult->_class}}', 'files');
          </script>
        </div>
      </fieldset>
      {{/if}}
    </td>
  </tr>
</table>

