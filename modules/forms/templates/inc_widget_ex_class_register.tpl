{{mb_default var=cssStyle value=""}}
{{mb_default var=form_name value=""}}

{{if "forms"|module_active}}
  {{unique_id var=uid}}
  <script>
    Main.add(function(){
      ExObject.register("ex_class-{{$uid}}", {
        object_guid: "{{$object->_guid}}",
        event_name: "{{$event_name}}",
        title: "{{$object->_view|JSAttribute}}",
        form_name: "{{$form_name}}"
      });
    });
  </script>
  <div id="ex_class-{{$uid}}" style="{{$cssStyle}}"></div>
{{/if}}
