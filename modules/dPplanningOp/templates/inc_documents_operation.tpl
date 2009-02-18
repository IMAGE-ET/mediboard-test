{{* $Id: $
  * Manipulation des documents d'une intervention et de son séjour associé
  * @param $operation COperation
	* @param $modelesByOwner array('COperation' => array(), 'CSejour' => array())
  *}}

{{assign var=object value=$operation}}
<div class="documents-{{$object->_class_name}}-{{$object->_id}} praticien-{{$object->chir_id}} mode-collapse" style="min-width: 260px; min-height: 50px; float: left; width: 50%;">
  {{include file="../../dPcompteRendu/templates/inc_widget_documents.tpl" mode="collapse" modelesByOwner=$modelesByOwner.COperation}}
</div>

{{assign var=object value=$operation->_ref_sejour}}
<div class="documents-{{$object->_class_name}}-{{$object->_id}} praticien-{{$object->praticien_id}} mode-collapse" style="min-width: 260px; min-height: 50px; float: left; width: 50%;">
  {{include file="../../dPcompteRendu/templates/inc_widget_documents.tpl" mode="collapse" modelesByOwner=$modelesByOwner.CSejour}}
</div>
