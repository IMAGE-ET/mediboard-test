{{* $Id: vw_aed_rpu.tpl 8113 2010-02-22 09:29:33Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 8113 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPcabinet script=file}}

{{if $app->user_prefs.directory_to_watch != ''}}
  <script type="text/javascript">
  Main.add(function(){
    File.appletDirectory = "{{$app->user_prefs.directory_to_watch|addslashes}}";
  });
</script>
{{/if}}

<!-- Modale pour l'applet --> 
{{mb_include module=dPfiles template=yoplet_modal}}

<table class="form">

<tr>
  <th class="title" colspan="2">{{tr}}CSejour{{/tr}}</th>
</tr>

<tr>
  <th class="category" style="width: 50%;">{{tr}}CFile{{/tr}}</th>
  <th class="category" style="width: 50%;">Documents</th>
</tr>

<tr>
  <td id="files-CSejour">
		
		<script type="text/javascript">
		  File.register('{{$sejour->_id}}','{{$sejour->_class_name}}', 'files-CSejour');
		</script>
	</td>
  <td id="documents-CSejour">
		{{mb_include_script module=dPcompteRendu script=modele_selector}}
    {{mb_include_script module=dPcompteRendu script=document}}
    <script type="text/javascript">
      Document.register('{{$sejour->_id}}','{{$sejour->_class_name}}','{{$sejour->_praticien_id}}','documents-CSejour');
    </script>
	</td>
</tr>

<tr>
  <th class="title" colspan="2">{{tr}}CConsultation{{/tr}}</th>
</tr>

{{if $consult->_id}} 
<tr>
  <th class="category" style="width: 50%;">{{tr}}CFile{{/tr}}</th>
  <th class="category" style="width: 50%;">Documents</th>
</tr>

<tr>
  <td id="files-CConsultation">
    {{mb_include_script module=dPcabinet script=file}}
    <script type="text/javascript">
      File.register('{{$consult->_id}}','{{$consult->_class_name}}', 'files-CConsultation');
    </script>
  </td>
  <td id="documents-CConsultation">
    {{mb_include_script module=dPcompteRendu script=modele_selector}}
    {{mb_include_script module=dPcompteRendu script=document}}
    <script type="text/javascript">
      Document.register('{{$consult->_id}}','{{$consult->_class_name}}','{{$sejour->_praticien_id}}','documents-CConsultation');
    </script>
  </td>
</tr>
{{else}}
<tr>
	<td colspan="2">
		<div class="small-info">Consultation non réalisée</div>
	</td>
</tr>
{{/if}}

</table>