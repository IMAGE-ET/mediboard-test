{{mb_script module="mediusers" script="CDiscipline"}}

<script>
  Main.add(function() {
    getForm('listFilter').onsubmit();
  });
</script>

<table class="main">
  {{if $can->edit}}
    <tr>
      <td style="width: 60%">
        <a class="button new" onclick="CDiscipline.edit('0')">
          {{tr}}CDiscipline-title-create{{/tr}}
        </a>
      </td>
    </tr>
  {{/if}}
  <tr>
    <td>
      <form name="listFilter" action="?" method="get"
            onsubmit="return onSubmitFormAjax(this, null, 'list_disciplines')">
        <input type="hidden" name="m" value="mediusers" />
        <input type="hidden" name="a" value="ajax_search_discipline" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.onsubmit()"/>

        <table class="main layout">
          <tr>
            <td class="separator expand" onclick="MbObject.toggleColumn(this, $(this).next())"></td>

            <td>
              <table class="form">
                <tr>
                  <th style="width: 8%"> Mots clés : </th>
                  <td> <input type="text" name="filter" value="" style="width: 20em;" onchange="$V(this.form.page, 0)" /> </td>
                </tr>

                <tr>
                  <td colspan="2">
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

<div id="list_disciplines" style="overflow: hidden"></div>