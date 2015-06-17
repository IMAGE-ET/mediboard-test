{{mb_script module=files script=file_category}}

<script>
  Main.add(function() {
    getForm('listFilter').onsubmit();
  });
</script>

<table class="main">
  {{if $can->edit}}
    <tr>
      <td style="width: 60%">
        <button class="button new" onclick="FilesCategory.edit('0');">{{tr}}CFilesCategory-title-create{{/tr}}</button>
      </td>
    </tr>
  {{/if}}

  <tr>
    <td>
      <form name="listFilter" action="?" method="get"
            onsubmit="return onSubmitFormAjax(this, null, 'list_file_category')">
        <input type="hidden" name="m" value="files" />
        <input type="hidden" name="a" value="ajax_list_categories" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>

        <table class="main layout">
          <tr>
            <td class="separator expand" onclick="MbObject.toggleColumn(this, $(this).next())"></td>

            <td>
              <table class="form">
                <tr>
                  <th style="width: 8%"> Mots clés : </th>
                  <td> <input type="text" name="filter" value="{{$filter}}" style="width: 20em;" onchange="$V(this.form.page, 0)" /> </td>

                  <th> {{mb_label class="CFilesCategory" field="class"}} </th>
                  <td>
                    <select name="class" style="width: 15em;">
                      <option value="">&mdash; {{tr}}All{{/tr}}</option>
                      {{foreach from=$listClass item=_class}}
                        <option value="{{$_class}}" {{if $_class == $class}}selected{{/if}}>
                          {{$_class}}
                        </option>
                      {{/foreach}}
                    </select>
                  </td>

                  <th> {{mb_label class="CFilesCategory" field="eligible_file_view"}} </th>
                  <td>
                    <label>Tous <input name="eligible_file_view" value="" {{if $eligible_file_view == null}}checked{{/if}}
                                      type="radio" onchange="$V(this.form.page, 0, false)"/></label>
                    <label>Oui <input name="eligible_file_view" value="1" {{if $eligible_file_view == "1"}}checked{{/if}}
                                      type="radio" onchange="$V(this.form.page, 0, false)"/></label>
                    <label>Non <input name="eligible_file_view" value="0" {{if $eligible_file_view == "0"}}checked{{/if}}
                                      type="radio" onchange="$V(this.form.page, 0, false)"/></label>
                  </td>
                </tr>

                <tr>
                  <td colspan="6">
                    <button type="submit" class="search">{{tr}}Filter{{/tr}}</button>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>

<div id="list_file_category"></div>
