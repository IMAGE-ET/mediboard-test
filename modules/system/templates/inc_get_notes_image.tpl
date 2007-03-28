<a style="float:left;" href="#nothing"
  onmouseover="ObjectTooltip.create(this, '{{$object->_class_name}}', {{$object->_id}}, { mode: 'notes' })"
  onclick="new Note().create('{{$object->_class_name}}', {{$object->_id}});">
  {{if $notes|@count}}
  {{if $high}}
  <img alt="Ecrire une note" src="images/icons/note_red.png" />
  {{else}}
  <img alt="Ecrire une note" src="images/icons/note_green.png" />
  {{/if}}
  {{else}}
  <img alt="Ecrire une note" src="images/icons/note_blue.png" />
  {{/if}}
</a>


          
          
          
        