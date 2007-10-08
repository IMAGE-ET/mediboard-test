<table class="main">
  <tr>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="2" class="title">
            Praticiens
          </th>
        </tr>
        {{foreach from=$praticiens item="curr_prat"}}
        <tr>
          <th>{{$curr_prat->_view}}</th>
          <td>
            <form name="editFrmPrat-{{$curr_prat->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="m" value="dPsante400" />
              <input type="hidden" name="dosql" value="do_idsante400_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="id_sante400_id" value="{{$curr_prat->_ref_last_id400->_id}}" />
              <input type="hidden" name="object_class" value="CMediusers" />
              <input type="hidden" name="object_id" value="{{$curr_prat->_id}}" />
              <input type="hidden" name="tag" value="sherpa group:{{$g}}" />
              <input type="hidden" name="last_update" value="{{$today}}" />
              {{mb_field object=$curr_prat->_ref_last_id400 field="id400"}}
              <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
            </form>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>

    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="2" class="title">
            Services
          </th>
        </tr>
        {{foreach from=$services item="curr_service"}}
        <tr>
          <th class="category" colspan="2">{{$curr_service->_view}}</th>
        </tr>
        {{foreach from=$curr_service->_ref_chambres item="curr_chambre"}}
        {{foreach from=$curr_chambre->_ref_lits item="curr_lit"}}
        <tr>
          <th>{{$curr_chambre->_view}} {{$curr_lit->_view}}</th>
          <td>
            <form name="editFrmLit-{{$curr_lit->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="m" value="dPsante400" />
              <input type="hidden" name="dosql" value="do_idsante400_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="id_sante400_id" value="{{$curr_lit->_ref_last_id400->_id}}" />
              <input type="hidden" name="object_class" value="CLit" />
              <input type="hidden" name="object_id" value="{{$curr_lit->_id}}" />
              <input type="hidden" name="tag" value="sherpa group:{{$g}}" />
              <input type="hidden" name="last_update" value="{{$today}}" />
              {{mb_field object=$curr_lit->_ref_last_id400 field="id400"}}
              <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
            </form>
          </td>
        </tr>
        {{/foreach}}
        {{/foreach}}
        {{/foreach}}
      </table>
    </td>

    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="2" class="title">
            Etablissements externes
          </th>
        </tr>
        {{foreach from=$listEtabExternes item="etab_externe"}}
        <tr>
          <th>{{$etab_externe->_view}}</th>
          <td>
            <form name="editFrmEtab-{{$etab_externe->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
              <input type="hidden" name="m" value="dPsante400" />
              <input type="hidden" name="dosql" value="do_idsante400_aed" />
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="id_sante400_id" value="{{$etab_externe->_ref_last_id400->_id}}" />
              <input type="hidden" name="object_class" value="CEtabExterne" />
              <input type="hidden" name="object_id" value="{{$etab_externe->_id}}" />
              <input type="hidden" name="tag" value="sherpa group:{{$g}}" />
              <input type="hidden" name="last_update" value="{{$today}}" />
              {{mb_field object=$etab_externe->_ref_last_id400 field="id400"}}
              <button type="submit" class="notext submit">{{tr}}Submit{{/tr}}</button>
            </form>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>

  </tr>
</table>