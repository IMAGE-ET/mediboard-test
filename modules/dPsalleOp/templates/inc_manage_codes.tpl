<form name="manageCodes" action="?m={{$module}}" method="post">
  <input type="hidden" name="m" value="{{$subject->_ref_module->mod_name}}" />
  <input type="hidden" name="dosql" value="{{$do_subject_aed}}" />
  <input type="hidden" name="{{$subject->_tbl_key}}" value="{{$subject->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="codes_ccam" value="{{$subject->codes_ccam}}" />
  <input type="submit" disabled="disabled" style="display:none;"/>
  <table class="form">
    
    
    
      <tr>
      <td colspan="2" style="vertical-align:middle;">
        <select name="_selCode">
          <option value="0">&mdash; Choisir</option>
          {{foreach from=$subject->_codes_ccam|smarty:nodefaults item=curr_code}}
          <option value="{{$curr_code}}">{{$curr_code}}</option>
          {{/foreach}}
        </select>
      
      
      {{if $can->edit || $modif_operation}}
    
        <button class="trash" type="button" onclick="delCode({{$subject->_id}})">Supprimer</button>
      </td>
      
      
      <td colspan="2" style="vertical-align:middle;">
        <button class="search" type="button" onclick="popCodeCCAM({{$subject->_praticien_id}})">Rechercher</button>
     
        <input type="text" size="7" name="_newCode" />
        <button class="tick" type="button" onclick="addCode({{$subject->_id}},{{$subject->_praticien_id}})">Ajouter</button>        
      </td>
      

      </tr>
      
  
      {{if ($subject->_class_name=="COperation")}}
      <tr>
     
        <th style="vertical-align:middle; text-align: left">
          <label for="anapath_1" title="Examen anatomie pathologique">Anapath :</label>
        </th>
        <td style="vertical-align:middle;">      
          <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="anapath" value="1" {{if $subject->anapath}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="anapath_1">Oui</label>
          <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="anapath" value="0" {{if !$subject->anapath}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="anapath_0">Non</label>
        </td>
      </td>
     
        <th style="vertical-align:middle; text-align: right">
          <label for="labo_1" title="Analyse biologique">Labo :</label>
        </th>
        <td style="vertical-align:middle;">      
          <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="labo" value="1" {{if $subject->labo}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="labo_1">Oui</label>
          <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="labo" value="0" {{if !$subject->labo}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="labo_0">Non</label>
        </td>
     
      </tr>
      {{/if}}
    {{/if}}
  </table>
</form>

{{if $ajax}}
<script type="text/javascript">

var oForm = document.manageCodes;
prepareForm(oForm);

</script>
{{/if}}