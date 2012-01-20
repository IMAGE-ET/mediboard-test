<script type="text/javascript">
  Main.add(function() {
    getForm('filterDocs').onsubmit();
  });
  
  changePage = function(page) {
    $V(getForm('filterDocs').page,page);
  }
</script>
<form name="filterDocs" method="get" onsubmit="return onSubmitFormAjax(this, null, 'result_docs')">
  <input type='hidden' name="m" value="dPpmsi" />
  <input type="hidden" name="a" value="ajax_refresh_last_docs" />
  <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit();"/>
  <table class="form">
    <tr>
      <th class="title" colspan="8">
        Filtre
      </th>
    </tr>
    <tr>
      <th>
        {{mb_label class="CCompteRendu" field="file_category_id"}}
      </th>
      <td>
        <select name="cat_docs" onchange="this.form.onsubmit();">
           <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
           {{foreach from=$categories item=_cat}}
             <option value="{{$_cat->_id}}" {{if $_cat->_id == $cat_docs}}selected="selected"{{/if}}>{{$_cat}}</option>
           {{/foreach}}
        </select>
      </td>
      <th>
        {{mb_label class="CMediusers" field="function_id"}}
      </th>
      <td>
        <select name="specialite_docs" onchange="$V(this.form.prat_docs, '', false); this.form.onsubmit();">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_function list=$specialites selected=$specialite_docs}}
        </select>
      </td>
      <th>
        Utilisateur
      </th>
      <td>
        <select name="prat_docs" onchange="$V(this.form.specialite_docs, '', false); this.form.onsubmit();">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=prat_docs}}
        </select>
      </td>
      <td>
        Du
        <input type="hidden" name="date_docs_min" onchange="this.form.onsubmit();" value="{{$date_docs_min}}"/>
        au
        <input type="hidden" name="date_docs_max" onchange="this.form.onsubmit();" value="{{$date_docs_max}}"/>
        <script type="text/javascript">
          Main.add(function() {
            var form = getForm('filterDocs');
            Calendar.regField(form.date_docs_min);
            Calendar.regField(form.date_docs_max);
          });
        </script>
      </td>
    </tr>
  </table>
</form>
<div style="width: 100%" id="result_docs"></div>