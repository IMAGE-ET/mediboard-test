<script type="text/javascript">

function checkSelect(){
	var oForm = getForm("caligs");
	var inputs = $(oForm).select("input");
  var valigs = 0;
	
	// Parcours des champs
	for(var i=0 ; i<inputs.length ; i++){
		if(inputs[i].checked==true && inputs[i].id.indexOf('-na') == -1){
      valigs += parseInt(inputs[i].value);
		}
	} 
	$V(oForm.scoreIGS, valigs);
}

// Lancement du reload
window.opener.ExamDialog.reload('{{$exam_igs->consultation_id}}');

Main.add(checkSelect);

function empty_on_click(elem) {
	elem.up().next().childElements().each(function(item){
		item.checked=false;
	});
  $(elem.name + '-na').checked = 'true';
  checkSelect();      
}

</script>

<form name="caligs" method="post" action="?m=dPcabinet&amp;a=exam_igs&amp;dialog=1">
  <input type="hidden" name="dosql" value="do_exam_igs_aed" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$exam_igs}}
  {{mb_field object=$exam_igs field="consultation_id" hidden=1}}

  <table class="form">
  	{{mb_include module=system template=inc_form_table_header object=$exam_igs}}

		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="age"}}
		    {{mb_value object=$exam_igs field="age"}}
		    <button type='button' class='cancel notext' name='age' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "age-na" style="display: none;" name="age" value = ''/>
		    {{mb_field object=$exam_igs field="age" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="FC"}}
		    <button type='button' class='cancel notext' name='FC' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "FC-na" style="display: none;" name="FC" value = ''/>
		    {{mb_field object=$exam_igs field="FC" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="TA"}}
		    <button type='button' class='cancel notext' name='TA' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "TA-na" style="display: none;" name="TA" value = ''/>
		    {{mb_field object=$exam_igs field="TA" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="temperature"}}
		    <button type='button' class='cancel notext' name='temperature' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "temperature-na" style="display: none;" name="temperature" value = ''/>
		    {{mb_field object=$exam_igs field="temperature" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="PAO2_FIO2"}}
		    <button type='button' class='cancel notext' name='PAO2_FIO2' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "PAO2_FIO2-na" style="display: none;" name="PAO2_FIO2" value = ''/>
		    {{mb_field object=$exam_igs field="PAO2_FIO2" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="diurese"}}
		    <button type='button' class='cancel notext' name='diurese' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "diurese-na" style="display: none;" name="diurese" value = ''/>
		    {{mb_field object=$exam_igs field="diurese" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="uree"}}
		    <button type='button' class='cancel notext' name='uree' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "uree-na" style="display: none;" name="uree" value = ''/>
		    {{mb_field object=$exam_igs field="uree" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="globules_blancs"}}
		    <button type='button' class='cancel notext' name='globules_blancs' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "globules_blancs-na" style="display: none;" name="globules_blancs" value = ''/>
		    {{mb_field object=$exam_igs field="globules_blancs" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="kaliemie"}}
		    <button type='button' class='cancel notext' name='kaliemie' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "kaliemie-na" style="display: none;" name="kaliemie" value = ''/>
		    {{mb_field object=$exam_igs field="kaliemie" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="natremie"}}
		    <button type='button' class='cancel notext' name='natremie' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "natremie-na" style="display: none;" name="natremie" value = ''/>
		    {{mb_field object=$exam_igs field="natremie" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="HCO3"}}
		    <button type='button' class='cancel notext' name='HCO3' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "HCO3-na" style="display: none;" name="HCO3" value = ''/>
		    {{mb_field object=$exam_igs field="HCO3" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="billirubine"}}
		    <button type='button' class='cancel notext' name='billirubine' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "billirubine-na" style="display: none;" name="billirubine" value = ''/>
		    {{mb_field object=$exam_igs field="billirubine" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="glascow"}}
		    <button type='button' class='cancel notext' name='glascow' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "glascow-na" style="display: none;" name="glascow" value = ''/>
		    {{mb_field object=$exam_igs field="glascow" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="maladies_chroniques"}}
		    <button type='button' class='cancel notext' name='maladies_chroniques' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "maladies_chroniques-na" style="display: none;" name="maladies_chroniques" value = ''/>
		    {{mb_field object=$exam_igs field="maladies_chroniques" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="admission"}}
		    <button type='button' class='cancel notext' name='admission' onclick='empty_on_click(this)'></button>
		  </th>
		  <td>
		    <input type="radio" id = "admission-na" style="display: none;" name="admission" value = ''/>
		    {{mb_field object=$exam_igs field="admission" typeEnum="radio" onclick="checkSelect();"}}
		  </td>
		</tr>
		<tr>
		  <th>
		    {{mb_label object=$exam_igs field="scoreIGS"}}
		  </th>
		  <td colspan="6" class="button">
		    {{mb_field object=$exam_igs field="scoreIGS" readonly="readonly"}}
	      {{if $exam_igs->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button type="button" class="trash" onclick="confirmDeletion(this.form, {typeName:'cet examen IGS'})">
          	{{tr}}Delete{{/tr}}
					</button>
        {{else}}
          <button type="submit" class="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
       </td>    
		</tr>
	</table>
</form>