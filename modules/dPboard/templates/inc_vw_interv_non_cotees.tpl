<script type="text/javascript">
  Main.add(function() {
    var form = getForm('changeDate');
    Calendar.regField(form.debut);
    Calendar.regField(form.fin);
  });
</script>
<form name="changeDate" method="get" action="?">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_interv_non_cotees" />
  <table class="form">
    <tr>
      <th colspan="3" class="title">
        Critères de filtre
      </th>
    </tr>
    <tr>
      <td>
        A partir du
        <input type="hidden" name="debut" value="{{$debut}}" class="date notNull" onchange="this.form.submit()"/>
      </td>
      <td>
        jusqu'au
        <input type="hidden" name="fin" value="{{$fin}}" class="date notNull" onchange="this.form.submit()"/>
      </td>
    </tr>
  </table>
</form>

<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      Liste des plages
    </th>
  </tr>
  {{if $plages|@count || $hors_plage|@count}}
    {{foreach from=$plages item=_plage}}
      <tr>
        <th colspan="3">
          Plage du {{mb_value object=$_plage field=date}}
          {{if $all_prats}}
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_plage->_ref_chir}}
          {{/if}}
        </th>
      </tr>
      {{foreach from=$_plage->_ref_operations item=_operation}}
        {{assign var=codes_ccam value=$_operation->codes_ccam}}
        <tr>
          <td class="narrow">
            {{assign var=patient value=$_operation->_ref_patient}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
              {{$patient}}
            </span>
          </td>
          <td>
            <a
              {{if $_operation->salle_id}}
                href="?m=dPsalleOp&tab=vw_operation&op={{$_operation->_id}}&salle={{$_operation->salle_id}}&date={{$_plage->date}}"
              {{/if}}>
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
                {{$_operation}} ({{$_operation->_actes_non_cotes}} acte(s) non coté(s))
              </span>
            </a>
          </td>
          <td>
            {{if $_operation->libelle}}
              {{$_operation->libelle}}
            {{else}}
              {{" ; "|implode:$_operation->_codes_ccam}}
            {{/if}}
          </td>
        </tr>
      {{/foreach}}
    {{/foreach}}
    {{if $hors_plage|@count}}
      <tr>
        <th>Hors plages</th>
        {{foreach from=$hors_plage item=_operation}}
          {{assign var=codes_ccam value=$_operation->codes_ccam}}
          <tr>
            <td>
              <a
                {{if $_operation->salle_id}}
                  href="?m=dPsalleOp&tab=vw_operation&op={{$_operation->_id}}&salle={{$_operation->salle_id}}&date={{$_plage->date}}"
                {{/if}}>
                <span onmouseover="ObjectTooltip.createEx(this, '{{$_operation->_guid}}')">
                  {{$_operation}} ({{$_operation->_actes_non_cotes}} acte(s) non coté(s))
                </span>
              </a>
            </td>
          </tr>
        {{/foreach}}
      </tr>
    {{/if}}
  {{else}}
    <tr>
      <td class="empty">{{tr}}COperation.none_non_cotee{{/tr}}</td>
    </tr>
  {{/if}}
</table>