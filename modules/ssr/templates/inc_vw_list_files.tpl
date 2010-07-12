<table class="tbl">
  <tr id="list_files-trigger">
    <th class="category" colspan="3">
      {{$count_object}} fichier(s)

      <script type="text/javascript">
        Main.add(function () {
          new PairEffect("list_files", { 
            bStoreInCookie: true
          });
        });
      </script>
    </th>
  </tr>
  
 <tbody id="list_files" style="display: none;">
{{if $count_object}} 
{{foreach from=$object->_ref_files item=_file}}
  <tr>
    <td>
      <a href="#" class="action" 
         onclick="File.popup('{{$object->_class_name}}','{{$object->_id}}','{{$_file->_class_name}}','{{$_file->_id}}');"
         onmouseover="ObjectTooltip.createEx(this, '{{$_file->_guid}}', 'objectView')">
        {{$_file}}
      </a>
    </td>
  </tr>
{{/foreach}}
{{/if}}
</table>



  