<script type="text/javascript">
  function setClose(color){
    window.opener.ColorSelector.set(color);
    window.close();
  }
</script>

<table border="0" cellpadding="0" cellspacing="0">
  {{foreach from=$range item=r}}
  {{if $r==0 || $r==3}}<tr>{{/if}}
  <td>
    <table border="0" cellpadding="0" cellspacing="0">
      {{foreach from=$range item=g}}
       <tr>
        {{foreach from=$range item=b}}
          <td style="background-color: #{{$hex.$r}}{{$hex.$g}}{{$hex.$b}}; width: 12px; height: 12px; cursor: pointer;" 
              onclick="setClose('{{$hex.$r}}{{$hex.$g}}{{$hex.$b}}');" title="{{$hex.$r}}{{$hex.$g}}{{$hex.$b}}"/>
        {{/foreach}}
       </tr>
      {{/foreach}}
    </table>
	</td>
	{{if $r==2 || $r==5}}</tr>{{/if}}
  {{/foreach}}
</table>