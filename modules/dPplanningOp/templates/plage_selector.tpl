<script type="text/javascript">
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
  
  window.opener.PlageSelector.set(key,val,adm);
  window.close();
}
</script>

<form action="index.php" name="frmSelector" method="get">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="plage_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="fmtdate" value="" />

<table class="main">
  <tr>
    <th class="category" colspan="2">
      <a style="float:left;" href="index.php?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;month={{$pmonth}}&amp;year={{$pyear}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&lt; &lt;</a>
      <a style="float:right;" href="index.php?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;month={{$nmonth}}&amp;year={{$nyear}}&amp;group_id={{$group_id}}&amp;operation_id={{$operation_id}}">&gt; &gt;</a>
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
                <label for="list_{{$curr_plage->plageop_id}}" {{if !$over}}ondblclick="setClose('{{$curr_plage->date|date_format:"%d/%m/%Y"}}')"{{/if}}>
                  {{$curr_plage->date|date_format:"%a %d %b %Y"}} - {{$curr_plage->_ref_salle->nom}}
                </label>
              </div>
            </div>
          </td>
          <td>
            {{if !$over}}
            <input type="radio" name="list" value="{{$curr_plage->plageop_id}}" ondblclick="setClose('{{$curr_plage->date|date_format:"%d/%m/%Y"}}')" onclick="document.frmSelector.fmtdate.value='{{$curr_plage->date|date_format:"%d/%m/%Y"}}'"/>
            {{/if}}
          </td>
        </tr>
        {{/foreach}}
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
          <td>
            <input type="radio" name="admission" value="veille"{{if !$operation_id}} checked="checked"{{/if}} />
          </td>
          <td>
            <label for="admission_veille">La veille</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="admission" value="jour" />
          </td>
          <td>
            <label for="admission_jour">Le jour même</label>
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" name="admission" value="aucune"{{if $operation_id}} checked="checked"{{/if}} />
          </td>
          <td>
            <label for="admission_jour">Ne pas modifier</label>
          </td>
        </tr>
        <tr>
          <td class="text" colspan="2">
            <i>Par défaut, une admission la veille se fait à 17h et à 8h pour le jour même</i>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">
            Légende
          </th>
        </tr>
        <tr>
          <td>
            <div class="progressBar">
              <div class="bar empty"></div>
            </div>
          </td>
          <td>plage de spécialité</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar full"></div>
            </div>
          </td>
          <td>plage pleine</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar booked"></div>
            </div>
          </td>
          <td>plage presque pleine</td>
        </tr>
        <tr>
          <td style="width:10px;">
            <div class="progressBar">
              <div class="bar normal" style="width: 60%;"></div>
            </div>
          </td>
          <td>taux de remplissage</td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="cancel" type="button" onclick="window.close()">{{tr}}cancel{{/tr}}</button>
            <button class="tick" type="button" onclick="setClose('')">{{tr}}Select{{/tr}}</button>          
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>