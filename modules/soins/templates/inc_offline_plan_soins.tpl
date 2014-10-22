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

{{if !$prescription->_ref_lines_med_for_plan|@count &&
     !$prescription->_ref_injections_for_plan|@count &&
     !$prescription->_ref_prescription_line_mixes_for_plan|@count &&
     !$prescription->_ref_lines_elt_for_plan|@count}}
  Pas de plan de soins pour {{$sejour}}
  {{mb_return}}
{{/if}}

{{math equation="75/x" x=$colspan assign=width_th}}

{{if $mode_dupa}}
  {{math equation="4+x" x=$colspan assign=th_chap_colspan}}
{{else}}
  {{math equation="3+x" x=$colspan assign=th_chap_colspan}}
{{/if}}

<table class="tbl">
  {{* Entête *}}
  <thead>
    <tr class="clear">
      <td colspan="{{$th_chap_colspan}}">
        <table class="main">
          <tr>
            <th colspan="4">
              <h1 style="page-break-after: auto; text-align: center;">
                <a href="#" onclick="window.print()">
                  Impression du {{$now|date_format:$conf.longdate}} à {{$now|date_format:$conf.time}} <br />
                </a>
              </h1>
            </th>
          </tr>
          <tr>
            <td class="text" style="width: 30%">
              <h2>
                <strong>SERVICE :</strong>

                {{if isset($service|smarty:nodefaults)}}
                  {{mb_value object=$service field=nom}}
                {{elseif $sejour->_ref_curr_affectation->_id}}
                  {{mb_value object=$sejour->_ref_curr_affectation->_ref_service field=nom}}
                {{/if}}
                {{if $sejour->_ref_curr_affectation->_id}}
                  &mdash; <strong>LIT : </strong> {{$sejour->_ref_curr_affectation}}
                {{/if}}
              </h2>
            </td>
            <td style="width: 30%">
              Civilité : {{mb_value object=$patient field=nom}} ({{mb_value object=$patient field=nom_jeune_fille}}) {{mb_value object=$patient field=prenom}}
              <br />
              Né(e) le : {{mb_value object=$patient field=naissance}}
            </td>
            <td>
              NDA : {{$sejour->_NDA}}
              <br />
              Date d'entrée : {{$sejour->entree|date_format:$conf.date}} à {{$sejour->entree|date_format:$conf.time}}
            </td>
            <td class="text">
              {{if $sejour->libelle}}
                Motif : {{$sejour->libelle}}
              {{/if}}
            </td>
          </tr>
        </table>
      </td>
    </tr>
      <th class="title" style="width: 20%" colspan="{{if $mode_dupa}}2{{else}}3{{/if}}">
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
        <th style="width: 10%" rowspan="2">
          Commentaires
        </th>
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
            <th style="width: {{$width_th}}%">{{$_heure|str_pad:2:"0":$smarty.const.STR_PAD_LEFT}}h</th>
          {{/foreach}}
        {{/foreach}}
      {{/if}}
    </tr>
  </thead>
  <tfoot>
    {{* Dernière ligne avec les initiales *}}
    <tr>
      <td colspan="{{if $mode_dupa}}4{{else}}3{{/if}}" style="text-align: right;">Initiales :</td>
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
  <tbody>
    {{* Parcours des lignes *}}

    {{* Lignes de perfusion *}}
    {{if $prescription->_ref_prescription_line_mixes_for_plan|@count}}
      <tr>
        <th colspan="{{$th_chap_colspan}}" class="section">Perfusions</th>
      </tr>
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
      <tr>
        <th colspan="{{$th_chap_colspan}}" class="section">Injections</th>
      </tr>

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
      <tr>
        <th colspan="{{$th_chap_colspan}}" class="section">Médicaments</th>
      </tr>
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
      <tr>
        <th colspan="{{$th_chap_colspan}}" class="section">Elements</th>
      </tr>
      {{foreach from=$prescription->_ref_lines_elt_for_plan item=elements_chap}}
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
      <tr>
        <th colspan="{{$th_chap_colspan}}" class="section">Inscriptions</th>
      </tr>
      {{foreach from=$prescription->_ref_inscriptions_for_plan item=line}}
        {{mb_include module=soins template=inc_offline_vw_line}}
      {{/foreach}}

      {{if $empty_lines}}
        {{foreach from=1|range:$empty_lines item=i}}
          {{mb_include module=soins template=inc_offline_vw_line_empty}}
        {{/foreach}}
      {{/if}}
    {{/if}}
  </tbody>
</table>