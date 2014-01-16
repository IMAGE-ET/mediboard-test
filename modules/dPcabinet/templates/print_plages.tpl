<!-- $Id$ -->

<script>
  Main.add(window.print);
</script>

{{assign var=main_colspan value="7"}}
{{assign var=patient_colspan value="2"}}

{{if $filter->_coordonnees}}
  {{math equation="x+2" x=$main_colspan assign=main_colspan}}
  {{math equation="x+2" x=$patient_colspan assign=patient_colspan}}
{{elseif $filter->_telephone}}
  {{math equation="x+1" x=$main_colspan assign=main_colspan}}
  {{math equation="x+1" x=$patient_colspan assign=patient_colspan}}
{{/if}}

{{if $show_lit}}
  {{math equation="x+1" x=$main_colspan assign=main_colspan}}
  {{math equation="x+1" x=$patient_colspan assign=patient_colspan}}
{{/if}}

<table class="tbl">
  <tr class="clear">
    <th colspan="{{$main_colspan}}">
      <h1 class="no-break">
        <a href="#" onclick="window.print()">
          {{if $filter->plageconsult_id}}
            Plage du {{mb_value object=$filter->_ref_plageconsult field=date}}
            de {{mb_value object=$filter->_ref_plageconsult field=debut}} à {{mb_value object=$filter->_ref_plageconsult field=fin}}
          {{else}}
            Planning du {{mb_value object=$filter field=_date_min}}
            {{if $filter->_date_min != $filter->_date_max}}
              au {{mb_value object=$filter field=_date_max}}
            {{/if}}
          {{/if}}
        </a>
      </h1>
    </th>
  </tr>
  
  {{foreach from=$listPlage item=curr_plage}}
    <tr class="clear">
      <td colspan="{{$main_colspan}}" class="text">
        <h2>
          <span style="float: right">
            {{$curr_plage->_ref_consultations|@count}} consultation(s)
          </span>

          {{$curr_plage->date|date_format:$conf.longdate}}
          - 
          Dr {{$curr_plage->_ref_chir->_view}}
          -
          {{$curr_plage->libelle}}
        </h2>
      </td>
    </tr>
    
    <tr>
      <th rowspan="2" colspan="2"><b>Heure</b></th>
      <th colspan="{{$patient_colspan}}"><b>Patient</b></th>
      <th colspan="3"><b>Consultation</b></th>
    </tr>
    
    <tr>
      <th style="width: 20%;">Nom / Prénom</th>
      {{if $filter->_coordonnees}}
        <th style="width: 15%;">Adresse</th>
        <th class="narrow">Tel</th>
      {{elseif $filter->_telephone}}
        <th class="narrow">Tel</th>
      {{/if}}
      <th class="narrow">Age</th>
      {{if $show_lit}}
        <th class="narrow">Lit</th>
      {{/if}}
      <th style="width: 20%;">Motif</th>
      <th style="width: 20%;">Remarques</th>
      <th>Durée</th>
    </tr>
  
    {{foreach from=$curr_plage->listPlace item =_place}}
      {{if $_place.consultations|@count}}
        {{foreach from=$_place.consultations item=curr_consult}}
          <tbody class="hoverable">
            <tr>
              {{assign var=categorie value=$curr_consult->_ref_categorie}}
              <td rowspan="2" {{if !$categorie->_id}}colspan="2"{{/if}}
                style="text-align: center; {{if $curr_consult->premiere}}background-color:#eaa;{{/if}}">
                {{$_place.time|date_format:$conf.time}}
              </td>
              {{mb_include template=inc_print_plages_line}}
            </tr>
          </tbody>
        {{/foreach}}
      {{elseif $filter->_non_pourvues}}
        <tbody class="hoverable">
          <tr>
            <td colspan="2"style="text-align: center;">
              {{$_place.time|date_format:$conf.time}}
            </td>
            <td colspan="{{math equation="x-2" x=$main_colspan}}"></td>
          </tr>
        </tbody>
      {{/if}}
    {{foreachelse}}
      <tr>
        <td colspan="{{$main_colspan}}" class="empty">
          {{tr}}CConsultation.none{{/tr}}
        </td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CPlageconsult.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
