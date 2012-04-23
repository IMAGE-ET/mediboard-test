<script type="text/javascript">

function showProgramme(plage_id) {
  var url = new Url("dPplanningOp", "ajax_prog_plageop");
  url.addParam("plageop_id", plage_id);
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
  var oForm = getForm("plageSelectorFrm");;
  
  var list = oForm.list;
  if(date == '') {
    date = $V(oForm._date);
  }
  if(salle_id == '') {
    salle_id = $V(oForm._salle_id);
  }
  var hour_voulu = $V(oForm._hour_voulu);
  var min_voulu  = $V(oForm._min_voulu);
  
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
  
  window.parent.PlageOpSelector.set(plage_id, salle_id, date, adm, typeHospi, hour_entree, min_entree, hour_voulu, min_voulu);
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

<table class="main">
  <tr>
    <th class="title" style="font-size: 16px;">
      {{assign var=prev    value=-1}}
      {{assign var=next    value=1}}
      {{assign var=current value=0}}
      <a style="float:left;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date_plagesel={{$listMonthes.$prev.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&lt; &lt;</a>
      <a style="float:right;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date_plagesel={{$listMonthes.$next.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&gt; &gt;</a>
      <div>
        <form action="?" name="chgDate" method="get">
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="a" value="plage_selector" />
        <input type="hidden" name="dialog" value="1" />
        <input type="hidden" name="curr_op_hour" value="{{$curr_op_hour}}" />
        <input type="hidden" name="curr_op_min" value="{{$curr_op_min}}" />
        <input type="hidden" name="chir" value="{{$chir}}" />
        <input type="hidden" name="group_id" value="{{$group_id}}" />
        <input type="hidden" name="operation_id" value="{{$operation_id}}" />
        <select name="date_plagesel" onchange="this.form.submit()">
        {{foreach from=$listMonthes key=curr_key_month item=curr_month}}
          <option value="{{$curr_month.date}}" {{if $curr_key_month == $current}}selected="selected"{{/if}}>
            {{$curr_month.month}}
          </option>  
        {{/foreach}}
        </select>
        </form>
      </div>
    </th>
  </tr>
</table>

<form action="?" name="plageSelectorFrm" method="get">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="plage_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="_salle_id" value="" />
<input type="hidden" name="_date" value="" />

<table class="main">  
  <tr>
    <td class="halfPane">
      {{if $listPlages|@count > 1}}
      <div class="small-warning">
        Plusieurs blocs sont disponible, veillez à bien choisir le bloc souhaité
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
      <div id="bloc-{{$_bloc->_id}}" style="display:none">
      <table class="tbl">
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
            <div class="progressBar" style="width: 98%;{{if $_plage->spec_id}}height: 25px;{{/if}}">
              <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
              <div class="text" style="text-align: left">
                <label 
                  for="list_{{$_plage->_id}}"
                  {{if $resp_bloc || $_plage->_verrouillee|@count == 0}}
                    ondblclick="setClose('{{$_plage->date}}', '{{$_plage->salle_id}}')"
                  {{else}}
                    onclick="showProgramme({{$_plage->_id}})"
                  {{/if}}
                >
                  {{$_plage->date|date_format:"%a %d"}} -
                  {{$_plage->debut|date_format:$conf.time}} -
                  {{$_plage->fin|date_format:$conf.time}}
                  &mdash; {{$_plage->_ref_salle->_view}}
                  {{if $_plage->spec_id}}
                  <br />{{$_plage->_ref_spec->_view|truncate:50}}
                  {{/if}}
                </label>
              </div>
            </div>
         	 </td>
          <td style="text-align: center;" class="narrow">
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
      </div>
      {{/foreach}}
      <table class="tbl">
        <tr>
          <th class="category" colspan="3">
            Légende
          </th>
        </tr>
        <tr>
          <td>
            <img src="images/icons/user.png" />
          </td>
          <td colspan="2">plage personnelle</td>
        </tr>
        <tr>
          <td>
            <img src="images/icons/user-function.png" />
          </td>
          <td colspan="2">plage de spécialité</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar full"></div>
            </div>
          </td>
          <td colspan="2">plage pleine</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar booked"></div>
            </div>
          </td>
          <td colspan="2">plage presque pleine</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar normal" style="width: 60%;"></div>
            </div>
          </td>
          <td colspan="2">taux de remplissage</td>
        </tr>
      </table>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="3" class="category">
            Admission du patient
          </th>
        </tr>
        <tr>
          <td>
            <input type="radio" name="admission" value="veille" />
          </td>
          <td>
            <label for="admission_veille">La veille</label> à
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
          </td>
          <td>
            <label for="admission_jour">Le jour même</label> à
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
          <td>
            <input type="radio" name="admission" value="aucune" />
          </td>
          <td colspan="2">
            <label for="admission_aucune">Ne pas modifier</label>
          </td>
        </tr>
        <tr>
          <th colspan="3" class="category">
            Heure de passage souhaitée
          </th>
        </tr>
        <tr>
          <td colspan="3" class="button">
            <select name="_hour_voulu" onchange="setMinVouluPlage();">
              <option value="">-</option>
              {{foreach from=$list_hours_voulu|smarty:nodefaults item=hour}}
                <option value="{{$hour}}">{{$hour}}</option>
              {{/foreach}}
            </select> h
            <select name="_min_voulu">
              <option value="">-</option>
              {{foreach from=$list_minutes_voulu|smarty:nodefaults item=min}}
                <option value="{{$min}}">{{$min}}</option>
              {{/foreach}}
            </select> min
          </td>
        </tr>
        <tr>
          <td colspan="3" class="button">
            <button class="cancel" type="button" onclick="window._close()">{{tr}}Cancel{{/tr}}</button>
            <button class="tick" type="button" onclick="setClose('', '')">{{tr}}OK{{/tr}}</button>          
          </td>
        </tr>
        <tr>
          <td colspan="3">
            <div id="prog_plageop"></div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>