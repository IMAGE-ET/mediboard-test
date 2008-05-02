<button class="new" type="button" style="float:right" onclick="File.upload('{{$object->_class_name}}','{{$object->_id}}', '')" >
  Ajouter un fichier
</button>

<strong>Fichiers</strong>
<ul>
  {{foreach from=$object->_ref_files item=curr_file}}
  <li>
    <form name="delFrm{{$curr_file->_id}}" action="?m={{$m}}" enctype="multipart/form-data" method="post" onsubmit="return checkForm(this)">
      <button class="trash notext" type="button" onclick="File.remove(this, '{{$object->_id}}', '{{$object->_class_name}}')">
        {{tr}}Delete{{/tr}}
      </button>
      <a href="#" onclick="File.popup('{{$object->_class_name}}','{{$object->_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}');">{{$curr_file->file_name}}</a>
      <small>({{$curr_file->_file_size}})</small>
      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_file_aed" />
      <input type="hidden" name="del" value="1" />
      {{mb_field object=$curr_file field="file_id" hidden=1 prop=""}}
      {{mb_field object=$curr_file field="_view" hidden=1 prop=""}}
    </form>
  </li>
  {{foreachelse}}
    <li><em>Aucun fichier disponible</em></li>
  {{/foreach}}
</ul>
