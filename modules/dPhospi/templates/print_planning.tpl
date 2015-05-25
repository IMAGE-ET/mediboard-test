<!-- $Id$ -->

<table class="tbl">
  <tr class="clear">
    <th colspan="16">
      <h1>
        <a href="#" onclick="window.print()">
          Planning du {{$filter->_date_min|date_format:$conf.datetime}}
          au {{$filter->_date_max|date_format:$conf.datetime}} 
          : {{$total}} séjour(s)
          <br />
          Filtrés sur : {{mb_label class=CSejour field=$filter->_horodatage}}
        </a>
      </h1>
    </th>
  </tr>
  {{foreach from=$listDays key=key_day item=curr_day}}
    {{if $filter->_by_date}}
      {{assign var=nb_sejour value=0}}
      {{foreach from=$curr_day key=key_prat item=curr_prat}}
        {{math equation='x+y' assign=nb_sejour x=$nb_sejour y=$curr_prat.sejours|@count}}
      {{/foreach}}
      <tr class="clear">
        <td colspan="{{if $prestation->_id}}17{{else}}16{{/if}}">
          <h2>
            <strong>
              {{$key_day|date_format:"%a %d %b %Y"}}
            </strong>- {{mb_label class=CSejour field=$filter->_horodatage}} x {{$nb_sejour}}
          </h2>
        </td>
      </tr>
    {{/if}}
    {{foreach from=$curr_day key=key_prat item=curr_prat name=_plages}}
    {{assign var="praticien" value=$curr_prat.praticien}}
    {{if !$filter->_by_date}}
      <tr class="clear">
        <td colspan="{{if $prestation->_id}}17{{else}}16{{/if}}">
          <h2>
            <strong>
              {{$key_day|date_format:"%a %d %b %Y"}}
              - Dr {{$praticien->_view}}
            </strong>
            - {{mb_label class=CSejour field=$filter->_horodatage}}
            x {{$curr_prat.sejours|@count}}
          </h2>
        </td>
      </tr>
    {{/if}}

    {{if !$filter->_by_date || $smarty.foreach._plages.first}}
      <tr>
        {{assign var=colspan_sejour value=6}}
        {{if $prestation->_id}}{{assign var=colspan_sejour value=$colspan_sejour+1}}{{/if}}
        {{if $filter->_notes}}{{assign var=colspan_sejour value=$colspan_sejour+1}}{{/if}}
        {{if $filter->_by_date}}{{assign var=colspan_sejour value=$colspan_sejour+1}}{{/if}}
        <th colspan="{{$colspan_sejour}}"><strong>Séjour</strong></th>
        <th colspan="5"><strong>Intervention(s)</strong></th>
        <th colspan="5"><strong>Patient</strong></th>
      </tr>
      <tr>
        {{if $filter->_by_date}}
          <th>{{mb_title class=CSejour field=praticien_id}}</th>
        {{/if}}
        <th>{{mb_title class=CSejour field=$filter->_horodatage}}</th>
        <th>Type</th>
        <th>Dur.</th>
        <th>Conv.</th>
        <th>Chambre</th>
        {{if $prestation->_id}}
          <th>{{mb_value object=$prestation field=nom}}</th>
        {{/if}}
        <th>Remarques</th>
        {{if $filter->_notes}}
          <th>Notes</th>
        {{/if}}
        <th>Date</th>
        <th>Dénomination</th>
        <th>Côté</th>
        <th>Bilan</th>
        <th>Remarques</th>
        <th>Nom / Prenom</th>
        <th>Naissance (Age)</th>
        {{if $filter->_coordonnees}}
        <th>Adresse</th>
        <th>Tel</th>
        {{/if}}
        <th>Remarques</th>
      </tr>
    {{/if}}
    {{assign var=horodatage value=$filter->_horodatage}}
    {{foreach from=$curr_prat.sejours item=curr_sejour}}
    <tr>
      {{if $filter->_by_date}}
        <td>{{$curr_sejour->_ref_praticien}}</td>
      {{/if}}
      <td>{{$curr_sejour->$horodatage|date_format:$conf.time}}</td>
      <td>
        {{if !$curr_sejour->facturable}}
        <strong>NF</strong>
        {{/if}}

        {{$curr_sejour->type|truncate:1:""|capitalize}}
      </td>
      <td>{{$curr_sejour->_duree_prevue}} j</td>
      <td class="text">{{$curr_sejour->convalescence|nl2br}}</td>
      <td class="text">
        {{mb_include module=hospi template=inc_placement_sejour sejour=$curr_sejour}}

        ({{tr}}chambre_seule.{{$curr_sejour->chambre_seule}}{{/tr}})
      </td>
      {{if $prestation->_id}}
        <td>
          {{mb_include module=hospi template=inc_vw_liaisons_prestation liaisons=$curr_sejour->_liaisons_for_prestation}}
        </td>
      {{/if}}
      <td class="text compact">{{$curr_sejour->rques|nl2br}}</td>
      {{if $filter->_notes}}
        <td class="text compact">
          {{if $curr_sejour->_ref_notes|@count}}
            <ul>
              {{foreach from=$curr_sejour->_ref_notes item=_note}}
                <li>
                  <span style="color: #333">{{$_note->libelle}} :</span> {{$_note->text}}
                </li>
              {{/foreach}}
            </ul>
          {{/if}}
        </td>
      {{/if}}
      <td class="text">
        {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
          {{$curr_operation->_datetime|date_format:"%d/%m/%Y"}}
          {{if $curr_operation->time_operation != "00:00:00"}}
            à {{$curr_operation->time_operation|date_format:$conf.time}}
          {{/if}}
          <br />
        {{/foreach}}
      </td>
      <td class="text">
        {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
          <ul style="padding-left: 0px;">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_operation->_guid}}');">
          {{if $curr_operation->libelle}}
            <em>[{{$curr_operation->libelle}}]</em>
            <br />
          {{else}}
          {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
            <em>{{$curr_code->code}}</em>
            {{if $filter->_ccam_libelle}}
              : {{$curr_code->libelleLong|truncate:60:"...":false}}
              <br/>
            {{else}}
              ;
            {{/if}}
          </span>
          {{/foreach}}
          {{/if}}
          </ul>
        {{/foreach}}
      </td>
      <td>
        {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
          {{$curr_operation->cote|truncate:1:""|capitalize}}
          <br />
        {{/foreach}}
      </td>
      <td class="text">
        {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
          {{$curr_operation->examen|nl2br}}
          <br />
        {{/foreach}}
      </td>
      <td class="text compact">
        {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
          {{$curr_operation->rques|nl2br}}
          <br />
        {{/foreach}}
      </td>

      {{assign var=patient value=$curr_sejour->_ref_patient}}
      <td class="text">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
          {{$patient->_view}}
        </span>
      </td>
      <td class="text">
        {{mb_value object=$patient field="naissance"}}
        <br />({{$patient->_age}})
      </td>

      {{if $filter->_coordonnees}}
      <td>
        {{mb_value object=$patient field=adresse}}
        <br />
        {{mb_value object=$patient field=cp}}
        {{mb_value object=$patient field=ville}}
      </td>
      <td>
        {{mb_value object=$patient field=tel}}
        <br />
        {{mb_value object=$patient field=tel2}}
      </td>
      {{/if}}

      <td class="text compact">
        {{$patient->rques|nl2br}}
      </td>
    </tr>
    {{/foreach}}
    {{/foreach}}
  {{/foreach}}
</table>