{{mb_script module=dPccam script=CCodageCCAM ajax=true}}
<div id="info_code">
  <table class="layout main">
    {{mb_default var=no_date_found value=""}}
    {{if $no_date_found}}
      <div class="small-info">{{tr}}{{$no_date_found}}{{/tr}}</div>
    {{/if}}
    <tr>
      <th>
        Date d'effet
        <select onchange="CCodageCCAM.refreshCodeFrom('{{$code_ccam}}', this);">
          {{foreach from=$date_versions item=_date_version}}
            <option
                value="{{$_date_version}}" {{if $date_version == $_date_version || $date_demandee == $_date_version}} selected{{/if}}>
              {{$_date_version}}</option>
          {{/foreach}}
        </select>
      </th>
    </tr>
    <tr>
      <td>
        <h2 style="text-align: center;"><strong>{{$code_ccam}}</strong><br/>{{$code_complet->libelleLong}}</h2>
        {{mb_include module=ccam template=inc_show_code_from_date}}
      </td>
    </tr>
  </table>
</div>

