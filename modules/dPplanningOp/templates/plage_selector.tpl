<script type="text/javascript">

function setClose(date, salle_id) {
  var form = document.frmSelector;
  
  var list = form.list;
  if(date == '') {
    date = form._date.value;
  }
  if(salle_id == '') {
    salle_id = form._salle_id.value;
  }
  
  var plage_id = $V(list);
  if (!plage_id) {
    alert('choisissez une plage non pleine');
  	return;
  }
   
  var adm = $V(form.admission);
  var typeHospi = "ambu";
  var hour_entree = form.hour_jour.value;
  var min_entree  = form.min_jour.value;
  // passage en hospi complete si admission == veille
  if(adm == "veille"){
    typeHospi = "comp";
    hour_entree = form.hour_veille.value;
    min_entree  = form.min_veille.value;
  }
    
  window.opener.PlageOpSelector.set(plage_id, salle_id, date, adm, typeHospi, hour_entree, min_entree);  
  window.close();
}  

Main.add(function () {
  var oFormSejour = window.opener.document.editSejour;
  var form = document.frmSelector;   
  $V(form.admission, "aucune");
  if (!oFormSejour.sejour_id.value) {
    $V(form.admission, ["ambu", "exte"].include(oFormSejour.type.value) ? "jour" : "veille");
  }
});
</script>

<table class="main">
  <tr>
    <th class="category">
      {{assign var=prev    value=-1}}
      {{assign var=next    value=1}}
      {{assign var=current value=0}}
      <a style="float:left;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date={{$listMonthes.$prev.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&lt; &lt;</a>
      <a style="float:right;" href="?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;date={{$listMonthes.$next.date}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&gt; &gt;</a>
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
        <select name="date" onchange="this.form.submit()">
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

<form action="?" name="frmSelector" method="get">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="plage_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="_salle_id" value="" />
<input type="hidden" name="_date" value="" />

<table class="main">  
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
              <div class="text" style="text-align: left">
                <label 
                  for="list_{{$curr_plage->_id}}"
                  onmouseover="ObjectTooltip.createDOM(this, 'plage-{{$curr_plage->_id}}')" 
                  {{if !$over}}ondblclick="setClose('{{$curr_plage->date}}', '{{$curr_plage->salle_id}}')"{{/if}}
                >
                  {{$curr_plage->date|date_format:"%a %d"}} -
                  {{$curr_plage->debut|date_format:"%Hh%M"}} -
                  {{$curr_plage->fin|date_format:"%Hh%M"}} 
                  &mdash; {{$curr_plage->_ref_salle->_view}} 
                </label>
              </div>
            </div>
            <div id="plage-{{$curr_plage->_id}}" style="display: none; width: 200px;">
              <table class="tbl">
              	<tr>
              		<th class="category">
              			Plage de {{$curr_plage->debut|date_format:'%Hh%M'}}-{{$curr_plage->fin|date_format:'%Hh%M'}}
              		</th>
              	</tr>
                {{foreach from=$curr_plage->_ref_operations item=curr_op}}
                <tr>
                  <td class="text">
                    {{if $curr_op->libelle}}
                      <em>{{$curr_op->libelle}}</em>
                    {{else}}
                      {{$curr_op->_text_codes_ccam}}
                    {{/if}}
                  </td>
                </tr>
                {{foreachelse}}
                <tr>
                  <td>Aucune intervention</td>
                </tr>
                {{/foreach}}
              </table>
            </div>
          </td>
          <td style="width: 1%;">
            {{if !$over || !$dPconfig.dPbloc.CPlageOp.locked}}
            {{if (($curr_plage->_nb_operations < $curr_plage->max_intervention) || ($curr_plage->max_intervention == 0) || ($curr_plage->max_intervention == ""))}}
            <input type="radio" name="list" value="{{$curr_plage->plageop_id}}"
               ondblclick="setClose('{{$curr_plage->date}}', '{{$curr_plage->salle_id}}')"
               onclick="document.frmSelector._date.value='{{$curr_plage->date}}'; document.frmSelector._salle_id.value='{{$curr_plage->salle_id}}';"/>
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
            <input type="radio" name="admission" value="veille" />
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
            <input type="radio" name="admission" value="aucune" />
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
            <button class="cancel" type="button" onclick="window.close()">{{tr}}Cancel{{/tr}}</button>
            <button class="tick" type="button" onclick="setClose('', '')">{{tr}}Select{{/tr}}</button>          
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>