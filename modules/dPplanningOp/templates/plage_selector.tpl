<script type="text/javascript">

function showProgramme(plage_id) {
  var url = new Url("dPplanningOp", "ajax_prog_plageop");
  url.addParam("plageop_id", plage_id);
  url.addParam("chir_id", {{$chir}});
  url.requestUpdate("prog_plageop");
}

function setMinVouluPlage() {
  oForm= getForm("plageSelectorFrm");
  if(oForm._hour_voulu.value && !oForm._min_voulu.value) {
    //oForm._min_voulu.value = "00";
    $V(oForm._min_voulu, "00");
  } else if(!oForm._hour_voulu.value) {
    //oForm._min_voulu.value = "";
    $V(oForm._min_voulu, "");
  }
}

function setClose(date, salle_id) {
  var oForm = getForm("plageSelectorFrm");
  
  var list = oForm.list;
  if(date == '') {
    date = $V(oForm._date);
  }
  
  if(salle_id == '') {
    salle_id = $V(oForm._salle_id);
  }
  
  var place_after_interv_id = $V(oForm._place_after_interv_id);
  var horaire_voulu         = $V(oForm._horaire_voulu);
  
  var plage_id = $V(list);
  if (!plage_id) {
    alert('Vous n\'avez pas selectionné de plage ou la plage selectionnée n\'est plus disponible à la planification.\n\nPour plus d\'information, veuillez contacter le responsable du bloc');
    return;
  }
   
  var adm = $V(oForm.admission);
  var typeHospi = "ambu";
  var hour_entree = $V(oForm.hour_jour);
  var min_entree  = $V(oForm.min_jour);
  
  // passage en hospi complete si admission == veille
  if(adm == "veille"){
    typeHospi = "comp";
    hour_entree = $V(oForm.hour_veille);
    min_entree  = $V(oForm.min_veille);
  }
  
  window.parent.PlageOpSelector.set(plage_id, salle_id, date, adm, typeHospi, hour_entree, min_entree, place_after_interv_id, horaire_voulu);
  window._close();
}  

Main.add(function () {
  var oFormSejour = window.parent.document.editSejour;
  var form = getForm("plageSelectorFrm");   
  $V(form.admission, "aucune");
  if (!oFormSejour.sejour_id.value) {
    $V(form.admission, ["ambu", "exte"].include(oFormSejour.type.value) ? "jour" : "veille");
  }
  Control.Tabs.create('main_tab_group', true);
});
</script>

<table class="main tbl">
  <tr>
    <th class="title" style="font-size: 1.5em;">
      {{assign var=prev    value=-1}}
      {{assign var=next    value=1}}
      {{assign var=current value=0}}
      
      <form action="?" name="chgDate" method="get">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="a" value="plage_selector" />
        <input type="hidden" name="dialog" value="1" />
        <input type="hidden" name="curr_op_hour" value="{{$curr_op_hour}}" />
        <input type="hidden" name="curr_op_min" value="{{$curr_op_min}}" />
        <input type="hidden" name="chir" value="{{$chir}}" />
        <input type="hidden" name="group_id" value="{{$group_id}}" />
        <input type="hidden" name="operation_id" value="{{$operation_id}}" />
        
        <button type="button" class="left notext" onclick="$V(this.form.date_plagesel, '{{$listMonthes.$prev.date}}')">&lt; &lt;</button>

        <select name="date_plagesel" onchange="this.form.submit()">
        {{foreach from=$listMonthes key=curr_key_month item=curr_month}}
          <option value="{{$curr_month.date}}" {{if $curr_key_month == $current}}selected="selected"{{/if}}>
            {{$curr_month.month}}
          </option>  
        {{/foreach}}
        </select>
        
        <button type="button" class="right notext" onclick="$V(this.form.date_plagesel, '{{$listMonthes.$next.date}}')">&lt; &lt;</button>
      </form>
    </th>
  </tr>
</table>

<form action="?" name="plageSelectorFrm" method="get">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="plage_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="_salle_id" value="" />
<input type="hidden" name="_date" value="" />

<table class="main layout">  
  <tr>
    <td class="halfPane">
      {{if $listPlages|@count > 1}}
      <div class="small-warning">
        Plusieurs blocs sont disponibles, veillez à bien choisir le bloc souhaité
      </div>
      {{elseif $listPlages|@count == 0}}
      <div class="small-info">
        Vous n'avez pas de plage ce mois-ci, vous pouvez contacter le responsable de bloc pour ajouter une vacation
      </div>
      {{/if}}
      
      <ul id="main_tab_group" class="control_tabs">
        {{foreach from=$listPlages key=_key_bloc item=_blocplage}}
        {{assign var=_bloc value=$blocs.$_key_bloc}}
        <li><a href="#bloc-{{$_bloc->_id}}">{{$_bloc->_view}} ({{$_blocplage|@count}})</a></li>
        {{/foreach}}
      </ul>
      <hr class="control_tabs" />
      
      {{foreach from=$listPlages key=_key_bloc item=_blocplage}}
      {{assign var=_bloc value=$blocs.$_key_bloc}}
      {{assign var=date_min value=$_bloc->_date_min}}
      <table class="tbl" id="bloc-{{$_bloc->_id}}" style="display:none">
        <tr>
          <th class="category" colspan="4">
            Choisir une date
          </th>
        </tr>
        {{foreach from=$_blocplage item=_plage}}
        <tr>
          <td class="narrow">
            {{mb_include module=system template=inc_object_notes object=$_plage}}
          </td>
          <td class="narrow">
            {{if $_plage->spec_id}}
            <img src="images/icons/user-function.png" />
            {{else}}
            <img src="images/icons/user.png" />
            {{/if}}
          </td>
          <td>
            {{assign var="pct" value=$_plage->_fill_rate}}
            {{if $pct > 100}}
              {{assign var="pct" value=100}}
            {{/if}}
            {{if $pct < 100}}
              {{assign var="backgroundClass" value="normal"}}
            {{elseif $pct == 100}}
              {{assign var="backgroundClass" value="booked"}}
            {{else}}
              {{assign var="backgroundClass" value="full"}}
            {{/if}}
            
            <label for="list_{{$_plage->_id}}"
              {{if $resp_bloc || $_plage->_verrouillee|@count == 0}}
                ondblclick="setClose('{{$_plage->date}}', '{{$_plage->salle_id}}')"
              {{else}}
                onclick="showProgramme({{$_plage->_id}})"
              {{/if}}>
              <div class="progressBar" style="width: 98%;{{if $_plage->spec_id}}height: 25px;{{/if}}">
                <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
                <div class="text" style="text-align: left">
                    {{$_plage->date|date_format:"%a %d"}} -
                    {{$_plage->debut|date_format:$conf.time}} -
                    {{$_plage->fin|date_format:$conf.time}}
                    &mdash; {{$_plage->_ref_salle->_view}}
                    {{if $_plage->spec_id}}
                    <br />{{$_plage->_ref_spec->_view|truncate:50}}
                    {{/if}}
                </div>
              </div>
            </label>
          </td>
          <td class="narrow">
            {{if $resp_bloc || $_plage->_verrouillee|@count == 0}}
              <input type="radio" name="list" value="{{$_plage->plageop_id}}"
                 ondblclick="setClose('{{$_plage->date}}', '{{$_plage->salle_id}}')"
                 onclick="showProgramme({{$_plage->_id}}); getForm('plageSelectorFrm')._date.value='{{$_plage->date}}'; getForm('plageSelectorFrm')._salle_id.value='{{$_plage->salle_id}}';"/>
            {{/if}}
            {{if $_plage->_verrouillee|@count > 0}}
              <img src="style/mediboard/images/icons/lock.png" onmouseover="ObjectTooltip.createDOM(this, 'verrou_{{$_plage->_guid}}')"/>
              <div style="display: none;" id="verrou_{{$_plage->_guid}}">
                Impossible {{if $resp_bloc}}pour le personnel{{/if}} de planifier à cette date :
                <ul>
                  {{foreach from=$_plage->_verrouillee item=_raison name=foreach_verrou}}
                    <li>
                      {{tr}}CPlageOp._verrouillee.{{$_raison}}{{/tr}}
                    </li>
                  {{/foreach}}
                </ul>
              </div>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
      </table>
      {{/foreach}}
      
      <table class="tbl">
        <tr>
          <th class="category" colspan="3">
            Légende
          </th>
        </tr>
        <tr>
          <td class="narrow button">
            <img src="images/icons/user.png" />
          </td>
          <td colspan="2">plage personnelle</td>
        </tr>
        <tr>
          <td class="button">
            <img src="images/icons/user-function.png" />
          </td>
          <td colspan="2">plage de spécialité</td>
        </tr>
        <tr>
          <td>
            <div class="progressBar">
              <div class="bar full"></div>
            </div>
          </td>
          <td colspan="2">plage pleine</td>
        </tr>
        <tr>
          <td>
            <div class="progressBar">
              <div class="bar booked"></div>
            </div>
          </td>
          <td colspan="2">plage presque pleine</td>
        </tr>
        <tr>
          <td>
            <div class="progressBar">
              <div class="bar normal" style="width: 60%;"></div>
            </div>
          </td>
          <td colspan="2">taux de remplissage</td>
        </tr>
        <tr>
          <td class="button">
            <div class="rank">1</div>
          </td>
          <td colspan="2">intervention validée par le bloc</td>
        </tr>
        <tr>
          <td class="button">
            <div class="rank desired" title="Pas encore validé par le bloc">2</div>
          </td>
          <td colspan="2">intervention ayant un ordre de passage souhaité</td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="2" class="category">
            Admission du patient
          </th>
        </tr>
        <tr>
          <td class="narrow">
            <input type="radio" name="admission" value="veille" />
            <label for="admission_veille">La veille à</label>
          </td>
          <td class="greedyPane">
            <select name="hour_veille">
              {{foreach from=$hours|smarty:nodefaults item=hour}}
              <option value="{{$hour}}" {{if $heure_entree_veille == $hour}}selected="selected"{{/if}}>{{$hour}}</option>
              {{/foreach}}
            </select>
            h
            <select name="min_veille">
              {{foreach from=$mins|smarty:nodefaults item=min}}
              <option value="{{$min}}">{{$min}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="admission" value="jour" />
            <label for="admission_jour">Le jour même à</label>
          </td>
          <td>
            <select name="hour_jour">
              {{foreach from=$hours|smarty:nodefaults item=hour}}
              <option value="{{$hour}}" {{if $heure_entree_jour == $hour}}selected="selected"{{/if}}>{{$hour}}</option>
              {{/foreach}}
            </select>
            h
            <select name="min_jour">
              {{foreach from=$mins|smarty:nodefaults item=min}}
              <option value="{{$min}}">{{$min}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="radio" name="admission" value="aucune" />
            <label for="admission_aucune">Ne pas modifier</label>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="text">
            <div class="small-info">
              Le choix de l'heure de passage est remplacé par les flèches dans le programme ci-dessous.<br />
              Afin de placer une intervention, cliquez sur la flèche correspondante.
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2" id="prog_plageop"></td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="cancel" type="button" onclick="window._close()">{{tr}}Cancel{{/tr}}</button>
            <button class="tick" type="button" onclick="setClose('', '')">{{tr}}OK{{/tr}}</button>          
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>