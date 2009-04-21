{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $is_anesth || $is_chir || $is_admin}}
<form name="filtre-protocole" method="get" action="?">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
	<table class="form">
	  <tr>
	    <td>
	    {{if $anesths|@count}}
        Anesthésiste: 
        <select name="anesth_id" onchange="this.form.submit()">
          <option value="">&mdash; Choix d'un anesthésiste</option>
          {{foreach from=$anesths item=_anesth}}
            <option class="mediuser" style="border-color: #{{$_anesth->_ref_function->color}};" value="{{$_anesth->_id}}" {{if $_anesth->_id == $anesth_id}}selected="selected"{{/if}}>{{$_anesth->_view}}</option>
          {{/foreach}}
        </select>
      {{/if}}
	    </td>
      <td>
      {{if $praticiens|@count}}
        Chirurgien: 
        <select name="praticien_id" onchange="this.form.submit()">
          <option value="">&mdash; Choix d'un chirurgien</option>
          {{foreach from=$praticiens item=_praticien}}
            <option class="mediuser" style="border-color: #{{$_praticien->_ref_function->color}};" value="{{$_praticien->_id}}" {{if $_praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$_praticien->_view}}</option>
          {{/foreach}}
        </select>
      {{/if}}
      </td>
	    <td>
	      <select name="all_prot" onchange="this.form.submit()">
	        <option value="1" {{if $all_prot == "1"}}selected="selected"{{/if}}>Tous les protocoles</option>
	        <option value="0" {{if $all_prot == "0"}}selected="selected"{{/if}}>Seulement les protocoles non associés</option>
	      </select>
	    </td>
	  </tr>
	</table>
</form>
<table class="tbl">
  <tr>
    <th>Chirurgien - Actes CCAM</th>
    <th>Protocole Anesth</th>
    <th>Protocole Chir</th>
  </tr>
  {{foreach from=$protocoles item=_protocole}}
  <tr>
    <td class="text">
      <strong>
      {{$_protocole->_ref_chir->_view}}
      {{if $_protocole->libelle}}
        - {{$_protocole->libelle}}
      {{/if}}
      {{foreach from=$_protocole->_ext_codes_ccam item=_code}}
        - {{$_code->code}}
      {{/foreach}}
      </strong>
      <br />  
      {{foreach from=$_protocole->_ext_codes_ccam item=_code_ccam}}
        - {{$_code_ccam->libelleLong}}
        <br />
      {{/foreach}}
    </td>
    <td>
      {{if $is_anesth || $is_admin}}
      <form name="selProtocoleAnesth-{{$_protocole->_id}}" action="?" method="post">  
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_protocole_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="protocole_id" value="{{$_protocole->_id}}" />
        <select name="protocole_prescription_anesth_id" 
                onchange="{{if $is_admin}} if(this.value) { $('prot_anesth_{{$_protocole->_id}}').innerHTML = this.options[this.selectedIndex].text; } else { $('prot_anesth_{{$_protocole->_id}}').innerHTML = '' } {{/if}}
                submitFormAjax(this.form, 'systemMsg');" style="max-width: 15em;">
          <option value="">&mdash; 
          {{if $is_admin}}
            Changer de protocole
          {{else}}
            Sélection d'un protocole
          {{/if}}
          </option>
          {{foreach from=$protocoles_list.anesth key=owner item=_protocoles_by_owner}}
            {{if $_protocoles_by_owner|@count}}
              <optgroup label="Liste des protocoles {{tr}}CPrescription._owner.{{$owner}}{{/tr}}">
              {{foreach from=$_protocoles_by_owner item=protocole}}
                <option value="{{$protocole->_id}}">{{$protocole->libelle}}</option>
              {{/foreach}}
              </optgroup>
            {{/if}}
          {{/foreach}}
        </select>
      </form>
      {{if $is_admin}}
        <br />
        <div id="prot_anesth_{{$_protocole->_id}}">{{$_protocole->_ref_protocole_prescription_anesth->_view}}</div>
      {{/if}}
      {{else}}
        {{$_protocole->_ref_protocole_prescription_anesth->_view}}
      {{/if}}
    </td>
    <td>  
      {{if $is_chir || $is_admin}}
      <form name="selProtocoleChir-{{$_protocole->_id}}" action="?" method="post">  
        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="dosql" value="do_protocole_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="protocole_id" value="{{$_protocole->_id}}" />
        <select name="protocole_prescription_chir_id" onchange="submitFormAjax(this.form, 'systemMsg')" style="max-width: 15em;">
          <option value="">&mdash; Sélection d'un protocole</option>
          {{foreach from=$protocoles_list.chir key=owner item=_protocoles_by_owner}}
            {{if $_protocoles_by_owner|@count}}
              <optgroup label="Liste des protocoles {{tr}}CPrescription._owner.{{$owner}}{{/tr}}">
              {{foreach from=$_protocoles_by_owner item=protocole}}
                <option value="{{$protocole->_id}}">{{$protocole->libelle}}</option>
              {{/foreach}}
              </optgroup>
            {{/if}}
          {{/foreach}}
        </select>
      </form>

      {{else}}
       {{$_protocole->_ref_protocole_prescription_chir->_view}}
      {{/if}}
      
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3">Aucun protocole ne correspond à la recherche</td>
  </tr>
  {{/foreach}}
</table>
{{else}}
  <div class="big-info">
  Vous devez être praticien pour gérer vos protocoles
  </div>
{{/if}}