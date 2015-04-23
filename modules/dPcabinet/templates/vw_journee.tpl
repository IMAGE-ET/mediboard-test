{{mb_script module="dPcabinet" script="edit_consultation" ajax=$ajax}}
{{mb_script module="dPcabinet" script="plage_consultation" ajax=$ajax}}

<script>

  Consultation.useModal();

  see_consult_without_dhe = function(sdate) {
    var url = new Url("admissions", "httpreq_vw_preadmissions");
    url.addParam("filter", "dhe");
    if (sdate) {url.addParam("date", sdate);}
    url.addParam("is_modal", 1);
    url.requestModal();
  };

Main.add(function () {
  if (document.selCabinet && "{{$offline}}" == 0){
    Calendar.regField(getForm("selCabinet").date, null, {noView: true});
  }
  
  // Mise à jour du compteur de patients arrivés
  if($('tab_main_courante')){
    var link = $('tab_main_courante').select("a[href=#consultations]")[0];
    link.update('Reconvocations <small>({{$nb_attente}} / {{$nb_a_venir}})</small>');
    {{if $nb_attente == '0'}}
      link.addClassName('empty');
    {{else}}
      link.removeClassName('empty');
    {{/if}}
  }
});

synchronizeView = function(form) {
  var empty = $V(form._empty) ? 1 : 0;
  $V(form.empty, empty);
  var canceled = $V(form._canceled) ? 1 : 0;
  $V(form.canceled, canceled);
  var paid = $V(form._paid) ? 1 : 0;
  $V(form.paid, paid);
  var finished = $V(form._finished) ? 1 : 0;
  $V(form.finished, finished);
  var immediate = $V(form._immediate) ? 1 : 0;
  $V(form.immediate, immediate);
  var matin = $V(form._matin) ? 1 : 0;
  $V(form.matin, matin);
  var apres_midi = $V(form._apres_midi) ? 1 : 0;
  $V(form.apres_midi, apres_midi);
  form.submit();
};

updatePratDisplayed = function() {
  var selected = [];
  $$('.select_prat').each(function(elt) {
    if ($V(elt)) {
      var prat = elt.name.split("_")[1];
      selected.push(prat);
    }
  });

  var oform = getForm('selCabinet');
  $V(oform.prats_selected, selected.join("-"));
  oform.submit();
};

Reconvocation = {
  checkPraticien: function() {
    var form = getForm('Create-Reconvocation');
    
    if ($V(form.prat_id) == '') {
      alert('Veuillez sélectionner un praticien');
      return false;
    }
    return true;
  },
  
  choosePatient: function() {
    Consultations.stop();

    {{if !$mode_urgence}}
      if (!Reconvocation.checkPraticien()) {
        return false;
      }
    {{/if}}
    
    {{if $mode_urgence}}
      this.createConsult();
    {{/if}}
    
    return false;
  },
  createConsult: function() {
    var url = new Url("dPcabinet", "ajax_create_reconvoc");
    url.requestModal(500);
  },
  submit: function() {
    var form = getForm('Create-Reconvocation');
    return onSubmitFormAjax(form, { onComplete: Consultations.start.curry(80) });  
  } 
}
</script>

{{mb_ternary var=current_m test=$mode_urgence value=dPurgences other=dPcabinet}}
<table class="main">
  {{if $mode_urgence}}
    <tr>
      <td>
        <script>
          PatSelector.init = function() {
            this.sForm = 'Create-Reconvocation';
            this.sId   = 'patient_id';
            this.sView = '_patient_view';
            this.pop();
          }
        </script>

        <form name="Create-Reconvocation" method="post" action="?" onsubmit="return Reconvocation.choosePatient();">
          <input type="hidden" name="dosql" value="do_consult_now" />
          <input type="hidden" name="m" value="dPcabinet" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="_datetime" value="now" class="dateTime" />

          <input type="hidden" name="patient_id" class="ref notNull" onchange="Reconvocation.submit();"/>
          <input type="hidden" name="_patient_view" />
          <input type="hidden" name="prat_id" value="" />
          <input type="hidden" name="motif" value="" />
          <button type="submit" class="new">Reconvocation immédiate</button>
        </form>

      </td>
    </tr>
  {{else}}
    <tr>
      <td>
        <form name="selCabinet" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="prats_selected" value="" />
        <table class="form">
          <tr>
            <th class="title" colspan="100">
              {{if $nb_anesth}}
                <button onclick="see_consult_without_dhe('{{$date}}');" class="button search" type="button" style="float:right;">Voir les consultations sans intervention prévue</button>
              {{/if}}
              Journée de consultation du
              {{$date|date_format:$conf.longdate}}
              <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
            </th>
          </tr>
          {{if !$offline}}
            <tr>
              {{if $mode_maternite}}
                <td colspan="2">
                  Consultation des sages femmes
                </td>
              {{else}}
                <th>
                  <label for="cabinet_id" title="Sélectionner un groupe">Groupe de praticiens</label>
                </th>
                <td>
                  <select name="cabinet_id" onchange="this.form.submit()" style="width: 15em;">
                    <option value="">&mdash; Choisir un groupe</option>
                    {{foreach from=$cabinets item=curr_cabinet}}
                      <option value="{{$curr_cabinet->_id}}" class="mediuser" style="border-color: #{{$curr_cabinet->color}}" {{if $curr_cabinet->_id == $cabinet_id}} selected="selected" {{/if}}>
                        {{$curr_cabinet->_view}}
                      </option>
                    {{/foreach}}
                  </select>
                </td>
              {{/if}}
              <td {{if $mode_urgence}}colspan="5"{{/if}}>
                <input name="_empty"      type="checkbox" value="1" onclick="synchronizeView(this.form);" {{if $empty}}checked="checked"{{/if}} />
                <input name="empty"       type="hidden"   value="{{$empty}}" />
                <label for="_empty"       title="Afficher les plages vides">Plages vides</label>
                <input name="_canceled"   type="checkbox" value="1" onclick="synchronizeView(this.form);" {{if $canceled}}checked="checked"{{/if}} />
                <input name="canceled"    type="hidden"   value="{{$canceled}}" />
                <label for="_canceled"    title="Afficher les consultations annulées">Annulées</label>
                <input name="_paid"       type="checkbox" value="1" onclick="synchronizeView(this.form);" {{if $paid}}checked="checked"{{/if}} />
                <input name="paid"        type="hidden"   value="{{$paid}}" />
                <label for="_paid"        title="Afficher les consultations réglées">Réglées</label>
                <input name="_finished"   type="checkbox" value="1" onclick="synchronizeView(this.form);" {{if $finished}}checked="checked"{{/if}} />
                <input name="finished"    type="hidden"   value="{{$finished}}" />
                <label for="_finished"    title="Afficher les consultations terminées">Terminées</label>
                <input name="_immediate"  type="checkbox" value="1" onclick="synchronizeView(this.form);" {{if $immediate}}checked="checked"{{/if}} />
                <input name="immediate"   type="hidden"   value="{{$immediate}}" />
                <label for="_immediate"   title="Afficher les consultations immédiates">Immédiates</label>
                <input name="_matin"      type="checkbox" value="1" onclick="synchronizeView(this.form);" {{if $matin}}checked="checked"{{/if}} />
                <input name="matin"       type="hidden"   value="{{$matin}}" />
                <label for="_matin"       title="Afficher les consultations du matin">Matin</label>
                <input name="_apres_midi" type="checkbox" value="1" onclick="synchronizeView(this.form);" {{if $apres_midi}}checked="checked"{{/if}} />
                <input name="apres_midi"  type="hidden"   value="{{$apres_midi}}" />
                <label for="_apres_midi"  title="Afficher les consultations de l'après-midi">Après-midi</label>
              </td>
              {{if !$mode_urgence}}
                <th>
                  <label for="mode_vue" title="Mode de vue du planning">Mode de vue</label>
                </th>
                <td colspan="3">
                  <select name="mode_vue" onchange="this.form.submit()">
                    <option value="vertical" {{if $mode_vue == "vertical"}}selected="selected"{{/if}}>Vertical</option>
                    <option value="horizontal" {{if $mode_vue == "horizontal"}}selected="selected"{{/if}}>Horizontal</option>
                  </select>
                </td>
              {{/if}}
            </tr>
          {{else}}
            <tr>
              <th class="title" colspan="100">
                {{$cabinet}}
              </th>
            </tr>
          {{/if}}
        </table>
        </form>

        <div id="prats">
          {{foreach from=$praticiens_av item=_prat}}
            <label>
              <input class="select_prat" type="checkbox" name="prats_{{$_prat->_id}}" value="" {{if $_prat->_id|in_array:$prats_selected}}checked="checked"{{/if}} onchange="updatePratDisplayed();" />
              {{$_prat}}
            </label>
          {{/foreach}}
        </div>
      </td>
    </tr>
  {{/if}}
    <tr>
      <td>
        <table class="form">
          {{if $mode_vue == "horizontal"}}
            {{foreach from=$praticiens item=_praticien key=prat_id}}
              <tr>
                <th class="title">{{$_praticien}}</th>
                {{assign var=listPlage value=$listPlages.$prat_id.plages}}
                {{mb_include module=cabinet template=inc_list_consult_horizontal}}
              </tr>
            {{/foreach}}

          {{else}}
            <tr>
            {{foreach from=$praticiens item=_praticien}}
              <th class="title">
                {{$_praticien}}
              </th>
            {{/foreach}}
            </tr>

             <!-- Affichage de la liste des consultations -->
             <tr>
             {{foreach from=$listPlages item=curr_day}}
               <td style="width: 200px; vertical-align: top;">
                 {{assign var="listPlage" value=$curr_day.plages}}
                 {{assign var="tab" value=""}}
                 {{assign var="vue" value="0"}}
                 {{assign var="userSel" value=$curr_day.prat}}
                 {{mb_include module=cabinet template=inc_list_consult}}
               </td>
             {{/foreach}}
           </tr>
         {{/if}}
       </table>
     </td>
   </tr>
</table>