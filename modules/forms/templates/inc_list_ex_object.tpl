{{assign var=self_guid value="$reference_class-$reference_id $target_element $detail $ex_class_id"}}
{{assign var=self_guid value=$self_guid|md5}}
{{assign var=self_guid value="guid_$self_guid"}}

{{if !$print}}
<script type="text/javascript">

ExObject.refreshSelf['{{$self_guid}}'] = function(start){
  start = start || 0;
  var options = {start: start};
  var form = getForm('filter-ex_object');
  
  if (form) {
    options = Object.extend(getForm('filter-ex_object').serialize(true), {
      start: start, 
      a: 'ajax_list_ex_object'
    });
  }
  
  ExObject.loadExObjects('{{$reference_class}}', '{{$reference_id}}', '{{$target_element}}', '{{$detail}}', '{{$ex_class_id}}', options);
}

</script>

{{/if}}

{{if $step && $detail < 3}}
  {{assign var=align value=null}}
  
  {{if $detail > 1}}
    {{assign var=align value=left}}
  {{/if}}
  
  {{mb_include module=system template=inc_pagination change_page="ExObject.refreshSelf.$self_guid" total=$total current=$start step=$step align=$align}}
{{/if}}

{{* FULL DETAIL = ALL *}}
{{if $detail == 3}}
  {{mb_include module=forms template=inc_list_ex_object_detail_3}}

{{* FULL DETAIL = COLUMNS *}}
{{elseif $detail == 2}}
  {{mb_include module=forms template=inc_list_ex_object_detail_2}}

{{* MEDIUM DETAIL *}}
{{elseif $detail == 1}}
  {{mb_include module=forms template=inc_list_ex_object_detail_1}}
  
{{elseif $detail == 0.5}}
  {{mb_include module=forms template=inc_list_ex_object_detail_05}}
  
{{* NO DETAIL *}}
{{else}}
  {{mb_include module=forms template=inc_list_ex_object_detail_0}}
{{/if}}