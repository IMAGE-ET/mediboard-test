{{if $occupation < 80}}
  {{assign var="backgroundClass" value="normal"}}
{{elseif $occupation < 100}}
  {{assign var="backgroundClass" value="booked"}}
{{else}}
  {{assign var="backgroundClass" value="full"}}
{{/if}} 

{{if $occupation}}
<script type="text/javascript">
  OccupationServices.tauxOccupation = {{$occupation}};
</script>
<div class="progressBar">
  <div class="bar {{$backgroundClass}}" style="width: {{$pct}}%;"></div>
  <div class="text" style="text-align: center">{{$occupation|string_format:"%.0f"}} %</div>
</div>
{{else}}
<div class="empty">{{tr}}Unavailable{{/tr}}</div>
{{/if}}