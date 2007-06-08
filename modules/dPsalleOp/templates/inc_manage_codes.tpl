<form name="manageCodes" action="?m={{$module}}" method="post">
  <input type="hidden" name="m" value="{{$subject->_ref_module->mod_name}}" />
  <input type="hidden" name="dosql" value="{{$do_subject_aed}}" />
  <input type="hidden" name="{{$subject->_tbl_key}}" value="{{$subject->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="codes_ccam" value="{{$subject->codes_ccam}}" />
  <input type="submit" disabled="disabled" style="display:none;"/>
  <table class="form">
    <tr>
      <td style="vertical-align:middle;">
        <select name="_selCode">
          <option value="0">&mdash; Codes</option>
          {{foreach from=$subject->_codes_ccam|smarty:nodefaults item=curr_code}}
          <option value="{{$curr_code}}">{{$curr_code}}</option>
          {{/foreach}}
        </select>
      </td>
      <td style="vertical-align:middle; text-align:right;">
        Ajouter un code
        <input type="text" size="7" name="_newCode" />
           
        <button class="tick" type="button" onclick="addCode({{$subject->_id}})">Ajouter</button>
        
      </td>
      {{if ($module!="dPcabinet") && ($module!="dPhospi")}}
      <th style="vertical-align:middle;">
        <label for="anapath_1" title="Examen anatomie pathologique">Anapath :</label>
      </th>
      <td style="vertical-align:middle;">      
        <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="anapath" value="1" {{if $subject->anapath}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="anapath_1">Oui</label>
        <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="anapath" value="0" {{if !$subject->anapath}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="anapath_0">Non</label>
      </td>
      {{/if}}
    </tr>
    <tr>
      <td style="vertical-align: middle;">
        <button class="trash" type="button" onclick="delCode({{$subject->_id}})">Supprimer</button>
        <div id="codename" style="vertical-align:middle; display:inline; white-space:normal">Selectionnez un code</div>
      </td>
      <td style="vertical-align:middle; text-align:right;">
        <button class="search" type="button" onclick="popCodeCCAM({{$chir_id}})">Rechercher</button>
      </td>
      {{if ($module!="dPcabinet") && ($module!="dPhospi")}}
      <th style="vertical-align:middle;">
        <label for="labo_1" title="Analyse biologique">Labo :</label>
      </th>
      <td style="vertical-align:middle;">      
        <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="labo" value="1" {{if $subject->labo}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="labo_1">Oui</label>
        <input type="radio" onChange="submitFormAjax(this.form, 'systemMsg');" name="labo" value="0" {{if !$subject->labo}}checked="checked"{{/if}} style="vertical-align:middle;" /><label for="labo_0">Non</label>
      </td>
      {{/if}}
    </tr>
  </table>
</form>