<form name="editFrm-{{$mediuser->_class_name}}-{{$mediuser->_id}}-{{$tag}}" action="?" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="dPsante400" />
  <input type="hidden" name="dosql" value="do_idsante400_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="id_sante400_id" value="{{$id_externe->_id}}" />
  <input type="hidden" name="object_class" value="{{$mediuser->_class_name}}" />
  <input type="hidden" name="object_id" value="{{$mediuser->_id}}" />
  <input type="hidden" name="tag" value="{{$tag}}" />
  <input type="hidden" name="type" value="{{$type}}" />
  <input type="hidden" name="last_update" value="{{$today}}" />
  <input type="{{$type}}" name="id400" value="{{$id_externe->id400}}" />
  <button type="button" class="notext submit" onclick="submitImeds(this.form);">{{tr}}Submit{{/tr}}</button>
</form>