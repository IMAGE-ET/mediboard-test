<script>
  Main.add(function() {
    if (window.tabsConsult || window.tabsConsultAnesth) {
      var count_items = {{$object->_ref_documents|@count}};
      count_items += $("files-fdr").select("a[class=action]").length;
      Control.Tabs.setTabCount("fdrConsult", count_items);
    }
  });
</script>

{{assign var=doc_count value=$object->_ref_documents|@count}}
{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{mb_default var=object_class value=$object->_class}}
{{mb_default var=object_id value=$object->_id}}
{{if $mode != "hide"}}
  
  {{if $doc_count && $mode == "collapse"}}
  <tr id="DocsEffect-{{$object->_guid}}-trigger">
    <th class="category" colspan="3">
      {{tr}}{{$object->_class}}{{/tr}} :
      {{$doc_count}} document(s)

      <script>
        Main.add(function () {
          new PairEffect("DocsEffect-{{$object->_guid}}", { 
            bStoreInCookie: true
          });
        });
      </script>
    </th>
  </tr>
  {{/if}}

  <tbody id="DocsEffect-{{$object->_guid}}" {{if $mode == "collapse" && $doc_count}}style="display: none;"{{/if}}>
  
  {{foreach from=$object->_ref_documents item=document}}
  <tr {{if $document->annule}}style="display: none;" class="doc_cancelled"{{/if}}>
    <td class="text {{if $document->annule}}cancelled{{/if}}">
      {{if $document->_can->read}}
        <a href="#{{$document->_guid}}" onclick="Document.edit({{$document->_id}}); return false;" style="display: inline;">
      {{/if}}
      {{if $document->_is_locked}}
        <img src="style/mediboard/images/buttons/lock.png" onmouseover="ObjectTooltip.createEx(this, '{{$document->_guid}}', 'locker')"/>
      {{/if}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$document->_guid}}', 'objectView')">
        {{$document}}
      </span>
      {{if $document->_can->read}}
        </a>
      {{/if}}
      {{if $document->private}}
        &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
      {{/if}}
    </td>
    
    <td class="button" style="width: 1px; white-space: nowrap;">
      <form name="Edit-{{$document->_guid}}" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPcompteRendu" />
        <input type="hidden" name="dosql" value="do_modele_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$document}}
        
        <input type="hidden" name="object_id" value="{{$object_id}}" />
        <input type="hidden" name="object_class" value="{{$object_class}}" />
        <input type="hidden" name="file_category_id" value="{{$document->file_category_id}}" />
        <button type="button" class="print notext"
          onclick="{{if $pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
            Document.printPDF({{$document->_id}});
          {{else}}
            Document.print({{$document->_id}});
          {{/if}}">
          {{tr}}Print{{/tr}}
        </button>
        {{if $document->_can->edit && !$document->_is_locked}}
          <button type="button" class="trash notext" onclick="Document.del(this.form, '{{$document->nom|smarty:nodefaults|JSAttribute}}')">
            {{tr}}Delete{{/tr}}
          </button>
        {{/if}}
      </form>
    </td> 

    {{if $conf.dPfiles.system_sender}}
    <td class="button" style="width: 1px">
      <form name="Send-{{$document->_guid}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPcompteRendu" />
        <input type="hidden" name="dosql" value="do_modele_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$document}}
      
        <!-- Send File -->
        {{mb_include module=files template=inc_file_send_button 
          _doc_item=$document
          notext=notext
          onComplete="Document.refreshList('$document->file_category_id', '$object_class','$object_id')"
        }}
      </form>
    </td>
    {{/if}}
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3" class="empty">
      {{tr}}{{$object->_class}}{{/tr}} : Aucun document
    </td>
  </tr>
  {{/foreach}}
{{/if}}