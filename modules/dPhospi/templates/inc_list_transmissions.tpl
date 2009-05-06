<script type="text/javascript">

toggleTrans = function(trans_class){
	$('transmissions').select('tr').each(function(element){
	  trans_class ? (element.hasClassName(trans_class) ?  element.show() : element.hide()) : element.show();
	});
}

</script>

<table class="tbl">
  <tr>
    <th colspan="7" class="title">
			{{if !$without_del_form}}
	    <select name="selCible" onchange="toggleTrans(this.value);" style="float: right">
	      <option value="">&mdash; Toutes les cibles</option>
	      {{foreach from=$cibles item=cibles_by_type}}
	        {{foreach from=$cibles_by_type item=_cible}}
	          <option value="{{$_cible}}">{{$_cible|capitalize}}</option>
	        {{/foreach}}
	      {{/foreach}}
	    </select>
	    {{/if}}
	    Observations et Transmissions
    </th>
  </tr>
  <tr>
    <th>{{tr}}Type{{/tr}}</th>
    <th>{{tr}}User{{/tr}}</th>
    <th>{{tr}}Date{{/tr}}</th>
    <th>{{tr}}Hour{{/tr}}</th>
    <th>{{mb_title class=CTransmissionMedicale field=object_class}}</th>
    <th>{{mb_title class=CTransmissionMedicale field=text}}</th>
    <th />
  </tr>  
  <tbody {{if !$without_del_form}}id="transmissions"{{/if}}>
  {{foreach from=$sejour->_ref_suivi_medical item=_suivi}}
 	  {{mb_include module=dPhospi template=inc_line_suivi _suivi=$_suivi show_patient=false nodebug=true}}
  {{foreachelse}}
  </tbody>
    <tr>
      <td colspan="7">{{tr}}CTransmissionMedicale.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>