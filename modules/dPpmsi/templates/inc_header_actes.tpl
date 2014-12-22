{{*
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="main tbl">
  <tr>
    <th class="title" colspan="4">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$subject->_guid}}')">
        {{tr}}{{$subject->_class}}{{/tr}}
      </span>
      par le Dr <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$subject->_ref_chir}}</span>
      &mdash; {{$subject->_datetime|date_format:$conf.longdate}}
      {{if $subject instanceof COperation && $subject->salle_id}}
        &mdash;
        {{$subject->_ref_salle}}
      {{elseif $subject instanceof COperation && !$subject->salle_id}}
        &mdash;
        Salle inconnue
      {{/if}}

    </th>
  </tr>
  {{if $subject instanceof COperation}}
    <tr>
      <th class="section narrow" colspan="2">Libellé</th>
      <th class="section narrow">Chirurgien</th>
      <th class="section narrow">Anesthésiste</th>
    </tr>
    <tr>
      <td class="text" colspan="2" style="text-align: center"><em>{{$subject->libelle}}</em></td>
      <td class="text" style="text-align: center">
        <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$subject->_ref_chir}}</span>
      </td>
      <td class="text" style="text-align: center">
        <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$subject->_ref_anesth}}</span>
      </td>
    </tr>
    <tr>
      <th class="section narrow" colspan="2">{{mb_label object=$subject field=depassement}}</th>
      <th class="section narrow" colspan="2">{{mb_label object=$subject field=depassement_anesth}}</th>
    </tr>
    <tr>
      <td class="text" colspan="2" style="text-align: center">{{mb_value object=$subject field=depassement}}</td>
      <td class="text" colspan="2" style="text-align: center">{{mb_value object=$subject field=depassement_anesth}}</td>
    </tr>

    <tr>
      <th class="section narrow">{{mb_label object=$subject field=anapath}}</th>
      <th class="section narrow">{{mb_label object=$subject field=labo}}</th>
      <th class="section narrow">{{mb_label object=$subject field=prothese}}</th>
      <th class="section narrow">{{mb_label object=$subject field=ASA}}</th>
    </tr>
    <tr>
      <td style="text-align: center">{{mb_value object=$subject field=anapath}}</td>
      <td style="text-align: center">{{mb_value object=$subject field=labo}}</td>
      <td style="text-align: center">{{mb_value object=$subject field=prothese}}</td>
      <td style="text-align: center">{{mb_value object=$subject field=ASA}}</td>
    </tr>
    <tr>
      <th class="section narrow" colspan="2">{{mb_label object=$subject field=type_anesth}}</th>
      <th class="section narrow" colspan="2">Consultation de pré-anesthésie</th>
    </tr>
    <tr>
      <td colspan="2" style="text-align: center">{{mb_value object=$subject field=type_anesth}}</td>
      <td class="button" colspan="2">
          {{if $subject->_ref_consult_anesth->consultation_anesth_id}}
            <span onmouseover="ObjectTooltip.createEx(this, 'CConsultAnesth-{{$subject->_ref_consult_anesth->consultation_anesth_id}}')">
            {{$subject->_ref_consult_anesth->_ref_consultation->_ref_plageconsult->date|date_format:$conf.longdate}}
            avec le Dr
            {{$subject->_ref_consult_anesth->_ref_consultation->_ref_plageconsult->_ref_chir}}
            </span>
          {{/if}}
      </td>
    </tr>
    <tr>
      <td class="button" colspan="4">
        <button class="{{if $subject->_ref_consult_anesth->_id}}print{{else}}warning{{/if}}"
                style="width:11em;" type="button" onclick="PMSI.printFicheAnesth('{{$subject->_ref_consult_anesth->_id}}', '{{$subject->_id}}');">
          Fiche d'anesthésie
        </button>
        <button class="print" onclick="PMSI.printFicheBloc({{$subject->operation_id}})">
          Feuille de bloc
        </button>
      </td>
    </tr>
  {{else}}
    <tr>
      <th class="section">{{mb_label object=$subject field=motif}}</th>
      <th class="section">{{mb_label object=$subject field=examen}}</th>
      <th class="section">{{mb_label object=$subject field=rques}}</th>
      <th class="section">{{mb_label object=$subject field=conclusion}}</th>
    </tr>
    <tr>
      <td style="vertical-align: top">{{mb_value object=$subject field=motif}}</td>
      <td>{{mb_value object=$subject field=examen}}</td>
      <td>{{mb_value object=$subject field=rques}}</td>
      <td>{{mb_value object=$subject field=conclusion}}</td>
    </tr>
  {{/if}}
</table>