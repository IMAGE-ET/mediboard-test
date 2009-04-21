{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editDetCCAM" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_detcim_aed" />
<input type="hidden" name="iddiag" value="{{$detcim->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $detcim->_id != ''}}
    <th class="title modify" colspan="2">
 		  Informations du détail CIM {{$detcim->_view}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Informations du détail CIM
    </th>
    {{/if}}
  </tr>
  {{if $detcim->_id != ''}}
  
  <tr>
		<th>{{mb_label object=$detcim field=iddiag}}</th>
		<td>{{mb_value object=$detcim field=iddiag}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detcim field=idinterv}}</th>
		<td>{{mb_value object=$detcim field=idinterv}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detcim field=numdos}}</th>
		<td>{{mb_value object=$detcim field=numdos}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detcim field=coddia}}</th>
		<td>{{mb_value object=$detcim field=coddia}}</td>
  </tr>  
    
  <tr>
    <th>{{mb_label object=$filter field=typdia}}</th>
		<td>{{mb_value object=$filter field=typdia}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$detcim field=datmaj}}</th>
		<td>{{mb_value object=$detcim field=datmaj}}</td>
  </tr>  

  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'entête CIM',objName:'{{$detcim->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>
{{/if}}
