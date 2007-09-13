<table class="main">
  <tr>
    <td class="halfPane">
      <table class="tbl">
        <tr>
          <th colspan="3" class="title">
            Identifiants labo
          </th>
        </tr>
        <tr>
          <th class="category">Praticiens</th>
          <th class="category">Code4</th>
          <th class="category">Code9</th>
        </tr>
        {{foreach from=$listPraticiens item="praticien"}}
        <tr>
          <td>{{$praticien.prat->_view}}</td>
          <td>
            <form name="editFrmPrat-{{$praticien.prat->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="m" value="dPsante400" />
              <input type="hidden" name="dosql" value="do_idsante400_aed" />
              <input type="hidden" name="del" value="0" />
              {{if $praticien.code4->_id}}
              <input type="hidden" name="id_sante400_id" value="{{$praticien.code4->_id}}" />
              {{/if}}
              <input type="hidden" name="object_class" value="CMediusers" />
              <input type="hidden" name="object_id" value="{{$praticien.prat->_id}}" />
              <input type="hidden" name="tag" value="labo code4" />
              <input type="hidden" name="last_update" value="{{$today}}" /> 
              {{mb_field object=$praticien.code4 field="id400"}}
              <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
            </form>
          </td>
          <td>
            <form name="editFrmPrat-{{$praticien.prat->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="m" value="dPsante400" />
              <input type="hidden" name="dosql" value="do_idsante400_aed" />
              <input type="hidden" name="del" value="0" />
              {{if $praticien.code9->_id}}
              <input type="hidden" name="id_sante400_id" value="{{$praticien.code9->_id}}" />
              {{/if}}
              <input type="hidden" name="object_class" value="CMediusers" />
              <input type="hidden" name="object_id" value="{{$praticien.prat->_id}}" />
              <input type="hidden" name="tag" value="labo code9" />
              <input type="hidden" name="last_update" value="{{$today}}" /> 
              {{mb_field object=$praticien.code9 field="id400"}}
              <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
            </form>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>