<span class="tooltip-trigger" style="float:left;"
  onmouseover="ObjectTooltip.create(this, { mode: 'objectNotes', params: { object_class: '{{$object->_class_name}}', object_id: '{{$object->_id}}' } })"
  onclick="new Note().create('{{$object->_class_name}}', '{{$object->_id}}');">
  {{if $notes|@count}}
  {{if $high}}
  <img alt="Ecrire une note" src="images/icons/note_red.png" />
  {{else}}
  <img alt="Ecrire une note" src="images/icons/note_green.png" />
  {{/if}}
  {{else}}
  <img alt="Ecrire une note" src="images/icons/note_blue.png" />
  {{/if}}
</span>