<table class="main">
	
<tr>
  <td class="halfPane">
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;prestation_id=0" class="button new">
      {{tr}}CPrestation-title-create{{/tr}}
    </a>
		
    <table class="tbl">
    	
    <tr>
      <th colspan="3">{{tr}}CPrestation.all{{/tr}}</th>
    </tr>
		
    <tr>
      <th>{{mb_title class=CPrestation field=nom}}</th>
      <th>{{mb_title class=CPrestation field=description}}</th>
      <th>{{mb_title class=CPrestation field=group_id}}</th>
    </tr>
		
    {{foreach from=$prestations item=_prestation}}
    <tr {{if $_prestation->_id == $prestation->_id}} class="selected" {{/if}}>
      <td>
      	<a href="?m={{$m}}&amp;tab={{$tab}}&amp;prestation_id={{$_prestation->_id}}">
      		{{mb_value object=$_prestation field=nom}}
			  </a>
			</td>
      <td class="text">{{mb_value object=$_prestation field=description}}</td>
      <td>{{mb_value object=$_prestation field=group_id}}</td>
    </tr>
    {{/foreach}}
    
		</table>

  </td>
	 
  <td class="halfPane">

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_prestation_aed" />
    <input type="hidden" name="del" value="0" />

    {{mb_key object=$prestation}}

    <table class="form">
    	
    <tr>
      {{if $prestation->_id}}
      <th class="title modify" colspan="2">
        {{mb_include module=system template=inc_object_notes      object=$prestation}}
        {{mb_include module=system template=inc_object_idsante400 object=$prestation}}
	      {{mb_include module=system template=inc_object_history    object=$prestation}}
        {{tr}}CPrestation-msg-modify{{/tr}} '{{$prestation}}'
      </th>
      {{else}}
      <th class="title" colspan="2">
        {{tr}}CPrestation-msg-create{{/tr}}
      </th>
      {{/if}}
    </tr>
		
    <tr>
      <th>{{mb_label object=$prestation field=group_id}}</th>
      <td>{{mb_field object=$prestation field=group_id options=$etablissements}}</td>
    </tr>
		
    <tr>
      <th>{{mb_label object=$prestation field=nom}}</th>
      <td>{{mb_field object=$prestation field=nom}}</td>
    </tr>       

    <tr>
      <th>{{mb_label object=$prestation field=description}}</th>
      <td>{{mb_field object=$prestation field=description}}</td>
    </tr>    

    <tr>
      <td class="button" colspan="2">
        {{if $prestation->_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la prestation ',objName:$V(this.form.nom)})">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>

    </table>   

    </form>
  </td>
</tr>

</table>
