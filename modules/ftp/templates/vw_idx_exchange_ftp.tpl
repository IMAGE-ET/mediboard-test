{{if !$exchange_ftp->_id}}
<script type="text/javascript">
  viewEchange = function(echange_ftp_id) {
    var url = new Url("ftp", "vw_idx_exchange_ftp");
    url.addParam("echange_ftp_id", echange_ftp_id);
    url.requestModal(800, 500);
  }

  function changePage(page) {
    $V(getForm('filterEchange').page, page);
  }
</script>
{{/if}}

<div id="empty_area" style="display: none;"></div>
<table class="main">
  {{if !$exchange_ftp->_id}}
  <!-- Filtres -->
  <tr>
    <td style="text-align: center;">
      <form action="?" name="filterEchange" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
        
        <table class="form">
          <tr>
            <th class="category" colspan="4">Choix de la date d'échange</th>
          </tr>
          <tr>
            <th style="width:50%">{{mb_label object=$exchange_ftp field="_date_min"}}</th>
            <td class="narrow">{{mb_field object=$exchange_ftp field="_date_min" form="filterEchange" register=true}}</td>
            <th class="narrow">{{mb_label object=$exchange_ftp field="_date_max"}}</th>
            <td style="width:50%">{{mb_field object=$exchange_ftp field="_date_max" form="filterEchange" register=true}}</td>
          </tr>
          <tr>
            <th class="category" colspan="4">{{tr}}filter-criteria{{/tr}}</th>
          </tr>
          <tr>
            <th colspan="2">{{mb_label object=$exchange_ftp field="echange_ftp_id"}}</th>
            <td colspan="2">{{mb_field object=$exchange_ftp field="echange_ftp_id"}}</td>
          </tr>
          <tr>
            <th colspan="2">Fonctions</th>
            <td colspan="2">
              <select class="str" name="function">
                <option value="">&mdash; Liste des fonctions</option>
                {{foreach from=$functions item=_function}}
                  <option value="{{$_function}}" {{if $function == $_function}} selected="selected"{{/if}}>
                    {{$_function}}
                  </option>
                {{/foreach}}
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: center">
              <button type="submit" class="search" onclick="$V(getForm('filterEchange').page, 0);">{{tr}}Filter{{/tr}}</button>
            </td>
          </tr>
        </table>
        {{if $total_exchange_ftp != 0}}
          {{mb_include module=system template=inc_pagination total=$total_exchange_ftp current=$page change_page='changePage' jumper='10'}}
        {{/if}}
      </form>
    </td>
  </tr>
  
  <tr>
    <td class="halfPane" rowspan="3">
      <table class="tbl">
        <tr>
          <th class="title" colspan="16">{{tr}}CExchangeFTP{{/tr}}</th>
        </tr>
        <tr>
          <th class="narrow"></th>
          <th class="narrow">{{tr}}Actions{{/tr}}</th>
          <th>{{mb_title object=$exchange_ftp field="echange_ftp_id"}}</th>
          <th>{{mb_title object=$exchange_ftp field="date_echange"}}</th>
          <th>{{mb_title object=$exchange_ftp field="emetteur"}}</th>
          <th>{{mb_title object=$exchange_ftp field="destinataire"}}</th>
          <th>{{mb_title object=$exchange_ftp field="function_name"}}</th>
          <th>{{mb_title object=$exchange_ftp field="input"}}</th>
          <th>{{mb_title object=$exchange_ftp field="output"}}</th>
          <th>{{mb_title object=$exchange_ftp field="response_time"}}</th>
        </tr>
        {{foreach from=$echangesFTP item=_exchange_ftp}}
          <tbody id="echange_{{$_exchange_ftp->_id}}">
            {{mb_include template="inc_exchange_ftp" object=$_exchange_ftp}}
          </tbody>
        {{foreachelse}}
          <tr>
            <td colspan="16" class="empty">
              {{tr}}CExchangeFTP.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
  {{else}}
    <tr>
      <td>
        {{mb_include template="inc_exchange_ftp_details"}}
      </td>
    </tr>
  {{/if}}
</table>