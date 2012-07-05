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
    <th colspan="3" class="title">
    {{tr}}{{$object->_class}}-uf-title-choice{{/tr}} '{{$object}}'
    </th>
  </tr>
  
  {{foreach from=$affectations_uf item=_affectation_uf}}
    <tr>
       <td class="narrow">
          <form name="delete-{{$_affectation_uf->_guid}}" action="?m={{$m}}" method="post">
            <button type="button" onclick="AffectationUf.onDeletion(this.form);" class="remove notext">{{tr}}Remove{{/tr}}</button>
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="dosql" value="do_affectation_uf_aed" />
            <input type="hidden" name="affectation_uf_id" value="{{$_affectation_uf->_id}}" />
        </form>
      </td>
      <td>
        {{mb_value object=$_affectation_uf field=uf_id}}
      </td>
      <td>
        <strong>
          {{tr}}CUniteFonctionnelle.type.{{$_affectation_uf->_ref_uf->type}}{{/tr}}
        </strong>
      </td>
    </tr>
  {{/foreach}}
    
  <tr>
    <td colspan="3">
      <form name="create-CAffectationUniteFonctionnelle" action="?" method="post" onsubmit="return AffectationUf.onSubmit(this);">
        <input type="hidden" name="m" value="dPhospi" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="dosql" value="do_affectation_uf_aed" />
        <input type="hidden" name="object_class" value="{{$object->_class}}" />
        <input type="hidden" name="object_id" value="{{$object->_id}}" />
        <button class="add notext" type="submit" {{if !is_array($ufs) || count($ufs) ==0 }}disabled{{/if}}>{{tr}}Add{{/tr}}</button>
        <select name="uf_id" >
          <option value="">&mdash; {{tr}}CUniteFonctionnelle{{/tr}}</option>
            {{foreach from=$ufs item=_ufs key=type}}
              <optgroup label="{{tr}}CUniteFonctionnelle.type.{{$type}}{{/tr}}">
                {{foreach from=$_ufs item=uf}}
                  <option value="{{$uf->_id}}" {{if $ufs_selected.$type}}disabled{{/if}}>
                    {{$uf->libelle}}
                  </option>
                {{/foreach}}
              </optgroup>
            {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
</table>