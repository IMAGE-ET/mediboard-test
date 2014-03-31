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

{{if $line->_class == "CPrescriptionLineMedicament" || $line->_class == "CPrescriptionLineElement"}}
  <tr>
    <td class="text" style="vertical-align: top">
      {{if $line->_ref_log_signee->_id}}
        {{$line->_ref_log_signee->date|date_format:$conf.date}} <br />
      {{/if}}
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$line->_ref_praticien}}
      <div class="compact">
        {{foreach from=$line->_ref_prises item=_prise}}
          {{$_prise}} <br />
        {{/foreach}}

        Du {{mb_value object=$line field=debut}} {{if $line->time_debut}}à {{mb_value object=$line field=time_debut}}{{/if}} au
          {{$line->_fin_reelle|date_format:$conf.date}} à {{$line->_fin_reelle|date_format:$conf.time}}
      </div>
    </td>
    <td class="text" style="vertical-align: top">
      <strong>
        {{if $line->_class == "CPrescriptionLineElement"}}
          {{$line}}
        {{else}}
          {{$line->_ucd_view}}
        {{/if}}
      </strong> <br />
      {{if $line->_class == "CPrescriptionLineMedicament" && $line->voie}}
        {{mb_value object=$line field=voie}}
      {{/if}}
      {{if $mode_dupa}}
        <div class="compact">
          <em>{{mb_value object=$line field=commentaire}}</em>
        </div>
      {{/if}}
      <div>
        {{if $line->conditionnel}}
          {{if $line->condition_active}}
            <img src="images/icons/cond.png" />
          {{else}}
            <img src="images/icons/cond_barre.png" />
          {{/if}}
        {{/if}}
        {{if $line->_class == "CPrescriptionLineMedicament" && $line->traitement_personnel}}
          <img src="images/icons/tp.png" />
        {{/if}}
        {{if $line->premedication}}
          <img src="images/icons/premed.png" />
        {{/if}}
      </div>
    </td>
    <td style="vertical-align: top">
      {{if !$mode_dupa}}
        <div class="compact">
          <em>
            {{mb_value object=$line field=commentaire}}
            {{if $line->_class == "CPrescriptionLineMedicament"}}
              <br />
              {{mb_value object=$line field=commentaire_pharma}}
            {{/if}}
          </em>
        </div>
      {{/if}}
    </td>
    {{if $mode_dupa}}
      <td></td>
    {{/if}}

    {{foreach from=$dates item=_date}}
      {{foreach from=$moments item=_moment name=moment}}

        {{assign var="text_align" value="right"}}
        {{if $_date < $now_date || ($_date <= $now_date && $moments_reverse.$_moment < $moments_reverse.$current_moment)}}
          {{assign var="text_align" value="left"}}
        {{/if}}

        {{assign var=administrations_in_hour value=""}}
        {{if isset($line->_administrations_moment.$unite_prise.$_date.$_moment|smarty:nodefaults)}}
          {{assign var=administrations_in_hour value=$line->_administrations_moment.$unite_prise.$_date.$_moment}}
        {{/if}}

        {{* Initialisations *}}
        {{assign var=quantite value="-"}}
        {{assign var=quantite_depart value="-"}}
        {{assign var=heure_reelle value=""}}

        {{* Quantite planifiée *}}
        {{if @$administrations_in_hour.quantite_planifiee}}
          {{assign var=quantite value=$administrations_in_hour.quantite_planifiee}}
        {{elseif @array_key_exists($_moment, @$line->_quantity_by_date_moment.$unite_prise.$_date)}}
          {{assign var=quantite value=$line->_quantity_by_date_moment.$unite_prise.$_date.$_moment.total}}
        {{/if}}

        <td style="vertical-align: top; text-align: center;"
            class="{{if $text_align == "left"}}hatching{{/if}}
                   {{if $smarty.foreach.moment.first}}left_day{{elseif $smarty.foreach.moment.last}}right_day{{/if}}">
          <div class="compact">
            {{if $quantite!="-" || @array_key_exists($_moment, $line->_administrations_moment.$unite_prise.$_date)}}
              {{if !$quantite}}
                {{assign var=quantite value="0"}}
              {{/if}}

              {{if $text_align == "left"}}
                {{if @$administrations_in_hour.quantite_planifiee}}
                  {{if @$administrations_in_hour.quantite}}
                    {{$administrations_in_hour.quantite}}
                  {{else}}
                    0
                  {{/if}}
                  {{if @$administrations_in_hour.quantite != $administrations_in_hour.quantite_planifiee}}
                    /{{$administrations_in_hour.quantite_planifiee}}
                  {{/if}}
                {{else}}
                  {{if @$administrations_in_hour.quantite}}
                    {{$administrations_in_hour.quantite}}
                    {{if $administrations_in_hour.quantite != $quantite}}
                      {{if !$line->sans_planif}}/{{$quantite}}{{/if}}
                    {{/if}}
                  {{elseif $line->_active && !$line->sans_planif}}
                    {{if $quantite}}0/{{$quantite}}{{/if}}
                  {{/if}}
                {{/if}}
              {{else}}
                {{* Que les planifications *}}
                {{if @$administrations_in_hour.quantite_planifiee}}
                  {{$administrations_in_hour.quantite_planifiee}}
                {{elseif $quantite}}
                  {{$quantite}}
                {{/if}}
              {{/if}}
            {{/if}}
          </div>
        </td>
      {{/foreach}}
    {{/foreach}}
  </tr>
{{else}}
  {{* Ligne de perfusion *}}
  {{assign var=nb_lines value=$line->_ref_lines|@count}}
  <tr>
    <td class="text" rowspan="{{$nb_lines}}" style="vertical-align: top">
      {{if $line->_ref_log_signature_prat->_id}}
        {{$line->_ref_log_signature_prat|date_format:$conf.date}} <br />
      {{/if}}
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$line->_ref_praticien}}
      <div class="compact">
        Du {{$line->_debut|date_format:$conf.date}} à {{$line->_debut|date_format:$conf.time}} au
        {{$line->_fin|date_format:$conf.date}} à {{$line->_fin|date_format:$conf.time}}
      </div>
    </td>
    <td class="text" rowspan="{{$nb_lines}}" style="vertical-align: top">
      {{foreach from=$line->_ref_lines item=_line_item}}
        <div>
          {{$_line_item->_ucd_view}}
          <br />
          <strong>
            {{$_line_item->_posologie}}
            {{if $_line_item->_unite_administration && $_line_item->_unite_administration != "ml"}}
              [{{$_line_item->_unite_administration}}]
            {{/if}}
          </strong>
        </div>
      {{/foreach}}
      {{if $line->voie}}
        {{mb_value object=$line field=voie}}
      {{/if}}
      <div>
        {{if $line->conditionnel}}
          {{if $line->condition_active}}
            <img src="images/icons/cond.png" />
          {{else}}
            <img src="images/icons/cond_barre.png" />
          {{/if}}
        {{/if}}
        {{if $line->traitement_personnel}}
          <img src="images/icons/tp.png" />
        {{/if}}
        {{if $line->premedication}}
          <img src="images/icons/premed.png" />
        {{/if}}
      </div>
      {{if $mode_dupa}}
        <hr style="width: 70%; border-color: #aaa; margin: 1px auto;">
        <div class="compact">
          <div style="white-space: nowrap;">
            {{if $line->_frequence}}
              {{if $line->type_line == "perfusion"}}Débit initial: {{/if}}
              {{$line->_frequence}}
              {{if $line->volume_debit && $line->duree_debit && $line->type_line != "oxygene"}}
                <br />
                ({{mb_value object=$line field=volume_debit}} ml en {{mb_value object=$line field=duree_debit}} h)
              {{/if}}
            {{/if}}
          </div>
          <em>{{mb_value object=$line field=commentaire}}</em>
        </div>
      {{/if}}
    </td>
    <td rowspan="{{$nb_lines}}" style="vertical-align: top">
      {{if !$mode_dupa}}
        <div style="white-space: nowrap;" class="compact">
          {{if $line->_frequence}}
            {{if $line->type_line == "perfusion"}}Débit initial: {{/if}}
            {{$line->_frequence}}
            {{if $line->volume_debit && $line->duree_debit && $line->type_line != "oxygene"}}
              <br />
              ({{mb_value object=$line field=volume_debit}} ml en {{mb_value object=$line field=duree_debit}} h)
            {{/if}}
          {{/if}}
          <em>
            {{mb_value object=$line field=commentaire}}
            <br />
            {{mb_value object=$line field=commentaire_pharma}}
          </em>
        </div>
      {{/if}}
    </td>
    {{if $mode_dupa}}
      <td rowspan="{{$nb_lines}}"></td>
    {{/if}}
    {{foreach from=$line->_ref_lines item=_line}}
      {{foreach from=$dates item=_date}}

        {{foreach from=$moments item=_moment name=moment}}
          {{assign var="text_align" value="right"}}
          {{if $_date < $now_date || ($_date <= $now_date && $moments_reverse.$_moment < $moments_reverse.$current_moment)}}
            {{assign var="text_align" value="left"}}
          {{/if}}

          <td style="vertical-align: top; text-align: center;"
              class="{{if $text_align == "left"}}hatching{{/if}}
              {{if $smarty.foreach.moment.first}}left_day{{elseif $smarty.foreach.moment.last}}right_day{{/if}}">
            {{if isset($_line->_administrations_moment.$_date.$_moment|smarty:nodefaults)}}
              {{assign var=nb_adm value=$_line->_administrations_moment.$_date.$_moment}}
            {{else}}
              {{assign var=nb_adm value=""}}
            {{/if}}
            {{assign var=original_dateTime value=""}}
            {{if isset($line->_prises_prevues_moment.$_date.$_moment|smarty:nodefaults)}}
              {{if array_key_exists('real_hour', $line->_prises_prevues_moment.$_date.$_moment)}}
                {{assign var=count_prises value=$line->_prises_prevues_moment.$_date.$_moment.real_hour|@count}}
                {{assign var=nb_prevue value=$_line->_quantite_administration*$count_prises}}
                {{assign var=hour_prevue value=$line->_prises_prevues_moment.$_date.$_moment.real_hour}}
                {{if array_key_exists('original_dateTime', $line->_prises_prevues_moment.$_date.$_moment)}}
                  {{assign var=original_dateTime value=$line->_prises_prevues_moment.$_date.$_moment.original_dateTime}}
                {{/if}}
              {{else}}
                {{assign var=perf_line_id value=$_line->_id}}
                {{if array_key_exists($perf_line_id, $line->_prises_prevues_moment.$_date.$_moment.manual)}}
                  {{assign var=nb_prevue value=$line->_prises_prevues_moment.$_date.$_moment.manual.$perf_line_id}}
                {{else}}
                  {{assign var=nb_prevue value=""}}
                {{/if}}
              {{/if}}
            {{else}}
              {{assign var=nb_prevue value=""}}
              {{assign var=hour_prevue value=""}}
            {{/if}}

            <div class="compact">
              {{if $text_align == "left"}}
                {{if $nb_adm}}
                  {{$nb_adm}}
                {{elseif $nb_prevue && $line->_active}}
                  0
                {{/if}}

                {{if $nb_prevue && $line->_active && ($nb_prevue != $nb_adm)}}/{{$nb_prevue}}{{/if}}
              {{else}}
                {{* Que les planifications *}}
                {{if $nb_prevue && $line->_active}}{{$nb_prevue}}{{/if}}
              {{/if}}
            </div>
          </td>
        {{/foreach}}
      {{/foreach}}
      </tr>
    {{/foreach}}
{{/if}}