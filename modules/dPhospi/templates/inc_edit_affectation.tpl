<script>
  bloquerLits = function(oForm){
    for(var i=0; i<100;i++){
      if($("editAffect_chambre"+i) && $("editAffect_chambre"+i).value){
        oForm.lit_id.value = $("editAffect_chambre"+i).value;
        onSubmitFormAjax(oForm);
      }
    }
    Control.Modal.close();
  }
</script>

<form name="editAffect" method="post" action="?" onsubmit="bloquerLits(this);">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="dosql" value="do_affectation_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$affectation}}
  {{mb_field object=$affectation field=lit_id hidden=true}}
  
  <table class="form">
    {{if !$affectation->_id}}
      <tr>
        <th class="category" colspan="4" >Service {{$service->nom}}</th>
      </tr>
      {{assign var="key" value="0"}}
      {{foreach from=$service->_ref_chambres item=chambre}}
        {{foreach from=$chambre->_ref_lits item=lit}}
          {{if $key%4==0}}
           <tr>
          {{/if}}
             <td>
          <input type="checkbox" name="chambre{{$key}}" value="{{if $affectation->lit_id == $lit->_id}}{{$lit->_id}}{{/if}}" {{if $affectation->lit_id == $lit->_id}}checked="checked"{{/if}} onclick="if(this.value){this.value='';}else{this.value='{{$lit->_id}}'};" />
          <label>{{$chambre->nom}} - {{$lit->_view}}</label></td>
          {{if $key%4==3}}
           </tr>
          {{/if}}
          {{assign var="key" value=$key+1}}
        {{/foreach}}
      {{/foreach}}
      {{if $key%4!=3}}
       </tr>
      {{/if}}
    {{/if}}
    <tr>
      <th>
        {{mb_label object=$affectation field=entree}}
      </th>
      <td>
        {{mb_field object=$affectation field=entree form=editAffect register=true}}
      </td>
      <th>
        {{mb_label object=$affectation field=sortie}}
      </th>
      <td>
        {{mb_field object=$affectation field=sortie form=editAffect register=true}}
      </td>
    </tr>
    <tr>
      <td colspan="4" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit();">
          {{if $affectation->_id}}
            {{tr}}Save{{/tr}}
          {{else}}
            {{tr}}Create{{/tr}}
          {{/if}}
        {{if $affectation->_id}}
          <button type="button" class="cancel" onclick="$V(this.form.del, 1); this.form.onsubmit();">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
{{if $affectation->_id}}
  {{mb_include module=hospi template=inc_other_actions}}
{{/if}}