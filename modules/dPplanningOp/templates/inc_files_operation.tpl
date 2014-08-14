{{* $Id: inc_files_operation.tpl 7461 2009-11-27 10:08:19Z phenxdesign $
  * Manipulation des fichiers d'une intervention et de son séjour associé
  * @param $operation COperation
  * @param $modelesByOwner array('COperation' => array(), 'CSejour' => array())
  *}}

{{assign var=object value=$operation}}
<div style="float: left; width: 50%;" id="files-{{$object->_guid}}">
  <script type="text/javascript">
    File.register('{{$object->_id}}','{{$object->_class}}', 'files-{{$object->_guid}}');
  </script>
  {{mb_include module="dPfiles" template="yoplet_uploader" object=$object}}
</div>

{{assign var=object value=$operation->_ref_sejour}}
<div style="float: left; width: 50%;" id="files-{{$object->_guid}}">
  <script type="text/javascript">
    File.register('{{$object->_id}}','{{$object->_class}}', 'files-{{$object->_guid}}');
  </script>
</div>