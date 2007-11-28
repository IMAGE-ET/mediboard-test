<script type="text/javascript">

function checkHeureSortieSelector(){
  var oForm = window.opener.document.editSejour;
  var form = document.frmSelector;
  
  // heure d'entree selectionne dans le plage selector
  var heure_entree = parseInt(form.hour_jour.value, 10);
  
  // on compare l'heure d'entree selectionne avec l'heure de sortie
  if (oForm._hour_sortie_prevue.value < heure_entree + 1) {
    heure_entree = heure_entree + 1;
    oForm._hour_sortie_prevue.value = heure_entree;
  }
}

function setClose(date) {
  var form = document.frmSelector;
  
  var list = form.list;
  if(date == '') {
    date = form.fmtdate.value;
  }
  var key = 0;
  key = getCheckedValue(list);
  var val = date;
  
  if (key == 0) {
    alert('choisissez une plage non pleine');
  	return;
  }
  var adm = 0;
  if(form.admission[1].checked) {
    adm = 1;
  } else if(form.admission[2].checked) {
    adm = 2;
  }
  
  var typeHospi = "ambu";
  var hour_entree = form.hour_jour.value;
  var min_entree  = form.min_jour.value;
  // passage en hospi complete si admission == veille
  if(getCheckedValue(form.admission) == "veille"){
    typeHospi = "comp";
    hour_entree = form.hour_veille.value;
    min_entree  = form.min_veille.value;
  }
    
  window.opener.PlageOpSelector.set(key,val,adm,typeHospi, hour_entree, min_entree);
  
  if(getCheckedValue(form.admission) == "jour"){
    checkHeureSortieSelector();
  }
  window.close();
}  

function pageMain(){
  var oFormSejour = window.opener.document.editSejour;
  var form = document.frmSelector;   

  if(oFormSejour.type.value == "comp"){
    setCheckedValue(form.admission, "veille");
  } 
  if(oFormSejour.type.value == "ambu"){
    setCheckedValue(form.admission, "jour");
  }
}
</script>

<form action="?" name="frmSelector" method="get">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="plage_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="fmtdate" value="" />

<table class="main">
  <tr>
    <th class="category" colspan="2">
      <a style="float:left;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;month={{$pmonth}}&amp;year={{$pyear}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&lt; &lt;</a>
      <a style="float:right;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;month={{$nmonth}}&amp;year={{$nyear}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&gt; &gt;</a>
      <div>{{$nameMonth}} {{$year}}</div>
    </th>
  </tr>
  
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th class="category" colspan="2">
            Choisir une date
          </th>
        </tr>
        {{foreach from=$listPlages item=curr_plage}}
        <tr>
          <td>
            {{assign var="pct" value=$curr_plage->_fill_rate}}
            {{if $pct > 100}}
              {{assign var="over" value=1}}
              {{assign var="pct" value=100}}
            {{else}}
              {{assign var="over" value=0}}              
            {{/if}}
            
            {{if $curr_plage->spec_id}}
              {{assign var="pct" value="100"}}
              {{assign var="backgroundClass" value="empty"}}
            {{elseif $pct < 100}}
              {{assign var="backgroundClass" value="normal"}}
            {{elseif !$over}}
              {{assign var="backgroundClass" value="booked"}}
            {{else}}
              {{assign var="backgroundClass" value="full"}}
            {{/if}} 
            <div class="progressBar">
              <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
              <div class="text">
                <label 
                  for="list_{{$curr_plage->plageop_id}}" 
                  title="Plage de {{$curr_plage->debut|date_format:'%Hh%M'}}-{{$curr_plage->fin|date_format:'%Hh%M'}}"
                  {{if !$over}}ondblclick="setClose('{{$curr_plage->date|date_format:"%d/%m/%Y"}}')"{{/if}}
                >
                  {{$curr_plage->date|date_format:"%a %d"}} 
                  en {{$curr_plage->_ref_salle->nom}}
                </label>
              </div>
            </div>
          </td>
          <td>
            {{if !$over || !$dPconfig.dPbloc.CPlageOp.locked}}
            {{if (($curr_plage->_nb_operations < $curr_plage->max_intervention) || ($curr_plage->max_intervention == 0))}}
            <input type="radio" name="list" value="{{$curr_plage->plageop_id}}" ondblclick="setClose('{{$curr_plage->date|date_format:"%d/%m/%Y"}}')" onclick="document.frmSelector.fmtdate.value='{{$curr_plage->date|date_format:"%d/%m/%Y"}}'"/>
            {{/if}}
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
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
            <input type="radio" name="admission" value="veille"{{if !$operation_id}} checked="checked"{{/if}}" />
          </td>
          <td>
            <label for="admission_veille">La veille</label> à
          </td>
          <td>
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
            <input type="radio" name="admission" value="aucune"{{if $operation_id}} checked="checked"{{/if}} />
          </td>
          <td colspan="2">
            <label for="admission_aucune">Ne pas modifier</label>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="3">
            Légende
          </th>
        </tr>
        <tr>
          <td>
            <div class="progressBar">
              <div class="bar empty"></div>
            </div>
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
        <tr>
          <td colspan="3" class="button">
            <button class="cancel" type="button" onclick="window.close()">{{tr}}cancel{{/tr}}</button>
            <button class="tick" type="button" onclick="setClose('')">{{tr}}Select{{/tr}}</button>          
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>