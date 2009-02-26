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
      <input type="hidden" name="m" value="dPfiles" />
      <input type="hidden" name="dosql" value="do_file_aed" />
      <input type="hidden" name="del" value="1" />
      {{mb_field object=$curr_file field="file_id" hidden=1 prop=""}}
      {{mb_field object=$curr_file field="_view" hidden=1 prop=""}}
    </form>
    <form name="editFile{{$curr_file->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
	   <input type="hidden" name="m" value="dPfiles" />
	   <input type="hidden" name="dosql" value="do_file_aed" />
	   <input type="hidden" name="file_id" value="{{$curr_file->_id}}" />
	   <input type="hidden" name="del" value="0" />
	   
	   <!-- Send File -->
		  {{if $curr_file->_is_sendable}}
		    <input type="hidden" name="_send" value="false" />
		    {{if $dPconfig.dPfiles.system_sender != "null"}}
		      {{if $curr_file->etat_envoi == "oui"}}
		        <button class="invalidefile notext" type="button" onclick="$V(this.form._send, true);submitFormAjax(this.form, 'systemMsg', { onComplete : function () { File.refresh('{{$object->_id}}','{{$object->_class_name}}'); } });">
		          {{tr}}Send File{{/tr}}
		        </button>
		      {{elseif $curr_file->etat_envoi == "obsolete"}}  
		        <button class="obsoletefile notext" type="button" onclick="$V(this.form._send, true);submitFormAjax(this.form, 'systemMsg', { onComplete : function () { File.refresh('{{$object->_id}}','{{$object->_class_name}}'); } });">
		          {{tr}}Send File{{/tr}}
		        </button>
		      {{else}}
		        <button class="sendfile notext" type="button" onclick="$V(this.form._send, true);submitFormAjax(this.form, 'systemMsg', { onComplete : function () { File.refresh('{{$object->_id}}','{{$object->_class_name}}'); } });">
		           {{tr}}Send File{{/tr}}
		         </button>
		      {{/if}}
		    {{/if}}
		  {{/if}}
    </form>
    <a href="#" onclick="File.popup('{{$object->_class_name}}','{{$object->_id}}','{{$curr_file->_class_name}}','{{$curr_file->_id}}');">{{$curr_file->file_name}}</a>
    <small>({{$curr_file->_file_size}})</small>
  </li>
  {{foreachelse}}
    <li><em>Aucun fichier disponible</em></li>
  {{/foreach}}
</ul>
