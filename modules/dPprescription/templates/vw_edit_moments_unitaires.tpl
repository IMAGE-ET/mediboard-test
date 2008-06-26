<table class="main">
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Libelle</th>
          <th>Principal</th>
          <th>Heure</th>
        </tr>
        {{foreach from=$moments item=momentsChap key=chap}}
        <tr>
          <th colspan="3">
            {{$chap}}
          </th>
        </tr>
        {{foreach from=$momentsChap item=moment}}
        <tr>
          <td>{{$moment->libelle}}</td>
          <td>
            <form name="changePrincipalMoment-{{$moment->_id}}" method="post" action="">
              <input type="hidden" name="dosql" value="do_moment_unitaire_aed" />
              <input type="hidden" name="m" value="dPprescription" />
              <input type="hidden" name="moment_unitaire_id" value="{{$moment->_id}}" />
              {{mb_field object=$moment field="principal" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg');"}}
            </form>
          </td>
          <td>
            <form name="changePrincipalMoment-{{$moment->_id}}" method="post" action="">
              <input type="hidden" name="dosql" value="do_moment_unitaire_aed" />
              <input type="hidden" name="m" value="dPprescription" />
              <input type="hidden" name="moment_unitaire_id" value="{{$moment->_id}}" />
              <select name="_heure" onchange="submitFormAjax(this.form, 'systemMsg');">
			          <option value="">-</option>
			          {{foreach from=$hours item=_hour}}
			          <option value="{{$_hour}}" {{if $moment->_heure == $_hour}}selected="selected"{{/if}}>{{$_hour}}</option>
			          {{/foreach}}
			        </select>
			        h
            </form>
          </td>
        </tr>
        {{/foreach}}
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>