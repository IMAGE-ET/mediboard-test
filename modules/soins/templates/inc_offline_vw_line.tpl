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

<tbody class="line_print">
{{if $line->_class == "CPrescriptionLineMedicament" || $line->_class == "CPrescriptionLineElement"}}
  <tr>
    <td class="text" style="vertical-align: top">
      {{if $line->date_signature}}
        {{$line->date_signature|date_format:$conf.date}} <br />
      {{/if}}
      {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$line->_ref_praticien}}
      <div class="compact" style="white-space: nowrap !important;">
        <div>Du {{$line->_debut_reel|date_format:$conf.date}} à {{$line->_debut_reel|date_format:$conf.time}}</div>
        <div>
          {{if $line->date_arret && $line->time_arret}}
            au {{$line->date_arret|date_format:$conf.date}} à {{$line->time_arret|date_format:$conf.time}}
          {{elseif $line->duree && $line->unite_duree}}
            au {{$line->_fin_reelle|date_format:$conf.date}} à {{$line->_fin_reelle|date_format:$conf.time}}
          {{else}}
            {{assign var=_line_chapitre value=$line->_chapitre}}
            {{if $line->_class == "CPrescriptionLineMedicament" || $conf.dPprescription.CCategoryPrescription.$_line_chapitre.fin_sejour}}
              à Fin du séjour
            {{else}}
              au {{$line->_fin_reelle|date_format:$conf.date}} à {{$line->_fin_reelle|date_format:$conf.time}}
            {{/if}}
          {{/if}}
        </div>
      </div>
    </td>
    <td class="text" style="vertical-align: top">
      <strong>
        {{if $line->_class == "CPrescriptionLineElement"}}
          {{$line}}
        {{else}}
          {{$line->_ucd_view}} {{if @in_array($line->code_cis, $risques_cis)}}<span style="color: red">(A risque)</span>{{/if}}
        {{/if}}
      </strong>
      <br />
      {{if $line->_class == "CPrescriptionLineMedicament" && $line->voie}}
        {{mb_value object=$line field=voie}}
      {{/if}}
      <div class="compact">
        {{if $line->sans_planif && $line->_class == "CPrescriptionLineMedicament"}}
          {{if $line->max_par_prise}}
            {{mb_label object=$line field=max_par_prise}} : {{mb_value object=$line field=max_par_prise}} {{mb_value object=$line field=unite_prise}}<br />
          {{/if}}
          {{if $line->max_par_jour}}
            {{mb_label object=$line field=max_par_jour}} : {{mb_value object=$line field=max_par_jour}} {{mb_value object=$line field=unite_prise}}<br />
          {{/if}}
          {{if $line->delay_prise}}
            {{mb_label object=$line field=delay_prise}} : {{mb_value object=$line field=delay_prise}} h<br />
          {{/if}}
          {{if $line->default_quantity}}
            {{mb_label object=$line field=default_quantity}} : {{mb_value object=$line field=default_quantity}} {{mb_value object=$line field=unite_prise}}<br />
          {{/if}}
        {{else}}
          {{if is_numeric($unite_prise)}}
            {{$line->_prises_for_plan.$unite_prise}}
          {{else}}
            {{foreach from=$line->_prises_for_plan.$unite_prise item=_prise key=prise_truc}}
              {{$_prise}}<br />
            {{/foreach}}
          {{/if}}
        {{/if}}
      </div>
      {{if $mode_dupa}}
        <div class="compact">
          <em>{{mb_value object=$line field=commentaire}}</em>
        </div>
      {{/if}}
      <div>
        {{if $line->conditionnel}}
          {{mb_include module=prescription template=line/inc_line_cond_icon condition=$line->_current_active}}
        {{/if}}
        {{if $line->_class == "CPrescriptionLineMedicament" && $line->traitement_personnel}}
          <img src="images/icons/tp.png" />
        {{/if}}
        {{if $line->premedication}}
          <span class="texticon texticon-premed">Prémed</span>
        {{/if}}
        {{if $line->_class == "CPrescriptionLineMedicament" && $line->_ref_produit->_is_stupefiant}}
          <span class="texticon texticon-stup" title="Produit Stupéfiant">Stup</span>
        {{/if}}
      </div>
    </td>
    <td style="vertical-align: top" class="text">
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
    {{if $mode_lite}}
      <td class="text">
        {{assign var=last_adm value=$line->_ref_last_administration}}
        {{if $last_adm->_id && ($last_adm->unite_prise == $unite_prise || $last_adm->prise_id == $unite_prise || $unite_prise == "aucune_prise")}}
          <div class="compact">
            {{$line->_ref_last_administration->quantite}}
            {{if $line instanceof CPrescriptionLineElement}}
              {{assign var=chap value=$line->_chapitre}}
              {{$conf.dPprescription.CCategoryPrescription.$chap.unite_prise}}
            {{elseif $line->inscription}}
              {{$line->_ref_produit->libelle_unite_presentation}}
            {{else}}
              {{$line->_ref_produit->_unite_administration}}
            {{/if}}
            <br />
            le {{$last_adm->dateTime|date_format:$conf.date}} <br />
            à {{$last_adm->dateTime|date_format:$conf.time}}
          </div>
        {{elseif !$last_adm->_id|@count}}
          <div class="empty">{{tr}}CAdministration.none_short{{/tr}}</div>
        {{/if}}
      </td>
    {{/if}}
    {{assign var=chap_sans_planif value=$line->_chapitre}}
    {{if (($line->_class == "CPrescriptionLineMedicament" && !$line->substituted) ||
        ($line->_class != "CPrescriptionLineMedicament" && $conf.dPprescription.CCategoryPrescription.$chap_sans_planif.alert_sans_planif) ) &&
         !$line->sans_planif && !$line->inscription && !$line->_count_planifications &&
         ($line->signee || $line->_class != "CPrescriptionLineMedicament" || !"dPprescription CPrescription show_unsigned_med_msg"|conf:"CGroups-$g")}}
      <td colspan="{{$colspan}}" class="left_day right_day">
        <div class="small-warning">
          <strong>{{tr}}CPrescription-no_planif{{/tr}}</strong>
        </div>
      </td>
    {{elseif !$line->inscription && !$line->signee}}
      <td colspan="{{$colspan}}" class="left_day right_day">
        <div class="small-warning">
          <strong>{{tr}}CPrescription-no_signee{{/tr}}</strong>
        </div>
      </td>
    {{else}}
      {{foreach from=$dates_plan_soin item=_moments key=_date}}
        {{foreach from=$_moments item=_moment name=moment}}

          {{assign var="text_align" value="right"}}
          {{if $_date < $now_date || ($_date <= $now_date && $_moment < $current_moment)}}
            {{assign var="text_align" value="left"}}
          {{/if}}

          {{assign var=administrations_in_hour value=""}}
          {{if @isset($line->_administrations_moment.$unite_prise.$_date.$_moment|smarty:nodefaults)}}
            {{assign var=administrations_in_hour value=$line->_administrations_moment.$unite_prise.$_date.$_moment}}
          {{/if}}

          {{* Initialisations *}}
          {{assign var=quantite value="-"}}
          {{assign var=quantite_depart value="-"}}
          {{assign var=heure_reelle value=""}}

          {{* Quantite planifiée *}}
          {{if @$administrations_in_hour.quantite_planifiee}}
            {{assign var=quantite value=$administrations_in_hour.quantite_planifiee}}
          {{elseif @isset($line->_quantity_by_date_moment.$unite_prise.$_date.$_moment|smarty:nodefaults)}}
            {{assign var=quantite value=$line->_quantity_by_date_moment.$unite_prise.$_date.$_moment.total}}
          {{/if}}

          {{* Hatching de la case si arrêt et que la case est dans les bornes de l'arrêt *}}
          {{assign var=hatching_arret value=""}}
          {{assign var=moment_to_hour value=$postes_to_hour.$_moment}}
          {{if $line->date_arret && $line->time_arret}}
            {{if "$_date $moment_to_hour:00:00" >= "`$line->date_arret` `$line->time_arret`"}}
              {{assign var=hatching_arret value="1"}}
            {{/if}}
          {{/if}}

          {{if $line->_fin_reelle}}
            {{if "$_date $moment_to_hour:00:00" >= $line->_fin_reelle}}
              {{assign var=hatching_arret value="1"}}
            {{/if}}
          {{/if}}

          <td style="vertical-align: top; text-align: center;"
              class="{{if $text_align == "left" || $hatching_arret}}hatching{{/if}}
                     {{if $smarty.foreach.moment.first}}left_day{{elseif $smarty.foreach.moment.last}}right_day{{/if}}">
            <div class="compact">
              {{if $quantite!="-" || @array_key_exists($_moment, $line->_administrations_moment.$unite_prise.$_date)}}
                {{if !$quantite}}
                  {{assign var=quantite value="0"}}
                {{/if}}

                {{if $text_align == "left" || $mode_lite}}
                  {{if @$administrations_in_hour.quantite_planifiee}}
                    {{if @$administrations_in_hour.quantite}}
                      {{$administrations_in_hour.quantite}}
                    {{else}}
                      0
                    {{/if}}{{if @$administrations_in_hour.quantite != $administrations_in_hour.quantite_planifiee}}
                      / {{$administrations_in_hour.quantite_planifiee}}
                    {{/if}}
                  {{else}}
                    {{if @isset($administrations_in_hour.quantite|smarty:nodefaults)}}
                      {{if $administrations_in_hour.quantite == 0}}
                        a
                      {{else}}
                        {{$administrations_in_hour.quantite}}
                      {{/if}}
                      {{if $administrations_in_hour.quantite != $quantite}}
                        {{if !$line->sans_planif}} / {{$quantite}}{{/if}}
                      {{/if}}
                    {{elseif (!$line->conditionnel || (isset($line->_active_dates.$_date|smarty:nodefaults) && $line->_active_dates.$_date)) && !$line->sans_planif}}
                      {{if $quantite}}0 / {{$quantite}}{{/if}}
                    {{/if}}
                  {{/if}}
                {{else}}
                  {{* Si plusieurs administration, pas d'affichage de la quantité, juste une flêche pour indiquer la présence de multiples planifications *}}
                  {{if @$line->_quantity_by_date_moment.$unite_prise.$_date.$_moment.nb_adm > 1}}
                    <div>
                      <img src="style/mediboard/images/buttons/down.png" />
                    </div>
                  {{else}}
                    {{* Que les planifications *}}
                    {{if isset($administrations_in_hour.quantite|smarty:nodefaults) && $administrations_in_hour.quantite == 0}}
                      a/{{/if}}{{if @$administrations_in_hour.quantite_planifiee}} {{$administrations_in_hour.quantite_planifiee}}{{elseif $quantite}}{{$quantite}}
                    {{/if}}
                  {{/if}}
                {{/if}}
              {{/if}}
            </div>
          </td>
        {{/foreach}}
      {{/foreach}}
    {{/if}}
  </tr>
{{else}}
  {{* Ligne de perfusion *}}
  {{foreach from=$line->_ref_lines item=_line_item name=lines_items}}
    <tr>
      <td class="text {{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}" style="vertical-align: top;">
        {{if $smarty.foreach.lines_items.first}}
          {{if $line->date_signature}}
            {{$line->date_signature|date_format:$conf.date}} <br />
          {{/if}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$line->_ref_praticien}}
          <div class="compact" style="white-space: nowrap !important;">
            <div>Du {{$line->_debut|date_format:$conf.date}} à {{$line->_debut|date_format:$conf.time}}</div>
            <div>
              {{if $line->date_arret && $line->time_arret}}
                au {{$line->date_arret|date_format:$conf.date}} à {{$line->time_arret|date_format:$conf.time}}
              {{else}}
                au {{$line->_fin|date_format:$conf.date}} à {{$line->_fin|date_format:$conf.time}}
              {{/if}}
            </div>
          </div>
        {{/if}}
      </td>
      <td class="text {{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}" style="vertical-align: top;">
        {{$_line_item->_ucd_view}}
        {{if $_line_item->_ref_produit->_is_stupefiant}}
          <span class="texticon texticon-stup" title="Produit Stupéfiant">Stup</span>
        {{/if}}
        <br />
        <strong>
          {{$_line_item->_posologie}}
          {{if $_line_item->_unite_administration && $_line_item->_unite_administration != "ml"}}
            [{{$_line_item->_unite_administration}}]
          {{/if}}
        </strong>
        {{if $line->voie}}
          <br />
          {{mb_value object=$line field=voie}}
        {{elseif $line->type_line == "aerosol" && $line->interface}}
          <br />
          {{mb_label object=$line field=interface}} {{tr}}CPrescriptionLineMix.interface.{{$line->interface}}{{/tr}}
        {{/if}}

        {{if $line->type == "PCA"}}
          <div class="compact">
            {{if $line->mode_bolus}}
              <br />
              {{mb_value object=$line field=mode_bolus}}
            {{/if}}
            {{if $line->quantite_dose_max && $line->duree_dose_max}}
              <br />
              Dose max {{$line->quantite_dose_max}} mg en {{$line->duree_dose_max}} h
            {{/if}}
            {{if $line->mode_bolus != "sans_bolus"}}
              <br />
              {{if $line->dose_bolus}}
                {{mb_label object=$line field=dose_bolus}} {{mb_value object=$line field=dose_bolus}} mg &mdash;
              {{/if}}
              {{if $line->periode_interdite}}
                {{mb_label object=$line field=periode_interdite}} {{mb_value object=$line field=periode_interdite}} min
              {{/if}}
            {{/if}}
          </div>
        {{/if}}
        <div>
          {{if $line->conditionnel}}
            {{mb_include module=prescription template=line/inc_line_cond_icon condition=$line->_current_active}}
          {{/if}}
          {{if $line->traitement_personnel}}
            <img src="images/icons/tp.png" />
          {{/if}}
          {{if $line->premedication}}
            <span class="texticon texticon-premed">Prémed</span>
          {{/if}}
        </div>
        {{if $smarty.foreach.lines_items.first || $mode_dupa}}
          {{if $mode_lite}}
            <br />
          {{elseif $mode_dupa}}
            <hr style="width: 70%; border-color: #aaa; margin: 1px auto;">
              <div class="compact">
                <div style="white-space: nowrap;">
          {{/if}}
          {{if $line->_frequence}}
            {{if $line->type_line == "perfusion"}}Débit initial : {{/if}}
            {{$line->_frequence}}
            {{if $line->volume_debit && $line->duree_debit && $line->type_line != "oxygene"}}
              <br />
              ({{mb_value object=$line field=volume_debit}} ml en {{mb_value object=$line field=duree_debit}} h)
            {{/if}}
            {{if $line->type_line == "perfusion" && $line->_last_variation->debit}}
              <br />
              Dernier débit : {{$line->_last_variation->debit}} ml/h
            {{/if}}
          {{/if}}
        {{/if}}
        {{if $mode_dupa}}
            </div>
            <em>{{mb_value object=$line field=commentaire}}</em>
          </div>
        {{/if}}
      </td>
      <td style="vertical-align: top;" class="text {{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}">
        {{if !$mode_dupa && $smarty.foreach.lines_items.first}}
          <div class="compact">
            <em>
              {{mb_value object=$line field=commentaire}}
              <br />
              {{mb_value object=$line field=commentaire_pharma}}
            </em>
          </div>
        {{/if}}
      </td>
      {{if $mode_dupa}}
        <td class="{{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}"></td>
      {{/if}}
      {{if $mode_lite}}
        <td class="text {{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}" style="vertical-align: top;">
          {{assign var=last_adm value=$_line_item->_ref_last_administration}}
          {{if $last_adm->_id}}
            <div class="compact">
              {{$last_adm->quantite}} {{$last_adm->unite_prise}} <br />
              le {{$last_adm->dateTime|date_format:$conf.date}} <br />
              à {{$last_adm->dateTime|date_format:$conf.time}}
            </div>
          {{elseif !$last_adm->_id}}
            <div class="empty">{{tr}}CAdministration.none_short{{/tr}}</div>
          {{/if}}
        </td>
      {{/if}}
      {{if ($line->mode_bolus != "bolus") &&
           ($line->continuite != "continue" || "CAppUI::conf"|static_call:"dPprescription CPrescription perf_continue_manuelle":"CGroups-$g" == '0') &&
           !$line->_count_planifications}}
        <td colspan="{{$colspan}}" class="left_day right_day {{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}">
          <div class="small-warning">
            <strong>{{tr}}CPrescription-no_planif{{/tr}}</strong>
          </div>
        </td>
      {{elseif !$line->signature_prat}}
        <td colspan="{{$colspan}}" class="left_day right_day {{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}">
          <div class="small-warning">
            <strong>{{tr}}CPrescription-no_signee{{/tr}}</strong>
          </div>
        </td>
      {{else}}
        {{foreach from=$dates_plan_soin item=_moments key=_date}}
          {{foreach from=$_moments item=_moment name=moment}}
            {{assign var="text_align" value="right"}}
            {{if $_date < $now_date || ($_date <= $now_date && $_moment < $current_moment)}}
              {{assign var="text_align" value="left"}}
            {{/if}}

            {{* Hatching de la case si arrêt et que la case est dans les bornes de l'arrêt *}}
            {{assign var=hatching_arret value=""}}
            {{assign var=moment_to_hour value=$postes_to_hour.$_moment}}

            {{if $line->date_arret && $line->time_arret}}
              {{if "$_date $moment_to_hour:00:00" >= "`$line->date_arret` `$line->time_arret`"}}
                {{assign var=hatching_arret value="1"}}
              {{/if}}
            {{/if}}
            {{if $line->date_retrait && $line->time_retrait}}
              {{if "$_date $moment_to_hour:00:00" >= "`$line->date_retrait` `$line->time_retrait`"}}
                {{assign var=hatching_arret value="1"}}
              {{/if}}
            {{/if}}
            {{if $line->_fin_reelle}}
              {{if "$_date $moment_to_hour:00:00" >= $line->_fin_reelle}}
                {{assign var=hatching_arret value="1"}}
              {{/if}}
            {{/if}}

            <td style="vertical-align: top; text-align: center;"
                class="{{if $text_align == "left" || $hatching_arret}}hatching{{/if}}
                {{if $smarty.foreach.moment.first}}left_day{{elseif $smarty.foreach.moment.last}}right_day{{/if}}
                {{if $smarty.foreach.lines_items.first}}first_perf{{/if}} {{if $smarty.foreach.lines_items.last}}last_perf{{/if}}">
              <div class="compact">
                {{if isset($_line_item->_administrations_moment.$_date.$_moment|smarty:nodefaults)}}
                  {{assign var=nb_adm value=$_line_item->_administrations_moment.$_date.$_moment}}
                {{else}}
                  {{assign var=nb_adm value=""}}
                {{/if}}
                {{assign var=original_dateTime value=""}}
                {{if isset($line->_prises_prevues_moment.$_date.$_moment|smarty:nodefaults)}}
                  {{if array_key_exists('real_hour', $line->_prises_prevues_moment.$_date.$_moment)}}
                    {{assign var=count_prises value=$line->_prises_prevues_moment.$_date.$_moment.real_hour|@count}}
                    {{assign var=nb_prevue value=$_line_item->_quantite_administration*$count_prises}}
                    {{assign var=hour_prevue value=$line->_prises_prevues_moment.$_date.$_moment.real_hour}}
                    {{if array_key_exists('original_dateTime', $line->_prises_prevues_moment.$_date.$_moment)}}
                      {{assign var=original_dateTime value=$line->_prises_prevues_moment.$_date.$_moment.original_dateTime}}
                    {{/if}}
                  {{else}}
                    {{assign var=perf_line_id value=$_line_item->_id}}
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

                {{if $text_align == "left" || $mode_lite}}
                  {{if $nb_adm}}
                    {{$nb_adm}}
                  {{elseif $nb_prevue && (!$line->conditionnel || (isset($line->_active_dates.$_date|smarty:nodefaults) && $line->_active_dates.$_date))}}
                    0
                  {{/if}}
                  {{if $nb_prevue && (!$line->conditionnel || (isset($line->_active_dates.$_date|smarty:nodefaults) && $line->_active_dates.$_date)) && ($nb_prevue != $nb_adm)}} / {{$nb_prevue}}{{/if}}
                {{else}}
                  {{* Que les planifications *}}
                  {{if $nb_prevue && (!$line->conditionnel || (isset($line->_active_dates.$_date|smarty:nodefaults) && $line->_active_dates.$_date))}} {{$nb_prevue}}{{/if}}
                {{/if}}
              </div>
            </td>
          {{/foreach}}
        {{/foreach}}
      </tr>
    {{/if}}
  {{/foreach}}
{{/if}}
</tbody>