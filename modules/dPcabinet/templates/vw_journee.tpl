<script type="text/javascript">
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
  form.submit();
}

printPlage = function(plage_id) {
  var url = new Url;
  url.setModuleAction("dPcabinet", "print_plages");
  url.addParam("plage_id", plage_id);
  url.popup(700, 550, "Planning");
}

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
    var oForm = getForm("Create-Reconvocation");
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
      <script type="text/javascript">
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
      <table class="form">
        <tr>
          <th class="title" colspan="100">
            Journée de consultation du
            {{$date|date_format:$conf.longdate}}
            <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
          </th>
        </tr>
        
        {{if !$offline}}
          <tr>
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
            <td {{if $mode_urgence}}colspan="5"{{/if}}>
              <input name="_empty" type="checkbox" value="1" onchange="synchronizeView(this.form);" {{if $empty}}checked="checked"{{/if}} />
              <input name="empty" type="hidden" value="{{$empty}}" />
              <label for="_empty" title="Afficher les plages vides">Plages vides</label>
              <input name="_canceled" type="checkbox" value="1" onchange="synchronizeView(this.form);" {{if $canceled}}checked="checked"{{/if}} />
              <input name="canceled" type="hidden" value="{{$canceled}}" />
              <label for="_canceled" title="Afficher les consultations annulées">Annulées</label>
              <input name="_paid" type="checkbox" value="1" onchange="synchronizeView(this.form);" {{if $paid}}checked="checked"{{/if}} />
              <input name="paid" type="hidden" value="{{$paid}}" />
              <label for="_paid" title="Afficher les consultations réglées">Réglées</label>
              <input name="_finished" type="checkbox" value="1" onchange="synchronizeView(this.form);" {{if $finished}}checked="checked"{{/if}} />
              <input name="finished" type="hidden" value="{{$finished}}" />
              <label for="_finished" title="Afficher les consultations terminées">Terminées</label>
              <input name="_immediate" type="checkbox" value="1" onchange="synchronizeView(this.form);" {{if $immediate}}checked="checked"{{/if}} />
              <input name="immediate" type="hidden" value="{{$immediate}}" />
              <label for="_immediate" title="Afficher les consultations immédiates">Immediates</label>
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