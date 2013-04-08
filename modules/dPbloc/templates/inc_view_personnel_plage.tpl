{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>Anesthésiste</th>
    <td class="greedyPane">
      <form name="editPlage" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="dosql" value="do_plagesop_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
        <input type="hidden" name="_repeat" value="1" />
        <input type="hidden" name="_type_repeat" value="simple" />
      
        <select name="anesth_id" style="width: 15em;" onchange="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadPersonnelPrevu});">
          <option value="">&mdash; Aucun anesthésiste</option>
          {{foreach from=$listAnesth item=_anesth}}
            <option value="{{$_anesth->_id}}" {{if $plage->anesth_id == $_anesth->_id}}selected="selected"{{/if}}>
              {{$_anesth->_view}}
            </option>
          {{/foreach}}
        </select>
      </form>
    </td>
  </tr>
  <tr>
    <th>
      <form name="editAffectationIADE" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpersonnel" />
        <input type="hidden" name="dosql" value="do_affectation_aed" />
        
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$plage->_id}}" />
        <input type="hidden" name="object_class" value="{{$plage->_class}}" />
        <input type="hidden" name="realise" value="0" />
        <select name="personnel_id" style="width: 15em;" onchange="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadPersonnelPrevu});">
          <option value="">&mdash; {{tr}}CPersonnel.emplacement.iade{{/tr}}</option>
          {{foreach from=$listPersIADE item=_personnelBloc}}
            <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
          {{/foreach}}
        </select>
      </form>
    </th>
    <td class="text">
      {{foreach from=$affectations_plage.iade item=_affectation}}
        <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPpersonnel" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
          <input type="hidden" name="del" value="1" />
          <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadPersonnelPrevu});">
            {{$_affectation->_ref_personnel->_ref_user->_view}}
          </button>
        </form>
      {{/foreach}}
    </td>
  </tr>
  <tr>
    <th>
      <form name="editAffectationAideOp" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpersonnel" />
        <input type="hidden" name="dosql" value="do_affectation_aed" />
        
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$plage->_id}}" />
        <input type="hidden" name="object_class" value="{{$plage->_class}}" />
        <input type="hidden" name="realise" value="0" />
        <select name="personnel_id" style="width: 15em;" onchange="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadPersonnelPrevu});">
          <option value="">&mdash; {{tr}}CPersonnel.emplacement.op{{/tr}}</option>
          {{foreach from=$listPersAideOp item=_personnelBloc}}
            <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
          {{/foreach}}
        </select>
      </form>
    </th>
    <td class="text">
      {{foreach from=$affectations_plage.op item=_affectation}}
        <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPpersonnel" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
          <input type="hidden" name="del" value="1" />
          <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadPersonnelPrevu});">
            {{$_affectation->_ref_personnel->_ref_user->_view}}
          </button>
        </form>
      {{/foreach}}
    </td>
  </tr>
  <tr>
    <th>
      <form name="editAffectationPanseuse" action="?m={{$m}}" method="post">
        <input type="hidden" name="m" value="dPpersonnel" />
        <input type="hidden" name="dosql" value="do_affectation_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="object_id" value="{{$plage->_id}}" />
        <input type="hidden" name="object_class" value="{{$plage->_class}}" />
        <input type="hidden" name="realise" value="0" />
        <select name="personnel_id" style="width: 15em;" onchange="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadPersonnelPrevu});">
          <option value="">&mdash; {{tr}}CPersonnel.emplacement.op_panseuse{{/tr}}</option>
          {{foreach from=$listPersPanseuse item=_personnelBloc}}
            <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
          {{/foreach}}
        </select>
      </form>
    </th>
    <td class="text">
      {{foreach from=$affectations_plage.op_panseuse item=_affectation}}
        <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
          <input type="hidden" name="m" value="dPpersonnel" />
          <input type="hidden" name="dosql" value="do_affectation_aed" />
          <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
          <input type="hidden" name="del" value="1" />
          <button class="cancel" type="button" onclick="submitFormAjax(this.form, 'systemMsg', {onComplete: reloadPersonnelPrevu});">
            {{$_affectation->_ref_personnel->_ref_user->_view}}
          </button>
        </form>
      {{/foreach}}
    </td>
  </tr>
</table>