<!-- $Id$ -->

<script type="text/javascript">
function checkPlage() {
  var form = document.editFrm;
  var timeDebut = form._hour_deb.value + ":" +form._min_deb.value;
  var timeFin   = form._hour_fin.value + ":" +form._min_fin.value;

  if(timeDebut >= timeFin) {
    alert("L'heure de fin doit être supérieure à l'heure de début de la plage de consultation");
    return false;
  }  

  if(!checkForm(form)){
    return false;
  }
  
  if(form.nbaffected.value!= 0 && form.nbaffected.value!=""){
    if(timeDebut > form._firstconsult_time.value || timeFin < form._lastconsult_time.value){
      if(!(confirm("Certaines consultations se trouvent en dehors de la plage de consultation.\n\nVoulez-vous appliquer les modifications ?"))){
        return false;
      }
    }  
  }
    
  return true;
}

function putArrivee(oForm) {
  var today = new Date();
  oForm.arrivee.value = today.toDATETIME(true);
  oForm.submit();
}

function goToDate(oForm, date) {
  $V(oForm.debut, date);
}

Main.add(function () {
  Calendar.regField(getForm("changeDate").debut, null, {noView: true});
  
  PairEffect.initGroup("functionEffect", { 
    bStoreInCookie: true,
    sEffect: "appear"
  });
});

</script>

<table class="main">
  <tr>
    <th>
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="plageconsult_id" value="0" />
        
        <a href="javascript:;" onclick="$V(this.getForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>
        
        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />
        
        <a href="javascript:;" onclick="$V(this.getForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="javascript:;" onclick="$V(this.getForm().debut, '{{$today}}')">Aujourd'hui</a>
      </form>
    </th>
    <td>
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <label for="chirSel" title="Praticien dont on observe le planning de consultation">Praticien</label>
        <select name="chirSel" onchange="this.form.submit()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un praticien &mdash;</option>
          {{foreach from=$listChirs item=curr_chir}}
          <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
            {{$curr_chir->_view}}
          </option>
          {{/foreach}}
        </select>
    
        <label for="vue1" title="Type de vue du planning de consultation">Type de vue</label>
        <select name="vue1" onchange="this.form.submit()">
          <option value="0"{{if !$vue}}selected="selected"{{/if}}>Tout afficher</option>
          <option value="1"{{if $vue}}selected="selected"{{/if}}>Cacher les payés</option>
        </select>
      </form>
    </td>
  </tr>
  <tr>
    <td>
      <table id="weeklyPlanning">
        <tr>
          <!-- Affichage du nom des jours -->
          <th></th>
          {{foreach from=$listDays key=curr_day item=plagesPerDay}}
          <th>{{$curr_day|date_format:"%A %d"}}</th>
          {{/foreach}}
        </tr>       
        <!-- foreach sur les heures -->
        {{foreach from=$listHours item=curr_hour}}
        <tr>
          <th rowspan="4">{{$curr_hour}}h</th>
          <!-- foreach sur les minutes -->
          {{foreach from=$listMins item=curr_mins key=keyMins}}   
          {{if $keyMins}}
          </tr><tr>
          {{/if}}
            {{foreach from=$listDays item=curr_day}}
              {{assign var="keyAff" value="$curr_day $curr_hour:$curr_mins:00"}}
              {{assign var="affichage" value=$affichages.$keyAff}}
             
              {{if $affichage === "empty"}}
              <td class="empty"></td>
              {{elseif $affichage == "hours"}}
            <td class="empty" rowspan="4"></td>
              {{elseif $affichage === "full"}}
              
              {{else}}
                {{assign var="_listPlages" value=$listPlages.$curr_day}}
                {{assign var=plage value=$_listPlages.$affichage}}
              
              <td class="{{if $plageconsult_id == $plage->plageconsult_id}}selectedPlage{{else}}nonEmpty{{/if}}" rowspan="{{$plage->_nbQuartHeure}}">
              <a href="?m={{$m}}&amp;tab={{$tab}}&amp;plageconsult_id={{$plage->plageconsult_id}}" title="Voir le contenu de la plage">
                {{if $plage->libelle}}{{$plage->libelle}}<br />{{/if}}
                {{$plage->debut|date_format:$dPconfig.time}} - {{$plage->fin|date_format:$dPconfig.time}}
              </a>
              {{assign var="pct" value=$plage->_fill_rate}}
              {{if $pct gt 100}}
              {{assign var="pct" value=100}}
              {{/if}}
              {{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
              {{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
              {{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
              {{else}}{{assign var="backgroundClass" value="full"}}
              {{/if}} 
              <a href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plage->plageconsult_id}}" title="Planifier une consultation dans cette plage"> 
                <div class="progressBar">
                  <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
                  <div class="text">{{$plage->_affected}} {{if $plage->_nb_patients != $plage->_affected}}({{$plage->_nb_patients}}){{/if}} / {{$plage->_total|string_format:"%.0f"}}</div>
                </div>
              </a>
              </td>
              {{/if}}
            {{/foreach}}
          {{/foreach}}  
        {{/foreach}}
      </table>
      
    {{if $plageSel->plageconsult_id}}
    <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;plageconsult_id=0">Créer une nouvelle plage</a>
    {{/if}}
    <table class="form">
      <tr id="editplage-trigger">
        {{if !$plageSel->plageconsult_id}}
        <th class="category" colspan="4">Créer une plage</th>
        {{else}}
        <th class="category modify" colspan="4">
          <a style="float:right;" href="#" onclick="view_log('CPlageconsult',{{$plageSel->plageconsult_id}})">
            <img src="images/icons/history.gif" alt="historique" />
          </a>
          Modifier cette plage
        </th>
        {{/if}}
      </tr>
      <tbody class="functionEffect" id="editplage" style="display:none;">
      <tr>
        <td>
          <form name='editFrm' action='?m=dPcabinet' method='post' onsubmit='return checkPlage()'>
          <input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
          <input type='hidden' name='del' value='0' />
          {{mb_field object=$plageSel field="plageconsult_id" hidden=1 prop=""}}
          <input type='hidden' name='nbaffected' value='{{$plageSel->_affected}}' />
          <input type='hidden' name='_firstconsult_time' value='{{$_firstconsult_time}}' />
          <input type='hidden' name='_lastconsult_time' value='{{$_lastconsult_time}}' />
          <table class="form">
            <tr>
              <th>{{mb_label object=$plageSel field="chir_id"}}</th>
              <td><select name="chir_id" class="{{$plageSel->_props.chir_id}}">
                <option value="">&mdash; Choisir un praticien</option>
                {{foreach from=$listChirs item=curr_chir}}
                  <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
                  {{$curr_chir->_view}}
                  </option>
                {{/foreach}}
                </select>
              </td>
              <th>{{mb_label object=$plageSel field="libelle"}}</th>
              <td>{{mb_field object=$plageSel field="libelle"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="_hour_deb"}}</th>
              <td><select name="_hour_deb" class="notNull num">
                {{foreach from=$listHours item=curr_hour}}
                  <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plageSel->_hour_deb}} selected="selected" {{/if}}>
                    {{$curr_hour|string_format:"%02d"}}
                  </option>
                {{/foreach}}
                </select> h
                <select name="_min_deb">
                  {{foreach from=$listMins item=curr_min}}
                    <option value="{{$curr_min|string_format:"%02d"}}" {{if $curr_min == $plageSel->_min_deb}} selected="selected" {{/if}}>
                      {{$curr_min|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                </select> min
              </td>
              <th>{{mb_label object=$plageSel field="date"}}</th>
              <td>
                <select name="date" class="{{$plageSel->_props.date}}">
                  <option value="">&mdash; Choisir le jour</option>
                  {{foreach from=$listDaysSelect item=curr_day}}
                  <option value="{{$curr_day}}" {{if $curr_day == $plageSel->date}} selected="selected" {{/if}}>
                    {{$curr_day|date_format:"%A"}}
                  </option>
                  {{/foreach}}
                </select>
              </td>
            </tr>     
            <tr>
              <th>{{mb_label object=$plageSel field="_hour_fin"}}</th>
              <td>
                <select name="_hour_fin" class="notNull num moreEquals|_hour_deb">
                  {{foreach from=$listHours item=curr_hour}}
                    <option value="{{$curr_hour|string_format:"%02d"}}" {{if $curr_hour == $plageSel->_hour_fin}} selected="selected" {{/if}}>
                      {{$curr_hour|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                </select> h
                <select name="_min_fin">
                  {{foreach from=$listMins item=curr_min}}
                    <option value="{{$curr_min|string_format:"%02d"}}" {{if $curr_min == $plageSel->_min_fin}} selected="selected" {{/if}}>
                      {{$curr_min|string_format:"%02d"}}
                    </option>
                  {{/foreach}}
                </select> min
              </td>
              <th><label for="_repeat" title="Nombre de plages à créer">Nombre de plages</label></th>
              <td><input type="text" size="2" name="_repeat" value="1" /></td>
            </tr>      
            <tr>
              <th>{{mb_label object=$plageSel field="_freq"}}</th>
              <td>
                <select name="_freq">
                  <option value="05" {{if ($plageSel->_freq == "05")}} selected="selected" {{/if}}>05</option>
                  <option value="10" {{if ($plageSel->_freq == "10")}} selected="selected" {{/if}}>10</option>
                  <option value="15" {{if ($plageSel->_freq == "15") || (!$plageSel->plageconsult_id)}} selected="selected" {{/if}}>15</option>
                  <option value="20" {{if ($plageSel->_freq == "20")}} selected="selected" {{/if}}>20</option>
                  <option value="30" {{if ($plageSel->_freq == "30")}} selected="selected" {{/if}}>30</option>
                  <option value="45" {{if ($plageSel->_freq == "45")}} selected="selected" {{/if}}>45</option>
               </select> minutes</td>
              <th>
                <label for="_type_repeat" title="Espacement des plages">Type de répétition</label>
              </th>
              <td>
                <select name="_type_repeat">
                  <option value="1">Normale</option>
                  <option value="2">Une semaine sur deux</option>
                  <option value="3">Une semaine sur trois</option>
                </select>
              </td>
            </tr>
            <tr>
              {{if !$plageSel->plageconsult_id}}
              <td class="button" colspan="4"><button type="submit" class="submit">Créer</button></td>
              {{else}}
              <td class="button" colspan="4"><button type="submit" class="modify">Modifier</button></td>
              {{/if}}
            </tr>
          </table>
          </form>
      
          {{if $plageSel->plageconsult_id}}
	      <form name='removeFrm' action='?m=dPcabinet' method='post'>
      	  <input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
	      <input type='hidden' name='del' value='1' />
	      {{mb_field object=$plageSel field="plageconsult_id" hidden=1 prop=""}}
          <table class="form">
	        <tr>
	          <th class="category modify" colspan="2">Supprimer cette plage</th>
            </tr>
	        <tr>
	          <th>Supprimer cette plage pendant</th>
	          <td><input type='text' name='_repeat' size="1" value='1' /> semaine(s)</td>
	        </tr>
	        <tr>
	          <td class="button" colspan="2">
	            <button class="trash" type='button' onclick="confirmDeletion(this.form,{typeName:'la plage de consultations du',objName:'{{$plageSel->date|date_format:$dPconfig.longdate}}'})">
	              Supprimer
	            </button>
	          </td>
	        </tr>
	      </table>
	    </form>
        {{/if}}        
        </td>
      </tr>
      </tbody>
    </table>
    </td>
    <td>
      {{if $plageSel->plageconsult_id}}
      <a class="button new" href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plageSel->_id}}">Planifier une consultation dans cette plage</a>
      {{/if}}
      <table class="tbl">
        <tr>
          <th colspan="10">
            <strong>
            {{if $plageSel->plageconsult_id}}
            Consultations du {{$plageSel->date|date_format:$dPconfig.longdate}}
            {{else}}
            Pas de plage selectionnée
            {{/if}}
            </strong>
          </th>
        </tr>

        <tr>
          <th>Heure</th>
          <th>Nom</th>
          <th>Motif</th>
          <th>Remarques</th>
          <th>RDV</th>
          <th>Etat</th>
        </tr>
        {{foreach from=$plageSel->_ref_consultations item=_consult}}
        <tr>
          {{assign var="consult_id" value=$_consult->_id}}
          {{assign var=patient value=$_consult->_ref_patient}}
          {{assign var="href_consult" value="?m=$m&tab=edit_consultation&selConsult=$consult_id"}}
          {{assign var="href_planning" value="?m=$m&tab=edit_planning&consultation_id=$consult_id"}}

          {{if !$patient->_id}}
            {{assign var="style" value="style='background: #ffa;'"}}          
          {{elseif $_consult->premiere}} 
            {{assign var="style" value="style='background: #faa;'"}}
          {{else}} 
            {{assign var="style" value=""}}
          {{/if}}
          
          <td {{$style|smarty:nodefaults}}>
            <div style="float: left">
            {{if $patient->_id}}
              <a href="{{$href_consult}}" title="Voir la consultation">{{$_consult->heure|date_format:$dPconfig.time}}</a>
            {{else}}
              {{$_consult->heure|date_format:$dPconfig.time}}
            {{/if}}
            </div>
            
            {{assign var=categorie value=$_consult->_ref_categorie}}
            {{if $categorie->_id}}
            <div style="float: right">
              <img src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" alt="{{$categorie->nom_categorie}}" title="{{$categorie->nom_categorie}}" />
            </div>
            {{/if}}
          </td>

          <td class="text" {{$style|smarty:nodefaults}}>
            {{if !$patient->_id}}
            [PAUSE]
            {{else}}
            <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$patient->_id}}">
              <img src="images/icons/edit.png" alt="modifier" />
            </a>
            <a href="{{$href_consult}}"
              class="tooltip-trigger"
              onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
              {{$patient}}
            </a>
            {{/if}}
          </td>
          <td class="text" {{$style|smarty:nodefaults}}>
            {{if $patient->_id}}
              <a href="{{$href_consult}}"  title="Voir la consultation">{{$_consult->motif|truncate:35:"...":false|nl2br}}</a>
            {{else}}
              {{$_consult->motif|truncate:35:"...":false|nl2br}}
            {{/if}}
          </td>
          <td class="text" {{$style|smarty:nodefaults}}>
            {{if $patient->_id}}
              <a href="{{$href_consult}}"  title="Voir la consultation">{{$_consult->rques|truncate:35:"...":false|nl2br}}</a>
            {{else}}
              {{$_consult->rques|truncate:35:"...":false|nl2br}}
            {{/if}}
          </td>
          <td {{$style|smarty:nodefaults}}>
            <form name="etatFrm{{$_consult->_id}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_field object=$_consult field="consultation_id" hidden=1 prop=""}}
            <input type="hidden" name="chrono" value="{{$_consult|const:'PATIENT_ARRIVE'}}" />
            <input type="hidden" name="arrivee" value="" />
            </form>
            
            <form name="cancelFrm{{$_consult->_id}}" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            {{mb_field object=$_consult field="consultation_id" hidden=1 prop=""}}
            <input type="hidden" name="chrono" value="{{$_consult|const:'TERMINE'}}" />
            <input type="hidden" name="annule" value="1" />
            </form>
            
            <a class="action" href="{{$href_planning}}">
              <img src="images/icons/planning.png" title="Modifier le rendez-vous" alt="modifier" />
            </a>
						{{if $_consult->chrono == $_consult|const:'PLANIFIE' && $patient->_id}}
            <a class="action" href="#" onclick="putArrivee(document.etatFrm{{$_consult->_id}})">
              <img src="images/icons/check.png" title="Notifier l'arrivée du patient" alt="arrivee" />
            </a>
            <a class="action" href="#" onclick="if(confirm('Voulez-vous vraiment annuler cette consultation ?')) {document.cancelFrm{{$_consult->_id}}.submit()}">
              <img src="images/icons/cancel.png" title="Annuler ce rendez-vous" alt="annuler" />
            </a>
            {{/if}}
          </td>
          <td {{$style|smarty:nodefaults}}>{{if $patient->_id}}{{$_consult->_etat}}{{/if}}</td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>
