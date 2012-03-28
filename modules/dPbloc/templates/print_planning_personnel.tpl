<table class="tbl">
  <tr class="clear">
    <th colspan="4">
      <h1>
        <a href="#" onclick="window.print()">
          Planning du {{$date_min|date_format:$conf.date}} au {{$date_max|date_format:$conf.date}}
        </a>
      </h1>
    </th>
  </tr>
  
  {{foreach from=$planning item=_planning_by_personnel key=personnel_id}}
    {{assign var=personnel value=$personnels.$personnel_id}}
      <tr class="clear">
      <td colspan="4"><h2>{{$personnel->_ref_user}}</h2></td>
      </tr>
    {{foreach from=$_planning_by_personnel item=_planning_by_date key=date}}
      {{if $_planning_by_date|@count}}
        <tr>
          <th colspan="4" class="title">{{$date|date_format:$conf.date}}</th>
        </tr>
        <tr>
          <th class="narrow category">Heure</th>
          <th class="narrow category">Salle</th>
          <th class="category">Chirurgien</th>
          <th class="category">Patient</th>
        </tr>
        {{foreach from=$_planning_by_date item=_operation}}
          <tr>
            <td>
              {{$_operation->_datetime|date_format:$conf.time}}
            </td>
            <td>
              {{$_operation->_ref_salle}}
            </td>
            <td>
              {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}
            </td>
            <td>
              {{$_operation->_ref_patient}}
            </td>
          </tr>
        {{/foreach}}
      {{/if}}
    {{foreachelse}}
      <tr>
        <td colspan="4" class="empty">
        Pas de plage
        </td>
      </tr>
    {{/foreach}}
  {{foreachelse}}
    <tr>
      <td colspan="4" class="empty">
        Aucun planning
      </td>
    </tr>
  {{/foreach}}
</table>