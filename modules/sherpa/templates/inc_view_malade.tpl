{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editMalade" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_SpMalade_aed" />
<input type="hidden" name="malnum" value="{{$malade->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $malade->_id}}
    <th class="title modify" colspan="2">
 		  Affichage des informations du malade {{$malade->malnom}} {{$malade->malpre}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Affichage des informations d'un malade
    </th>
    {{/if}}
  </tr>
  {{if $malade->_id}}
  
  <tr>
    <th>{{mb_label object=$malade field="malfla"}}</th>
	<td>{{mb_value object=$malade field="malfla"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$malade field="malnom"}}</th>
		<td>{{mb_value object=$malade field="malnom"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="malpre"}}</th>
		<td>{{mb_value object=$malade field="malpre"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="datnai"}}</th>
		<td>{{mb_value object=$malade field="datnai"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="rannai"}}</th>
		<td>{{mb_value object=$malade field="rannai"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="vilnai"}}</th>
		<td>{{mb_value object=$malade field="vilnai"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="malpat"}}</th>
		<td>{{mb_value object=$malade field="malpat"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="nation"}}</th>
		<td>{{mb_value object=$malade field="nation"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="sexe"}}</th>
		<td>{{mb_value object=$malade field="sexe"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="malnss"}}</th>
		<td>{{mb_value object=$malade field="malnss"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="clenss"}}</th>
		<td>{{mb_value object=$malade field="clenss"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="parent"}}</th>
		<td>{{mb_value object=$malade field="parent"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="malru1"}}</th>
		<td>{{mb_value object=$malade field="malru1"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="malru2"}}</th>
		<td>{{mb_value object=$malade field="malru2"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="malpos"}}</th>
		<td>{{mb_value object=$malade field="malpos"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="malvil"}}</th>
		<td>{{mb_value object=$malade field="malvil"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="maltel"}}</th>
		<td>{{mb_value object=$malade field="maltel"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="malpro"}}</th>
		<td>{{mb_value object=$malade field="malpro"}}</td>
  </tr> 

  <tr>
		<th>{{mb_label object=$malade field="perso1"}}</th>
		<td>{{mb_value object=$malade field="perso1"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="prvad1"}}</th>
		<td>{{mb_value object=$malade field="prvad1"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="prvil1"}}</th>
		<td>{{mb_value object=$malade field="prvil1"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="prtel1"}}</th>
		<td>{{mb_value object=$malade field="prtel1"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="malie1"}}</th>
		<td>{{mb_value object=$malade field="malie1"}}</td>
  </tr>  

  <tr>
		<th>{{mb_label object=$malade field="perso2"}}</th>
		<td>{{mb_value object=$malade field="perso2"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="prvad2"}}</th>
		<td>{{mb_value object=$malade field="prvad2"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$malade field="prvil2"}}</th>
		<td>{{mb_value object=$malade field="prvil2"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="prtel2"}}</th>
		<td>{{mb_value object=$malade field="prtel2"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="malie2"}}</th>
		<td>{{mb_value object=$malade field="malie2"}}</td>
  </tr>  

  <tr>
		<th>{{mb_label object=$malade field="assnss"}}</th>
		<td>{{mb_value object=$malade field="assnss"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="nsscle"}}</th>
		<td>{{mb_value object=$malade field="nsscle"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="assnom"}}</th>
		<td>{{mb_value object=$malade field="assnom"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="asspre"}}</th>
		<td>{{mb_value object=$malade field="asspre"}}</td>
  </tr> 

  <tr>
		<th>{{mb_label object=$malade field="asspat"}}</th>
		<td>{{mb_value object=$malade field="asspat"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="assru1"}}</th>
		<td>{{mb_value object=$malade field="assru1"}}</td>
  </tr>  

  <tr>
		<th>{{mb_label object=$malade field="assru2"}}</th>
		<td>{{mb_value object=$malade field="assru2"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="asspos"}}</th>
		<td>{{mb_value object=$malade field="asspos"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="assvil"}}</th>
		<td>{{mb_value object=$malade field="assvil"}}</td>
  </tr>    

  <tr>
		<th>{{mb_label object=$malade field="datmaj"}}</th>
		<td>{{mb_value object=$malade field="datmaj"}}</td>
  </tr>
     
  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le malade',objName:'{{$malade->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$malade->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'établissement courant</th>
  </tr>
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=patient value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>
      <a href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
        {{$patient->_view}}
      </a>
    </td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
</table>

{{/if}}
