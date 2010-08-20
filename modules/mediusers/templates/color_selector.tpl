<script type="text/javascript">
  var colorGlobal = null;

  function setClose(){
    window.opener.ColorSelector.set(colorGlobal);
    window.close();
  }
  
  function selectColor(color, cell) {
    colorGlobal = color;
    $("color-view").setStyle({backgroundColor: '#'+color});
    $("palette").select('td').each (function (td) {
      td.setStyle({outline: ''});
    });
    $(cell).setStyle({outline: '1px dotted #000'});
  }
</script>
<table border="0" cellpadding="0" cellspacing="4" id="palette">
  <tr>
    <td rowspan="2">
      <table border="0" cellpadding="0" cellspacing="0" style="height: 100%; vertical-aligne: top;">
        {{foreach from=$hex item=h}}
          {{assign var=rgb value="$h$h$h"}}
          <tr>
          	<td style="background-color: #{{$rgb}}; width: 12px; height: 24px; cursor: pointer; {{if $color==$rgb}}outline: 1px dotted #000;{{/if}}" 
              onclick="selectColor('{{$rgb}}', this);"
              ondblclick="selectColor('{{$rgb}}', this); setClose();"
              title="{{$rgb}}"
						/>
					</tr>
        {{/foreach}}
      </table>
    </td>
    <td rowspan="2">
      <table border="0" cellpadding="0" cellspacing="0">
        {{foreach from=$range item=r}}
        {{if $r==0 || $r==3}}<tr>{{/if}}
        <td>
          <table border="0" cellpadding="0" cellspacing="0">
            {{foreach from=$range item=g}}
             <tr>
              {{foreach from=$range item=b}}
                {{assign var=r_color value=$hex.$r}}
                {{assign var=g_color value=$hex.$g}}
                {{assign var=b_color value=$hex.$b}}
                {{assign var=rgb value="$r_color$g_color$b_color"}}
                <td style="background-color: #{{$rgb}}; width: 12px; height: 12px; cursor: pointer; {{if $color==$rgb}}outline: 1px dotted #000;{{/if}}" 
                  onclick="selectColor('{{$rgb}}', this);" 
                  ondblclick="selectColor('{{$rgb}}', this); setClose();"
                  title="{{$rgb}}"
								/>
              {{/foreach}}
             </tr>
            {{/foreach}}
          </table>
      	</td>
      	{{if $r==2 || $r==5}}</tr>{{/if}}
        {{/foreach}}
      </table>
    </td>
    <td style="height: 72px; {{if $color}}background-color: #{{$color}};{{/if}}" id="color-view"></td>
  </tr>
  <tr>
    <td style="vertical-align: bottom;">
      <button type="button" class="tick" onclick="setClose()">{{tr}}Select{{/tr}}</button>
    </td>
  </tr>
</table>
