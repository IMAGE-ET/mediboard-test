{{mb_default var=cssStyle value=""}}

{{if "forms"|module_active}}
  {{unique_id var=uid}}
  <script>
    ExObject.registerFormItem("{{$object->_id}}", "ex_class-{{$uid}}");
  </script>
  <div id="ex_class-{{$uid}}" style="{{$cssStyle}}"></div>
{{/if}}
