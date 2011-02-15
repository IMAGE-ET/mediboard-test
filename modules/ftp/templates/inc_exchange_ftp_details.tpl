<script type="text/javascript">
  Main.add(function () {
    Control.Tabs.create('tabs-contenu', true);
  });
</script>

<table class="form">
  <tr>
    <th class="title">
      {{tr}}CExchangeFTP{{/tr}} - {{$exchange_ftp->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      <br />
      - {{mb_value object=$exchange_ftp field="function_name"}} -
    </th>
  </tr>
  <tr>
    <td>
      <ul id="tabs-contenu" class="control_tabs">
        <li><a href="#input">{{mb_title object=$exchange_ftp field="input"}}</a></li>
        <li><a href="#output">{{mb_title object=$exchange_ftp field="output"}}</a></li>
      </ul>
      
      <hr class="control_tabs" />
    
      <div id="input" style="display: none;">
        {{mb_value object=$exchange_ftp field="input" export=true}}
      </div>
      
      <div id="output" style="display: none;">
        {{mb_value object=$exchange_ftp field="output" export=true}}
      </div>
    </td>
  </tr>
  <tr>
    <td style="text-align: center;">
      <a target="blank" href="?m=ftp&a=download_echange&echange_ftp_id={{$exchange_ftp->_id}}&dialog=1&suppressHeaders=1&message=1&acq=1" class="button modify">{{tr}}Download{{/tr}}</a>
    </td>
  </tr>
</table>