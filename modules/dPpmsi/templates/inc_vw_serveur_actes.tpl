{{* $Id: configure.tpl 8820 2010-05-03 13:18:20Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPpmsi
 * @version $Revision: 8820 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>

Main.add(function () {
  {{foreach from=$sejour->_ref_operations item=curr_op}}
    PMSI.loadExportActes('{{$curr_op->_id}}', 'COperation');
  {{/foreach}}
});

</script>

<table class="form">
{{foreach from=$sejour->_ref_operations item=curr_op}}
  <tr>
    <th class="category text" colspan="4">
      Intervention par le Dr {{$curr_op->_ref_chir->_view}}
      &mdash; {{$curr_op->_datetime|date_format:$conf.longdate}}
      &mdash; 
      {{if $curr_op->salle_id}}
        {{$curr_op->_ref_salle->_view}}
      {{else}}
        Salle inconnue
      {{/if}}
    </th>
  </tr>
  <tr>
    <td class="button" colspan="4">
      <button class="{{if $curr_op->_ref_consult_anesth->_id}}print{{else}}warning{{/if}}"
        style="width:11em;" type="button" onclick="printFicheAnesth('{{$curr_op->_ref_consult_anesth->_id}}', '{{$curr_op->_id}}');">
        Fiche d'anesthésie
      </button>
      <button class="print" onclick="printFicheBloc({{$curr_op->operation_id}})">
        Feuille de bloc
      </button>
    </td>
  </tr>
  {{if $curr_op->libelle}}
  <tr>
    <th>Libellé</th>
    <td colspan="3" class="text"><em>{{$curr_op->libelle}}</em></td>
  {{/if}}
  {{foreach from=$curr_op->_ext_codes_ccam item=curr_code}}
  <tr>
    <th>{{$curr_code->code}}</th>
    <td class="text" colspan="3">{{$curr_code->libelleLong}}</td>
  </tr>
  {{/foreach}}
  <tr>
    <th>{{mb_label object=$curr_op field=depassement}}</th>
    <td colspan="3">{{mb_value object=$curr_op field=depassement}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$curr_op field=depassement_anesth}}</th>
    <td colspan="3">{{mb_value object=$curr_op field=depassement_anesth}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$curr_op field=anapath}}</th>
    <td colspan="3">{{mb_value object=$curr_op field=anapath}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$curr_op field=labo}}</th>
    <td colspan="3">{{mb_value object=$curr_op field=labo}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$curr_op field=prothese}}</th>
    <td colspan="3">{{mb_value object=$curr_op field=prothese}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$curr_op field=ASA}}</th>
    <td colspan="3">{{mb_value object=$curr_op field=ASA}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$curr_op field=type_anesth}}</th>
    <td colspan="3">{{mb_value object=$curr_op field=type_anesth}}</td>
  </tr>  
  {{if $curr_op->_ref_consult_anesth->consultation_anesth_id}}
  <tr>
    <td class="button" colspan="4">Consultation de pré-anesthésie le {{$curr_op->_ref_consult_anesth->_ref_plageconsult->date|date_format:"%A %d %b %Y"}}
      avec le Dr {{$curr_op->_ref_consult_anesth->_ref_plageconsult->_ref_chir->_view}}
    </td>
  </tr>
  {{assign var=const_med value=$curr_op->_ref_consult_anesth->_ref_consultation->_ref_patient->_ref_constantes_medicales}}
  <tr>
    <td class="button">Poids</td>
    <td class="button">Taille</td>
    <td class="button">Groupe</td>
    <td class="button">Tension</td>
  </tr>
  <tr>
    <td class="button">{{$const_med->poids}} kg</td>
    <td class="button">{{$const_med->taille}} cm</td>
    <td class="button">{{tr}}CConsultAnesth.groupe.{{$curr_op->_ref_consult_anesth->groupe}}{{/tr}} {{tr}}CConsultAnesth.rhesus.{{$curr_op->_ref_consult_anesth->rhesus}}{{/tr}}</td>
    <td class="button">{{$const_med->_ta_gauche_systole}}/{{$const_med->_ta_gauche_diastole}}</td>
  </tr>
  {{/if}}
  <tr>
    <td class="button" colspan="4">
      <a class="button edit" href="?m=dPpmsi&amp;tab=edit_actes&amp;operation_id={{$curr_op->operation_id}}">
        Modifier les actes
      </a>
    </td>
  </tr>
  <tr>
    <td colspan="4" id="modifActes-{{$curr_op->_id}}">
      {{mb_include template="inc_confirm_actes_ccam"}}
    </td>
  </tr>
  {{if ($conf.dPpmsi.systeme_facturation == "siemens")}}
  <tr>
    <td colspan="4">
      <form name="editOpFrm{{$curr_op->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_planning_aed" />
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="operation_id" value="{{$curr_op->operation_id}}" />
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            <em>Lien S@nté.com</em> : Intervention
          </th>
        </tr>
        <tr>
          <th><label for="_cmca_uf_preselection" title="Choisir une pré-selection pour remplir les unités fonctionnelles">Pré-sélection</label></th>
          <td>
            <select name="_cmca_uf_preselection" onchange="choosePreselection(this)">
              <option value="">&mdash; Choisir une pré-selection</option>
              <option value="ABS|ABSENT">(ABS) Absent</option>
              <option value="AEC|ARRONDI EURO">(AEC) Arrondi Euro</option>
              <option value="AEH|ARRONDI EURO">(AEH) Arrondi Euro</option>
              <option value="AMB|CHIRURGIE AMBULATOIRE">(AMB) Chirurgie Ambulatoire</option>
              <option value="CHI|CHIRURGIE">(CHI) Chirurgie</option>
              <option value="CHO|CHIRURGIE COUTEUSE">(CHO) Chirurgie Coûteuse</option>
              <option value="EST|ESTHETIQUE">(EST) Esthétique</option>
              <option value="EXL|EXL POUR RECUP V4 V5">(EXL) EXL pour récup. v4 v5</option>
              <option value="EXT|EXTERNES">(EXT) Externes</option>
              <option value="MED|MEDECINE">(MED) Médecine</option>
              <option value="PNE|PNEUMOLOGUE">(PNE) Pneumologie</option>
              <option value="TRF|TRANSFERT >48H">(TRF) Transfert > 48h</option>
              <option value="TRI|TRANSFERT >48H">(TRI) Transfert > 48h</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label for="code_uf" title="Choisir un code pour l'unité fonctionnelle">Code d'unité fonct.</label>
          </th>
          <td>
            <input type="text" class="notNull {{$curr_op->_props.code_uf}}" name="code_uf" value="{{$curr_op->code_uf}}" size="10" maxlength="10" />
          </td>
        </tr>
        <tr>
          <th>
            <label for="libelle_uf" title="Choisir un libellé pour l'unité fonctionnelle">Libellé d'unité fonct.</label>
          </th>
          <td>
            <input type="text" class="notNull {{$curr_op->_props.libelle_uf}}" name="libelle_uf" value="{{$curr_op->libelle_uf}}" size="20" maxlength="35" onchange="submitOpForm({{$curr_op->_id}})" />
          </td>
        </tr>
        <tr>
          <td colspan="2" id="updateOp{{$curr_op->operation_id}}"></td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td colspan="4" id="export_COperation_{{$curr_op->_id}}">
    </td>
  </tr>
{{foreachelse}}  
<tr>
  <td>
    <div class="small-info">Aucune intervention pour ce séjour</div>
  </td>
</tr>
{{/foreach}}
</table>