<!-- Formulaire d'un service -->
<form name="edit{{$service->_guid}}" action="" method="post" onsubmit="return submit_Ajax(this, 'services')">
  <input type="hidden" name="m" value="hospi" />
  <input type="hidden" name="dosql" value="do_service_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$service}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header_uf object=$service tag=$tag_service}}
    
    <tr>
      <th>{{mb_label object=$service field=group_id}}</th>
      <td>{{mb_field object=$service field=group_id options=$etablissements}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$service field=nom}}</th>
      <td>{{mb_field object=$service field=nom}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$service field=cancelled}}</th>
      <td>{{mb_field object=$service field=cancelled}}</td>
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
      <th>{{mb_label object=$service field=externe}}</th>
      <td>{{mb_field object=$service field=externe}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$service field=neonatalogie}}</th>
      <td>{{mb_field object=$service field=neonatalogie}}</td>
    </tr>
        
    <tr>
      <th>{{mb_label object=$service field=description}}</th>
      <td>{{mb_field object=$service field=description}}</td>
    </tr>    
  
    <tr>
      <td class="button" colspan="2">
        {{if $service->_id}}
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
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