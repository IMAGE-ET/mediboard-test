{{* $Id: inc_files_operation.tpl 7461 2009-11-27 10:08:19Z phenxdesign $
  * Manipulation des fichiers d'une intervention et de son séjour associé
  * @param $operation COperation
  * @param $modelesByOwner array('COperation' => array(), 'CSejour' => array())
  *}}

{{mb_include_script module="dPcabinet" script="file"}}

{{assign var=object value=$operation}}
<div style="float: left; width: 50%;" id="files-{{$object->_guid}}">
  <script type="text/javascript">
    File.register('{{$object->_id}}','{{$object->_class_name}}', 'files-{{$object->_guid}}');
  </script>
</div>

{{assign var=object value=$operation->_ref_sejour}}
<div style="float: left; width: 50%;" id="files-{{$object->_guid}}">
  <script type="text/javascript">
    File.register('{{$object->_id}}','{{$object->_class_name}}', 'files-{{$object->_guid}}');
  </script>
</div>