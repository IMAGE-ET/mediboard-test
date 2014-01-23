<script type="text/javascript">

function changePage(page){
  oForm = getForm("filter-users");
  $V(oForm.current, page);
  oForm.submit();
  
}

</script>

<form action="?" name="filter-users" method="get" >
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden" name="dialog" value="{{$dialog}}" />
  <input type="hidden" name="current" value="{{$current}}" />
  <input type="hidden" name="interv" value="{{$interv}}"/>
</form>

{{mb_include module=system template=inc_pagination change_page=changePage}}

<form name="searchIntervenant" method="get" onsubmit="return false;">
  <input type="text" name="keywords_nom" value="" class="autocomplete"/>
  <label>
    <input type="checkbox" name="exclude_without_code" {{if $exclude_without_code == "true"}}checked="checked"{{/if}}/>
   {{tr}}CIntervenantCdARR.exclude_without_code{{/tr}}
  </label>
</form>

{{main}}
  var oForm = getForm("searchIntervenant");
  var url = new Url("ssr", "ajax_interv_autocomplete");
  url.autoComplete(oForm.keywords_nom, '', {
    minChars: 2,
    dropdown: true,
    width: "250px",
    select: "interv",
    callback: function(input, querystring) {
      return querystring + "&exclude_without_code=" + oForm.exclude_without_code.checked;
    },
    afterUpdateElement: function(oHidden) {
      var url = new Url("ssr", "edit_codes_intervenants", "tab");
      url.addParam("exclude_without_code", oForm.exclude_without_code.checked);
      url.addParam("interv", oHidden.value)
      url.redirect();
    }
  });
{{/main}}
<table class="tbl">
  <tr>
    <th>{{mb_title object=$mediuser field=_user_last_name}}</th>
    <th>{{mb_title object=$mediuser field=code_intervenant_cdarr}}</th>
  </tr>
  {{foreach from=$mediusers item=_mediuser}}
  <tr>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_mediuser}}</td>
    <td>
      <form name="mediuser-{{$_mediuser->_id}}" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">

      <input type="hidden" name="m" value="mediusers" />
      <input type="hidden" name="dosql" value="do_mediusers_aed" />
      <input type="hidden" name="user_id" value="{{$_mediuser->_id}}" />
      <input type="hidden" name="del" value="0" />
      
      <select name="code_intervenant_cdarr" onchange="this.form.onsubmit()">
        <option value="">&mdash; aucun code</option>
        {{foreach from=$intervenants item=_interv}}
        <option value="{{$_interv->code}}" {{if $_interv->code == $_mediuser->code_intervenant_cdarr}}selected="selected"{{/if}}>
          {{$_interv->_view}}
        </option>
        {{/foreach}}
      </select>
      
      </form>
    </td>
  </tr>
  {{/foreach}}
</table>