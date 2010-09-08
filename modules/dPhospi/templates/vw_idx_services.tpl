<table class="main">
	
<tr>
  <td class="halfPane">
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;service_id=0" class="button new">
      {{tr}}CService-title-create{{/tr}}
    </a>
    
    <!-- Liste des services -->
    <table class="tbl">
	    <tr>
	      <th colspan="3" class="title">
	        {{tr}}CService.all{{/tr}}
	      </th>
	    </tr>
	    <tr>
	      <th>{{mb_title class=CService field=nom}}</th>
	      <th>{{mb_title class=CService field=description}}</th>
	      <th>{{mb_label class=CService field=group_id}}</th>
	    </tr>
	
			{{foreach from=$services item=_service}}
	    <tr {{if $_service->_id == $service->_id}} class="selected" {{/if}}>
	      <td>
	      	<a href="?m={{$m}}&amp;tab={{$tab}}&amp;service_id={{$_service->_id}}">
	          {{mb_value object=$_service field=nom}}
					</a>
				</td>
	      <td class="text">{{mb_value object=$_service field=description}}</td>
	      <td>{{mb_value object=$_service field=group_id}}</td>
	    </tr>
	    {{/foreach}}
    </table>
  </td> 

  <td class="halfPane">
  	<!-- Formulaire d'un service -->
    <form name="Edit-CService" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_service_aed" />
    <input type="hidden" name="del" value="0" />
		{{mb_key object=$service}}

    <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$service}}	
			
    <tr>
      <th>{{mb_label object=$service field=group_id}}</th>
      <td>{{mb_field object=$service field=group_id options=$etablissements}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$service field=nom}}</th>
      <td>{{mb_field object=$service field=nom}}</td>
    </tr>       

    <tr>
      <th>{{mb_label object=$service field=responsable_id}}</th>
      <td>
        <select name="responsable_id">
          <option value="">&mdash; {{tr}}None{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$praticiens selected=$service->responsable_id}}
        </select>
			</td>
    </tr>

    <tr>
      <th>{{mb_label object=$service field=type_sejour}}</th>
      <td>{{mb_field object=$service field=type_sejour}}</td>
    </tr> 
    
    <tr>
      <th>{{mb_label object=$service field=urgence}}</th>
      <td>{{mb_field object=$service field=urgence}}</td>
    </tr> 
    
    <tr>
      <th>{{mb_label object=$service field=uhcd}}</th>
      <td>{{mb_field object=$service field=uhcd}}</td>
    </tr>    

    <tr>
      <th>{{mb_label object=$service field=hospit_jour}}</th>
      <td>{{mb_field object=$service field=hospit_jour}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$service field=cancelled}}</th>
      <td>{{mb_field object=$service field=cancelled}}</td>
    </tr>
		    
    <tr>
      <th>{{mb_label object=$service field=description}}</th>
      <td>{{mb_field object=$service field=description}}</td>
    </tr>    

    <tr>
      <td class="button" colspan="2">
        {{if $service->_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le service ',objName: $V(this.form.nom)})">
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
