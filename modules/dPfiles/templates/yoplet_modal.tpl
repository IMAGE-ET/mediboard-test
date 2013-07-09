<script type="text/javascript">
  Main.add(function() {
    var url = new Url("files", "ajax_category_autocomplete");
    url.addParam("object_class", "{{$object->_class}}");
    File.applet.autocompleteCat =
      url.autoComplete(getForm('addFastFile').keywords_category, '', {
        minChars: 2,
        dropdown: true,
        width: "100px"
      });
  });
</script>
<div id="modal-yoplet" style="display: none; width: 400px;">
  <form name="addFastFile" method="post" action="?"
    onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="dPfiles" />
    <input type="hidden" name="dosql" value="do_file_aed" />
    <input type="hidden" name="_from_yoplet" value="1" />
    <input type="hidden" name="object_class" value="" />
    <input type="hidden" name="object_id" value="" />
    <input type="hidden" name="file_date" value="now" />
    <input type="hidden" name="callback" value="File.applet.addfile_callback" />
    <input type="hidden" name="_index" value="" />
    <input type="hidden" name="_file_path" value="" />
    <input type="hidden" name="_checksum" value="" />
    
		<div style="max-height: 400px; overflow: auto;">
			
    <table class="tbl">
      <tr>
        <th class="title" colspan="5">
        	Fichiers disponibles dans : 
					<br />
 					{{$app->user_prefs.directory_to_watch}}
				</th>
      </tr>
			<tr>
        <th class="narrow"></th>
        <th>{{mb_title class=CFile field=file_name}}</th>
        <th style="width: 2em;">
				  <img src="style/mediboard/images/buttons/change.png" title="{{tr}}Send{{/tr}}"/>
				</th>
        <th style="width: 2em;">
          <img src="style/mediboard/images/buttons/merge.png" title="{{tr}}Link{{/tr}}"/>
				</th>
        <th style="width: 2em;">
          <img src="style/mediboard/images/buttons/trash.png" title="{{tr}}Delete{{/tr}}"/>
				</th>
			</tr>
      <tbody id="file-list">
      </tbody>
		</table>
		
		</div>
		<hr />
		
		<table class="form">
      <tr>
        <th style="width: 1px;">
          {{mb_label class=CFile field=_rename}}
        </th>
				<td>
          <input type="text" name="_rename" value="" />
        </td>
      </tr>
      <tr>
        <th>
          {{mb_label class=CFile field=file_category_id}}
        </th>
        <td>
          <input type="text" value="&mdash; {{tr}}Choose{{/tr}}" name="keywords_category" class="autocomplete str" autocomplete="off" onclick="this.value = '';" style="width: 5em;" />
          <input type="hidden" name="file_category_id" value="" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
				  <input type="checkbox" name="delete_auto" checked="checked"/>
					<label for="delete_auto">{{tr}}Delete after send{{/tr}}</label>
				</td>
      </tr>
      <tr>
        <td class="button" colspan="2">
	        <button type="button" class="cancel" onclick="File.applet.cancelModal();">
	        	{{tr}}Cancel{{/tr}}
					</button>
	        <button type="button" class="change uploadinmodal" onclick="this.disabled = 'disabled'; File.applet.uploadFiles();">
	          {{tr}}Upload{{/tr}}
					</button>
	        <button type="button" class="tick" onclick="File.applet.closeModal();">
	          {{tr}}Close{{/tr}}
	        </button>
        </td>
      </tr>
    </table>
  </form>
</div>