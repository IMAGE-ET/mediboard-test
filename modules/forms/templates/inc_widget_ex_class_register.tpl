{{mb_default var=cssStyle value=""}}

{{if "forms"|module_active}}
  {{unique_id var=uid}}
  <script type="text/javascript">
    Main.add(function(){
      ExObject.register("ex_class-{{$uid}}", {
        object_guid: "{{$object->_guid}}",
        event_name: "{{$event_name}}", 
        title: "{{$object}}"
      });
    });
  </script>
  <div id="ex_class-{{$uid}}" style="{{$cssStyle}}"></div>
{{/if}}
