{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

PairEffect.initGroup("functionEffect", { 
  bStoreInCookie: true
});

</script>

{{if $board}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Hospitalisations</th>
  </tr>
  <tr>
    <th>Entree</th>
    <th>Sortie</th>
    <th>Patient</th>
    <th>Chambre</th>
    <th>Bornes GHM</th>
  </tr>
  {{foreach from=$listSejours item=curr_sejour}}
  <tr>
    {{if $date == $curr_sejour->entree_prevue|date_format:"%Y-%m-%d"}}
    <td style="background-color: #afa">
    {{else}}
    <td>
    {{/if}}
      {{$curr_sejour->entree_prevue|date_format:"%d/%m %Hh%M"}}
    </td>
    {{if $date == $curr_sejour->sortie_prevue|date_format:"%Y-%m-%d"}}
    <td style="background-color: #afa">
    {{else}}
    <td>
    {{/if}}
      {{$curr_sejour->sortie_prevue|date_format:"%d/%m %Hh%M"}}
    </td>
    <td>
      <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sejour->_id}}">
      	{{$curr_sejour->_ref_patient->_view}}
      </a>
    </td>
    <td>
      {{foreach from=$curr_sejour->_curr_affectations item=curr_aff}}
        {{$curr_aff->_ref_lit->_view}}<br />
      {{/foreach}}
    </td>
		<td>
      {{assign var=GHM value=$curr_sejour->_ref_GHM}}
      {{if $GHM->_DP}}
        <div class={{if $GHM->_borne_basse > $GHM->_duree || $GHM->_borne_haute < $GHM->_duree}}"warning"{{else}}"message"{{/if}}>
	        De {{$GHM->_borne_basse}}
	        à {{$GHM->_borne_haute}} jours
	        ({{$GHM->_duree}})
        </div>
      {{else}}
      -
      {{/if}}
		</td>
  </tr>
  {{/foreach}}        
</table>
{{else}}
<table class="tbl">
  <tr>
    <th class="title" colspan="4">Sorties</th>
  </tr>
  <tr>
    <th>Heure</th>
    <th>Patient</th>
    <th>Chambre</th>
  </tr>
  {{foreach from=$listAff item=curr_aff}}
  <tr id="operSejour{{$curr_aff->sejour_id}}-trigger">
    <td>{{$curr_aff->sortie|date_format:$dPconfig.time}}</td>
    <td style="background-image: none;padding: 2px;">{{$curr_aff->_ref_sejour->_ref_patient->_view}}</td>
    <td style="background-image: none;padding: 2px;">{{$curr_aff->_ref_lit->_view}}</td>
  </tr>
  <tbody class="functionEffect" id="operSejour{{$curr_aff->sejour_id}}">
  {{foreach from=$curr_aff->_ref_sejour->_ref_operations item=curr_oper}}
  <tr>
    <td></td>
    <td class="text">
      {{if $curr_oper->libelle}}
        <em>[{{$curr_oper->libelle}}]</em>
        <br />
      {{/if}}
      {{foreach from=$curr_oper->_ext_codes_ccam item=curr_code}}
        <strong>{{$curr_code->code}}</strong>
        {{if !$board}}
        : {{$curr_code->libelleLong}}
        {{/if}}
        <br />
      {{/foreach}}
    </td>
    <td>
      <table>
      {{foreach from=$curr_oper->_ref_documents item=curr_oper_doc}}
        <tr>
          <th>{{$curr_oper_doc->nom}}</th>
          <td class="button">
            <form name="editDocumentFrm{{$curr_oper_doc->compte_rendu_id}}" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPcompteRendu" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_modele_aed" />
            <input type="hidden" name="object_id" value="{{$curr_oper->operation_id}}" />
            <input type="hidden" name="compte_rendu_id" value="{{$curr_oper_doc->compte_rendu_id}}" />
            <button class="edit notext" type="button" onclick="Document.edit({{$curr_oper_doc->compte_rendu_id}})">{{tr}}Edit{{/tr}}</button>
            <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$curr_oper_doc->nom|smarty:nodefaults|JSAttribute}}'})" >{{tr}}Delete{{/tr}}</button>
            <button class="trash notext" type="button" onclick="this.form.del.value = 1; this.form.submit()">{{tr}}Delete{{/tr}}</button>
            </form>
          </td>
        </tr>
      {{/foreach}}
      </table>
      <form name="newDocumentFrm" action="?m={{$m}}" method="post">
      <table>
        <tr>
          <td>
            <select name="_choix_modele" onchange="if (this.value) Document.create(this.value, {{$curr_oper->operation_id}})">
              <option value="">&mdash; Choisir un modèle</option>
              <optgroup label="CRO">
              {{foreach from=$crList item=curr_cr}}
              <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
              {{/foreach}}
              </optgroup>
              <optgroup label="Document d'hospi">
              {{foreach from=$hospiList item=curr_hospi}}
              <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
              {{/foreach}}
              </optgroup>
            </select>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{/foreach}}
  </tbody>
  {{/foreach}}        
</table>
{{/if}}
