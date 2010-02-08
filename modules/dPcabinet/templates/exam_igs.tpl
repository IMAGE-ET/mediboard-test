<script type="text/javascript">

function checkSelect(){
	var oForm = getForm("caligs");
	var inputs = $(oForm).select("input");
  var valigs = 0;
	
	// Parcours des champs
	for(var i=0 ; i<inputs.length ; i++){
		if(inputs[i].checked==true){
      valigs += parseInt(inputs[i].value);
		}
	} 
	$V(oForm.scoreIGS, valigs);
}

// Lancement du reload
window.opener.ExamDialog.reload('{{$exam_igs->consultation_id}}');

Main.add(checkSelect);

</script>

<form name="caligs" method="post" action="?m=dPcabinet&amp;a=exam_igs&amp;dialog=1">
  <input type="hidden" name="dosql" value="do_exam_igs_aed" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  {{mb_field object=$exam_igs field="examigs_id" hidden=1 prop=""}}
  {{mb_field object=$exam_igs field="consultation_id" hidden=1 prop=""}}

  <table class="main tbl" style="table-layout: fixed;">
    <tr>
      <th colspan="7" class="title">Indice de Gravité Simplifié</th>
    </tr>
		<tr>
		  <th style="width: 0.1%;">
		    {{mb_label object=$exam_igs field="age"}}
		    {{mb_value object=$exam_igs field="age"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="age" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="FC"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="FC" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
      <td></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="TA"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="TA" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
      <td colspan="2"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="temperature"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="temperature" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		  <td colspan="4"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="PAO2_FIO2"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="PAO2_FIO2" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
      <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="diurese"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="diurese" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
      <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="uree"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="uree" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
      <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="globules_blancs"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="globules_blancs" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
      <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="kaliemie"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="kaliemie" separator="</td><td>" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
      <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="natremie"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="natremie" separator="</td><td>" typeEnum="radio" onclick="checkSelect(this.id,this.name);"}}
		  </td>
		  <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="HCO3"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="HCO3" separator="</td><td>" typeEnum="radio" onclick="checkSelect(this.id,this.name);"}}
		  </td>
		  <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="billirubine"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="billirubine" separator="</td><td>" typeEnum="radio" onclick="checkSelect(this.id,this.name);"}}
		  </td>
		  <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="glascow"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="glascow" separator="</td><td>" typeEnum="radio" onclick="checkSelect(this.id,this.name);"}}
		  </td>
      <td></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="maladies_chroniques"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="maladies_chroniques" separator="</td><td>" typeEnum="radio" onclick="checkSelect(this.id,this.name);"}}
		  </td>
		  <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="admission"}}
		  </th>
		  <td>
		    {{mb_field object=$exam_igs field="admission" separator="</td><td>" typeEnum="radio" onclick="checkSelect(this.id,this.name);"}}
		  </td>
		  <td colspan="3"></td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="scoreIGS"}}
		  </th>
		  <td colspan="6" style="text-align: center">
		    {{mb_field object=$exam_igs field="scoreIGS" readonly="readonly"}}
	      {{if $exam_igs->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form, {typeName:'cet examen IGS',target:'systemMsg'})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
       </td>    
		</tr>
	</table>
</form>