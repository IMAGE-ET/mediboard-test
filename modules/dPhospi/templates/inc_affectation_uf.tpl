{{* $Id: inc_affectation_uf.tpl $ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=hospi script=affectation_uf}}

<table class="form">
  <tr>
    <th class="title">
    {{tr}}{{$object->_class}}-uf-title-choice{{/tr}} '{{$object}}'
    </th>
  </tr>
	
	{{foreach from=$affectations_uf item=_affectation_uf}}
    <tr>
	   	<td>
	      <form name="delete-{{$_affectation_uf->_guid}}" action="?m={{$m}}" method="post">
          <button type="button" onclick="AffectationUf.onDeletion(this.form);" class="remove notext">{{tr}}Remove{{/tr}}</button>
	        <input type="hidden" name="m" value="{{$m}}" />
	        <input type="hidden" name="del" value="1" />
	        <input type="hidden" name="dosql" value="do_affectation_uf_aed" />
	        <input type="hidden" name="affectation_uf_id" value="{{$_affectation_uf->_id}}" />
	        {{mb_value object=$_affectation_uf field=uf_id}}
	      </form>
			</td>
    </tr>
  {{/foreach}}
		
	<tr>
		<td>
			<form name="create-CAffectationUniteFonctionnelle" action="?" method="post" onsubmit="return AffectationUf.onSubmit(this);">
			  <input type="hidden" name="m" value="dPhospi" />
			  <input type="hidden" name="del" value="0" />
			  <input type="hidden" name="dosql" value="do_affectation_uf_aed" />
			  <input type="hidden" name="object_class" value="{{$object->_class}}" />
			  <input type="hidden" name="object_id" value="{{$object->_id}}" />
        <button class="add notext" type="submit" {{if !is_array($ufs) || count($ufs) ==0 }}disabled{{/if}}>{{tr}}Add{{/tr}}</button>
				<select name="uf_id" >
          <option value="">&mdash; {{tr}}CUniteFonctionnelle{{/tr}}</option>
            {{foreach from=$ufs item=uf}}                 
              <option value="{{$uf->_id}}" 
							 {{foreach from=$affectations_uf item=_affectation_uf}}
							   {{if $_affectation_uf->uf_id==$uf->_id}}disabled{{/if}}
							 {{/foreach}}
							>{{$uf->libelle}}</option>
            {{/foreach}}
        </select>
      </form>
		</td>
	</tr>
</table>