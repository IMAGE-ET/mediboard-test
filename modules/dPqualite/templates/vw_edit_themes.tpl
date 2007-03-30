<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
    
      <form name="FrmTypeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="typeVue">{{tr}}_classification{{/tr}}</label>
      <select name="typeVue" onchange="this.form.submit();">
        <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>{{tr}}_CChapitreDoc_classification_chap{{/tr}}</option>
        <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>{{tr}}_CThemeDoc_classification_theme{{/tr}}</option>
      </select>
      </form><br />
    
      <a class="buttonnew" href="index.php?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_theme_id=0">
        {{tr}}CThemeDoc.create{{/tr}}
      </a>
      <table class="tbl">
        <tr>
          <th>{{tr}}CThemeDoc-nom-court{{/tr}}</th>
        </tr>
        {{foreach from=$listThemes item=curr_theme}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_theme_id={{$curr_theme->doc_theme_id}}" title="{{tr}}CThemeDoc.modify{{/tr}}">
              {{$curr_theme->nom}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
    <td class="halfPane">
      <form name="editThème" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_theme_aed" />
	  <input type="hidden" name="doc_theme_id" value="{{$theme->doc_theme_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $theme->doc_theme_id}}
          <th class="title modify" colspan="2">{{tr}}msg-CThemeDoc-title-modify{{/tr}}: {{$theme->_view}}</th>
          {{else}}
          <th class="title" colspan="2">{{tr}}msg-CThemeDoc-title-create{{/tr}}</th>
          {{/if}}
        </tr>   
        <tr>
          <th><label for="nom" title="{{tr}}CThemeDoc-nom-desc{{/tr}}">{{tr}}CThemeDoc-nom{{/tr}}</label></th>
          <td><input name="nom" class="{{$theme->_props.nom}}" type="text" value="{{$theme->nom}}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">            
            {{if $theme->doc_theme_id}}
              <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'{{tr escape="javascript"}}CThemeDoc.one{{/tr}}',objName:'{{$theme->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
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