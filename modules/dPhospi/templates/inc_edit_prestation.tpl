{{if $prestation->_id}}
  <script type="text/javascript">
    Main.add(function() {
      editItem('{{$item_id}}', '{{$prestation->_class}}', '{{$prestation->_id}}');
      refreshItems('{{$prestation->_class}}', '{{$prestation->_id}}', '{{$item_id}}');
    });
  </script>    
{{/if}}
<form name="edit_prestation" method="post" onsubmit="return onSubmitFormAjax(this)">
  <input type="hidden" name="m" value="dPhospi" />
  {{if $prestation instanceof CPrestationJournaliere}}
    <input type="hidden" name="dosql" value="do_prestation_journaliere_aed" />
  {{else}}
    <input type="hidden" name="dosql" value="do_prestation_ponctuelle_aed" />
  {{/if}}
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="callback" value="afterEditPrestation" />
  {{mb_key object=$prestation}}
  {{mb_field object=$prestation field=group_id hidden=1}}
  
  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$prestation}}
    <tr>
      <th>
        {{mb_label object=$prestation field=nom}}
      </th>
      <td>
        {{mb_field object=$prestation field=nom}}
      </td>
    </tr>
    {{if $prestation instanceof CPrestationJournaliere}}
    <tr>
      <th>
        {{mb_label object=$prestation field=desire}}
      </th>
      <td>
        {{mb_field object=$prestation field=desire}}
      </td>
    </tr>
    {{/if}}
    <tr>
      <th>
        {{mb_label object=$prestation field=type_hospi}}
      </th>
      <td>
        {{mb_field object=$prestation field=type_hospi}}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="save" onclick="this.form.onsubmit()">
          {{tr}}{{if $prestation->_id}}Save{{else}}Create{{/if}}{{/tr}}
        </button>
        {{if $prestation->_id}}
          <button type="button" class="cancel" onclick="confirmDeletion(this.form, {
              typeName: 'la prestation',
              objName:'{{$prestation->_view|smarty:nodefaults|JSAttribute}}',
              ajax: true})">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

<hr />
<div id="edit_item"></div>
<hr />
<div id="list_items"></div>