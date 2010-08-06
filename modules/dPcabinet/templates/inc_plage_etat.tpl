{{*
  * $Id$
  * @param $_plage CPlageconsult
  *}}

{{assign var="pct" value=$_plage->_fill_rate}}
{{if $pct gt 100}}
{{assign var="pct" value=100}}
{{/if}}
{{if $pct lt 50}}{{assign var="backgroundClass" value="empty"}}
{{elseif $pct lt 90}}{{assign var="backgroundClass" value="normal"}}
{{elseif $pct lt 100}}{{assign var="backgroundClass" value="booked"}}
{{else}}{{assign var="backgroundClass" value="full"}}
{{/if}} 

<div class="progressBar">
  <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
  <div class="text">
    <a href="#1" onclick="PlageConsult.changePlage({{$_plage->_id}}); return false;">
      {{$_plage->date|date_format:"%A %d"}}
    </a>
  </div>
</div>
