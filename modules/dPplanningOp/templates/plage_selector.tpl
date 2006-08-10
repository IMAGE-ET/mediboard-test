<script type="text/javascript">
function setClose(date) {
  var form = document.frmSelector;
  var list = form.list;
  if(date == '') {
    date = form.fmtdate.value;
  }
  var key = list.options[list.selectedIndex].value;
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
  
  window.opener.setPlage(key,val,adm);
  window.close();
}
</script>

<form action="index.php" target="_self" name="frmSelector" method="get">
<input type="hidden" name="m" value="dPplanningOp" />
<input type="hidden" name="a" value="plage_selector" />
<input type="hidden" name="dialog" value="1" />
<input type="hidden" name="fmtdate" value="" />

<table class="form">

  <tr>
    <th class="category" colspan="2">
      <a style="float:left;" href="index.php?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;month={{$pmonth}}&amp;year={{$pyear}}">&lt; &lt;</a>
      <a style="float:right;" href="index.php?m=dPplanningOp&amp;a=plage_selector&amp;dialog=1&amp;curr_op_hour={{$curr_op_hour}}&amp;curr_op_min={{$curr_op_min}}&amp;chir={{$chir}}&amp;month={{$nmonth}}&amp;year={{$nyear}}">&gt; &gt;</a>
      <div>{{$nameMonth}} {{$year}}</div>
    </th>
  </tr>

  <tr>
    <td rowspan="3">
      <select name="list"  size="14">
        <option value="0" selected="selected">&mdash; Choisir une date &mdash;</option>
        {{foreach from=$list item=curr_plage}}
        {{if $curr_plage.free_time < 0}}
          <option value="0" ondblclick="setClose('{{$curr_plage.date|date_format:"%d/%m/%Y"}}')"
          onclick="document.frmSelector.fmtdate.value='{{$curr_plage.date|date_format:"%d/%m/%Y"}}'"
        {{else}}
          <option value="{{$curr_plage.plageop_id}}" ondblclick="setClose('{{$curr_plage.date|date_format:"%d/%m/%Y"}}')"
          onclick="document.frmSelector.fmtdate.value='{{$curr_plage.date|date_format:"%d/%m/%Y"}}'"
        {{/if}}
        {{if $curr_plage.spec_id}}
          style="background:#aae"
        {{elseif $curr_plage.free_time < 0}}
          style="background:#eaa"
        {{else}}
          style="background:transparent"
        {{/if}}>
        {{$curr_plage.date|date_format:"%a %d %b %Y"}} - {{$curr_plage.nom}}
        </option>
        {{/foreach}}
      </select>
    </td>
    <td>
      <strong>Admission du patient</strong>
      <br />
      <input type="radio" name="admission" value="veille"{{if !$oper_id}} checked="checked"{{/if}} />
      <label for="admission_veille">La veille</label>
      <br />
      <input type="radio" name="admission" value="jour" />
      <label for="admission_jour">Le jour même</label>
      <br />
      <input type="radio" name="admission" value="aucune"{{if $oper_id}} checked="checked"{{/if}} />
      <label for="admission_jour">Ne pas modifier</label>
      <br />
    </td>
  </tr>

  <tr>
    <td class="text"><i>Légende :</i>
      <table class="form">
        <tr>
          <td style="background-color:#eaa; width:10px;"></td>
          <td>plages pleines</td>
        </tr>
        <tr>
          <td style="background-color:#aae; width:10px;"></td>
          <td>plages de spécialité</td>
        </tr>
        <tr>
          <td class="text" colspan="2">
            <i>Par défaut, une admission la veille se fait à 17h et à 8h pour le jour même</i>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td class="button" colspan="2">
      <button class="cancel" type="button" onclick="window.close()">annuler</button>
      <button class="tick" type="button" onclick="setClose('')">Sélectionner</button>
    </td>
  </tr>

</table>

</form>
