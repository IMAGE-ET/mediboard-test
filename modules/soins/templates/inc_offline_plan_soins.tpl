{{*
 * $Id$
 *  
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{assign var=patient value=$sejour->_ref_patient}}
{{assign var=prescription value=$sejour->_ref_prescription_sejour}}

{{math equation="75/x" x=$colspan assign=width_th}}

{{if $mode_dupa}}
  {{math equation="4+x" x=$colspan assign=th_chap_colspan}}
{{elseif $mode_lite}}
  {{math equation="5+x" x=$colspan assign=th_chap_colspan}}
{{else}}
  {{math equation="3+x" x=$colspan assign=th_chap_colspan}}
{{/if}}

<table class="tbl" {{if $page_break}}style="page-break-after: always;"{{/if}}>
  {{* Entête *}}
  <thead>
    <tr class="clear">
      <td colspan="{{$th_chap_colspan}}" class="button">
        <h3>
          <a href="#" onclick="window.print()">
            {{$patient}}
           </a>
        </h3>
      </td>
    </tr>
    <tr class="clear">
      <td colspan="{{$th_chap_colspan}}">
        <table class="main">
          <tr>
            <td class="text" style="width: 20%; vertical-align: top;">
              Impression du {{$now|date_format:$conf.longdate}} à {{$now|date_format:$conf.time}} <br />

              <strong>SERVICE :</strong>

              {{if isset($service|smarty:nodefaults)}}
                {{mb_value object=$service field=nom}}
              {{elseif $sejour->_ref_curr_affectation->_id}}
                {{mb_value object=$sejour->_ref_curr_affectation->_ref_service field=nom}}
              {{/if}}
              {{if $sejour->_ref_curr_affectation->_id}}
                &mdash; <strong>LIT : </strong> {{$sejour->_ref_curr_affectation}}
              {{/if}}
            </td>
            <td style="width: 23%; vertical-align: top;" class="text">
              Civilité : {{mb_value object=$patient field=civilite}} {{mb_value object=$patient field=nom}} ({{mb_value object=$patient field=nom_jeune_fille}}) {{mb_value object=$patient field=prenom}}
               ({{$patient->_annees}} ans)
               {{if $patient->_ref_constantes_medicales->poids}}({{$patient->_ref_constantes_medicales->poids}} kg){{/if}}
               {{if $patient->_ref_constantes_medicales->taille}}({{$patient->_ref_constantes_medicales->taille}} cm){{/if}}
              <br />
              Né(e) le : {{mb_value object=$patient field=naissance}}
            </td>
            <td style="width: 23%; vertical-align: top;" class="text">
              NDA : {{$sejour->_NDA}}
              <br />
              {{if $sejour->libelle}}
                Motif : {{$sejour->libelle|spancate:25:"...":true}}
                <br />
              {{/if}}
              Date d'entrée : {{$sejour->entree|date_format:$conf.date}} à {{$sejour->entree|date_format:$conf.time}}
              <br />
              Date de sortie : {{$sejour->sortie|date_format:$conf.date}} à {{$sejour->sortie|date_format:$conf.time}}
            </td>
            <td class="text" style="vertical-align: top">
              {{if $sejour->_ref_last_operation->_id && !$sejour->_ref_last_operation->annulee}}
                {{assign var=last_op value=$sejour->_ref_last_operation}}
                {{assign var=last_op_id value=$last_op->_id}}

                Date d'intervention : {{$last_op->_datetime_best|date_format:$conf.date}} à {{$last_op->_datetime_best|date_format:$conf.time}}
                (J{{$sejour->_jour_op.$last_op_id.jour_op}})
                <br />
                Côté : {{mb_value object=$last_op field=cote}}
                <br />
                Libellé : {{$last_op->libelle|spancate:75:"...":true}}
              {{/if}}
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <th class="title" style="width: 20%" colspan="{{if $mode_dupa}}2{{elseif $mode_lite}}4{{else}}3{{/if}}">
        {{tr}}soins.dupa_prescriptions{{/tr}}
      </th>
      {{if $mode_dupa}}
      <th class="title" style="width: 10%" colspan="2">
        {{tr}}soins.dupa_evolutions{{/tr}}
      </th>
      {{/if}}
      <th class="title" colspan="{{$colspan}}">
        ADMINISTRATION
      </th>
    </tr>
    <tr>
      <th class="text" style="width: 7%" rowspan="2">
        Date signature
      </th>
      <th class="text" rowspan="2" style="width: 8%">
        Libellé médicament <br />
        Posologie <br />
        Commentaires <br />
      </th>
      {{if $mode_dupa}}
        <th class="text" rowspan="2">
          Poursuite du traitement ou arrêt
        </th>
        <th class="text" rowspan="2">
          Date/heure <br />
          Nom <br />
          Signature médecin
        </th>
      {{else}}
        <th style="width: {{if $mode_lite}}5{{else}}10%{{/if}}" rowspan="2">
          Commentaires
        </th>
        {{if $mode_lite}}
          <th style="width: 5%" rowspan="2">
            Dernière <br />
            adminis- <br />
            tration
          </th>
        {{/if}}
      {{/if}}
      {{if $moments|@count == 4}}
        {{foreach from=$dates item=_date}}
          <th colspan="4">
            {{$_date|date_format:"%a"|substr:0:1|strtoupper}} <br />
            {{$_date|date_format:"%d/%m"}}
          </th>
        {{/foreach}}
      {{else}}
        {{foreach from=$dates_plan_soin item=_date key=_date_key}}
          <th colspan="{{$_date|@count}}">
            {{$_date_key|date_format:"%a"|substr:0:1|strtoupper}} <br />
            {{$_date_key|date_format:"%d/%m"}}
          </th>
        {{/foreach}}
      {{/if}}
    </tr>
    <tr>
      {{if $moments|@count == 4}}
        {{foreach from=$dates item=_date}}
          <th style="width: {{$width_th}}%">M</th>
          <th style="width: {{$width_th}}%">M</th>
          <th style="width: {{$width_th}}%">S</th>
          <th style="width: {{$width_th}}%">N</th>
        {{/foreach}}
      {{else}}
        {{foreach from=$dates_plan_soin item=_dates}}
          {{foreach from=$_dates item=_heure}}
            <th style="width: {{$width_th}}%">{{$_heure}}h</th>
          {{/foreach}}
        {{/foreach}}
      {{/if}}
    </tr>
  </thead>

  {{* Parcours des lignes *}}

  {{* Lignes de perfusion *}}
    {{if $prescription->_ref_prescription_line_mixes_for_plan|@count}}
      <tbody class="line_print">
        <tr>
          <th colspan="{{$th_chap_colspan}}" class="section">Perfusions / Aérosols / Oxygènes</th>
        </tr>
      </tbody>
      {{foreach from=$prescription->_ref_prescription_line_mixes_for_plan item=line}}
        {{mb_include module=soins template=inc_offline_vw_line}}
      {{/foreach}}

      {{if $empty_lines}}
        {{foreach from=1|range:$empty_lines item=i}}
          {{mb_include module=soins template=inc_offline_vw_line_empty}}
        {{/foreach}}
      {{/if}}
    {{/if}}

    {{if $prescription->_ref_injections_for_plan|@count}}
      <tbody class="line_print">
        <tr>
          <th colspan="{{$th_chap_colspan}}" class="section">Injections</th>
        </tr>
      </tbody>
      {{* Lignes de médicaments (injectables) *}}
      {{foreach from=$prescription->_ref_injections_for_plan item=_cat_ATC key=_key_cat_ATC}}
        {{foreach from=$_cat_ATC item=lines}}
          {{foreach from=$lines key=unite_prise item=line}}
            {{mb_include module=soins template=inc_offline_vw_line}}
          {{/foreach}}
        {{/foreach}}
      {{/foreach}}

      {{if $empty_lines}}
        {{foreach from=1|range:$empty_lines item=i}}
          {{mb_include module=soins template=inc_offline_vw_line_empty}}
        {{/foreach}}
      {{/if}}
    {{/if}}

    {{if $prescription->_ref_lines_med_for_plan|@count}}
      <tbody class="line_print">
        <tr>
          <th colspan="{{$th_chap_colspan}}" class="section">Médicaments</th>
        </tr>
      </tbody>
      {{* Lignes de médicaments *}}
      {{foreach from=$prescription->_ref_lines_med_for_plan item=_cat_ATC key=_key_cat_ATC}}
        {{foreach from=$_cat_ATC item=lines}}
          {{foreach from=$lines key=unite_prise item=line}}
            {{mb_include module=soins template=inc_offline_vw_line}}
          {{/foreach}}
        {{/foreach}}
      {{/foreach}}

      {{if $empty_lines}}
        {{foreach from=1|range:$empty_lines item=i}}
          {{mb_include module=soins template=inc_offline_vw_line_empty}}
        {{/foreach}}
      {{/if}}
    {{/if}}

    {{* Lignes d'éléments *}}
    {{if $prescription->_ref_lines_elt_for_plan|@count}}
      {{if !$mode_lite}}
        <tbody class="line_print">
          <tr>
            <th colspan="{{$th_chap_colspan}}" class="section">Elements</th>
          </tr>
        </tbody>
      {{/if}}
      {{foreach from=$prescription->_ref_lines_elt_for_plan item=elements_chap key=chap}}
        {{if $mode_lite}}
          <tbody class="line_print">
            <tr>
              <th colspan="{{$th_chap_colspan}}" class="section">{{tr}}CPrescription._chapitres.{{$chap}}{{/tr}}</th>
            </tr>
          </tbody>
        {{/if}}
        {{foreach from=$elements_chap item=elements_cat}}
          {{foreach from=$elements_cat item=_element}}
            {{foreach from=$_element key=unite_prise item=line}}
              {{mb_include module=soins template=inc_offline_vw_line}}
            {{/foreach}}
          {{/foreach}}
        {{/foreach}}
      {{/foreach}}

      {{if $empty_lines}}
        {{foreach from=1|range:$empty_lines item=i}}
          {{mb_include module=soins template=inc_offline_vw_line_empty}}
        {{/foreach}}
      {{/if}}
    {{/if}}

    {{* Inscriptions *}}
    {{if $prescription->_ref_inscriptions_for_plan|@count}}
      {{assign var=unite_prise value="aucune_prise"}}
      <tbody class="line_print">
        <tr>
          <th colspan="{{$th_chap_colspan}}" class="section">Inscriptions</th>
        </tr>
      </tbody>
      {{foreach from=$prescription->_ref_inscriptions_for_plan item=line}}
        {{mb_include module=soins template=inc_offline_vw_line}}
      {{/foreach}}

      {{if $empty_lines}}
        {{foreach from=1|range:$empty_lines item=i}}
          {{mb_include module=soins template=inc_offline_vw_line_empty}}
        {{/foreach}}
      {{/if}}
    {{/if}}

  <tfoot>
  {{* Dernière ligne avec les initiales *}}
  <tr>
    <td colspan="{{if $mode_dupa || $mode_lite}}4{{else}}3{{/if}}" style="text-align: right;">Initiales :</td>
    {{assign var=prescription_id value=$prescription->_id}}
    {{foreach from=$dates_plan_soin item=_moments key=_date}}
      {{foreach from=$_moments item=_moment name=moment}}
        <td class="{{if $smarty.foreach.moment.first}}left_day{{elseif $smarty.foreach.moment.last}}right_day{{/if}}">
          {{if @isset($initiales.$prescription_id.$_date.$_moment|smarty:nodefaults)}}
            {{foreach from=$initiales.$prescription_id.$_date.$_moment item=_initiales name=initiales}}
              {{$_initiales}}
              {{if !$smarty.foreach.initiales.last}} - {{/if}}
            {{/foreach}}
          {{/if}}
        </td>
      {{/foreach}}
    {{/foreach}}
  </tr>
  </tfoot>
</table>