{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage forms
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
onUploadComplete = function(message){
  SystemMessage.notify(message);
}
</script>

<form name="import-form" action="?m=forms&amp;a=do_import_fields" method="post" 
      onsubmit="return checkForm(this)" target="upload_iframe" enctype="multipart/form-data">
  <input type="hidden" name="m" value="forms" />
  <input type="hidden" name="a" value="do_import_fields" />
  <input type="hidden" name="suppressHeaders" value="1" />
	
	<table class="main form" style="table-layout: fixed;">
		<tr>
			<th class="title" colspan="2">
				Importation
			</th>
		</tr>
		
		<tr>
			<th>
				<label for="import">Fichier</label>
			</th>
			<td>
		    <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
		    <input type="file" name="import" style="width: 20em;" class="notNull" />
			</td>
		</tr>
		
		<tr>
			<th></th>
      <td>
        <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
      </td>
		</tr>
	</table>
</form>

<iframe name="upload_iframe" style="display: none;"></iframe>
