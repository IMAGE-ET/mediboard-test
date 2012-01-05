<!-- $Id$ -->

<script type="text/javascript">
function showConsultations(oTd, plageconsult_id){
  oTd = $(oTd);
  
  elements=oTd.up("table").select('div.event-container');;
  elements.each(function(e) {
      e.down("div").style.border="1px solid #999";
  });
  
  oTd.up("div").up("div").style.border="2px solid #444";
  
  var url = new Url("dPcabinet", "inc_consultation_plage");
  url.addParam("plageconsult_id", plageconsult_id);
  url.requestUpdate('consultations');
}

function checkPlage() {
  var form = getForm("editFrm");
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

function showConsultSiDesistement(){
  var url = new Url("dPcabinet", "vw_list_consult_si_desistement");
  url.addParam("chir_id", '{{$chirSel}}');
  url.pop(500, 500, "test");
}

function printPlage(plage_id) {
    var form = document.paramFrm;
    var url = new Url;
    url.setModuleAction("dPcabinet", "print_plages");
    url.addParam("plage_id", plage_id);
    url.addParam("show_tel", 1);
    url.popup(700, 550, "Planning");
  }

Main.add(function () {
  var planning = window["planning-{{$planning->guid}}"];
  Calendar.regField(getForm("changeDate").debut, null, {noView: true});
});
</script>

{{mb_script module=dPcabinet script=plage_consultation}}
{{mb_script module=ssr script=planning}}
<table class="main">
  <tr>
    <th style="width: 60%;">
      <form action="?" name="changeDate" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="plageconsult_id" value="0" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$prec}}')">&lt;&lt;&lt;</a>
        
        Semaine du {{$debut|date_format:"%A %d %b %Y"}} au {{$fin|date_format:"%A %d %b %Y"}}
        <input type="hidden" name="debut" class="date" value="{{$debut}}" onchange="this.form.submit()" />
        
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$suiv}}')">&gt;&gt;&gt;</a>
        <br />
        <a href="#1" onclick="$V($(this).getSurroundingForm().debut, '{{$today}}')">Aujourd'hui</a>
      </form>
      <br/>
      <button style="float:left;" class="new" onclick="PlageConsultation.edit('0');">Créer une nouvelle plage</button>
    </th>
    <td style="min-width: 350px;">
      <form action="?" name="selectPrat" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <select name="chirSel" style="width: 15em;" onchange="this.form.submit()">
          <option value="-1" {{if $chirSel == -1}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
          {{foreach from=$listChirs item=curr_chir}}
          <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
            {{$curr_chir->_view}}
          </option>
          {{/foreach}}
        </select>
        
        Cacher les : 
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_payees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_payees}}checked="checked"{{/if}} name="_hide_payees"> payées
            <input type="hidden" name="hide_payees" value="{{$hide_payees}}" />
          </label>
          <label>
            <input type="checkbox" onchange="$V(this.form.hide_annulees, this.checked ? 1 : 0); this.form.submit()" {{if $hide_annulees}}checked="checked"{{/if}} name="_hide_annulees"> annulées
            <input type="hidden" name="hide_annulees" value="{{$hide_annulees}}" />
          </label>
      </form>
      
      <br />
      
      {{if $chirSel && $chirSel != -1}}
        <button type="button" class="lookup" 
                {{if !$count_si_desistement}}disabled="disabled"{{/if}}
                onclick="showConsultSiDesistement()">
          {{tr}}CConsultation-si_desistement{{/tr}} ({{$count_si_desistement}})
        </button>
      {{/if}}
      
      {{if $plageSel->_id}}
        <a class="button new" href="?m={{$m}}&amp;tab=edit_planning&amp;consultation_id=0&amp;plageconsult_id={{$plageSel->_id}}">Planifier une consultation dans cette plage</a>
      {{/if}}
      
    </td>
  </tr>
  <tr>
    <td>
      {{mb_include module=ssr template=inc_vw_week}}

      <div class="small-info">
        <strong>L'affichage du semainier a évolué</strong>.
        <div>Désormais, vous pouvez utiliser les boutons qui apparaissent au survol de la plage de consultation pour :</div>
        <div>
            <button type="button" class="notext list">Liste</button>
            Afficher la liste des patients sur la droite
        </div>
        <div>
            <button type="button" class="notext edit">Edit</button>
            Modifier la plage selectionnée
        </div>
        <div>
            <button type="button" class="notext clock">RDV</button>
            Prendre un nouveau rendez-vous dans cette plage
        </div>
        Les anciennes commandes fonctionnent encore mais seront supprimées prochainement.
    </div>
    <td id="consultations">{{mb_include module=dPcabinet template=inc_consultations}}</td>
  </tr>
</table>