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

<form name="import-form" action="?m=forms&amp;a=do_import" method="post" target="upload_iframe" enctype="multipart/form-data">
  <input type="hidden" name="m" value="forms" />
  <input type="hidden" name="a" value="do_import" />
  <input type="hidden" name="suppressHeaders" value="1" />
	
	<table class="main form">
		<tr>
			<th class="title" colspan="5">
				Importation
			</th>
		<tr>
			<th>
				Type d'éléments à importer
			</th>
			<td>
		    <select name="object_class">
		      {{foreach from=$classes item=_class}}
		        <option value="{{$_class}}">{{tr}}{{$_class}}{{/tr}}</option>
		      {{/foreach}}
		    </select>
			</td>
			
			<th>Fichier</th>
			<td>
		    <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
		    <input type="file" name="import" style="width: 20em;" />
			</td>
			
			<td>
				<button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
			</td>
		</tr>
	</table>
</form>

<iframe name="upload_iframe" style="display: none;"></iframe>
