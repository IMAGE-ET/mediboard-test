{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="tbl">
  <tr>
    <th class="title" style="width: 150px;">Séjours non affectés</th>
  </tr>

  {{foreach from=$sejours item=_sejour}}
  <tr>
    <td>
		  <div id="{{$_sejour->_guid}}" style="background-color:#fff;">
        <script type="text/javascript">Repartition.draggableSejour('{{$_sejour->_guid}}')</script>
 		  	{{assign var=patient value=$_sejour->_ref_patient}}
	      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
	        {{$patient}}        
	      </span> 
	      <br/> 
	      <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
	        pour {{$_sejour->_duree}}j        
	      </span>  
		  </div>
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td >
      <em>{{tr}}CSejour.none{{/tr}}</em>
    </td>
  </tr>
  {{/foreach}}
	
</table>
