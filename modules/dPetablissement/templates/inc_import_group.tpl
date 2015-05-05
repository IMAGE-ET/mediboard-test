<script>
  Main.add(function(){
    Control.Tabs.create("import-tabs");
  });

  submitImport = function (form) {
    var selects = form.select("select").filter(function(select){
      return $V(select) === "__ignore__";
    });

    selects.each(function(select){
      select.disabled = true;
    });

    onSubmitFormAjax(form, {}, 'group-import-report');

    selects.each(function(select){
      select.disabled = null;
    });

    return false;
  };
</script>

<form name="import-ex_class" method="post" onsubmit="return submitImport(this)">
  <input type="hidden" name="m" value="etablissement" />
  <input type="hidden" name="dosql" value="do_import_group" />
  <input type="hidden" name="file_uid" value="{{$uid}}" />

  <ul class="control_tabs" id="import-tabs">
  {{foreach from=$data item=_data key=_class name=_data}}
    <li>
      <a href="#{{$_class}}-tab">
        {{$smarty.foreach._data.iteration+1}}. {{tr}}{{$_class}}{{/tr}}
      </a>
    </li>
  {{/foreach}}
  </ul>

  {{foreach from=$data item=_data key=_class}}
    <div id="{{$_class}}-tab" style="display: none;">
      {{mb_include module=etablissement template=inc_import_group_subitem
        class=$_class
        field=$_data.field
        objects=$_data.objects
        all_objects=$_data.all_objects
        allow_create=$_data.allow_create
      }}
    </div>
  {{/foreach}}

  <table class="main tbl">
    <tr>
      <td style="width: 50%;"></td>
      <td><button class="save">{{tr}}Import{{/tr}}</button></td>
    </tr>
  </table>
</form>