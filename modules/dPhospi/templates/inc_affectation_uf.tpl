{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=hospi script=affectation_uf}}

<table class="form">
  <tr>
    <th colspan="4" class="title">
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
      <td class="text empty">
        {{assign var=uf_aff value=$_affectation_uf->_ref_uf}}
        {{if $uf_aff->date_debut && $uf_aff->date_fin}}
          Du {{mb_value object=$uf_aff field=date_debut}}
          Au {{mb_value object=$uf_aff field=date_fin}}
        {{elseif $uf_aff->date_debut}}
          Jusqu'au {{mb_value object=$uf_aff field=date_fin}}
        {{elseif $uf_aff->date_fin}}
          A partir du {{mb_value object=$uf_aff field=date_fin}}
        {{/if}}
      </td>
    </tr>
  {{/foreach}}
    
  <tr>
    <td colspan="4">
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
                  {{assign var=uf_id value=$uf->_id}}
                  <option value="{{$uf->_id}}" {{if isset($ufs_selected.$uf_id|smarty:nodefaults)}}disabled{{/if}}>
                    {{$uf->libelle}}
                    {{if $uf->date_debut && $uf->date_fin}}
                      Du {{mb_value object=$uf field=date_debut}}
                      Au {{mb_value object=$uf field=date_fin}}
                    {{elseif $uf->date_debut}}
                      Jusqu'au {{mb_value object=$uf field=date_fin}}
                    {{elseif $uf->date_fin}}
                      A partir du {{mb_value object=$uf field=date_fin}}
                    {{/if}}
                  </option>
                {{/foreach}}
              </optgroup>
            {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
</table>