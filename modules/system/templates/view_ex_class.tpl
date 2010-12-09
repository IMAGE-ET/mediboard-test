{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

var ExClass = {
  id: null,
  edit: function(id) {
    this.id = id || this.id;
    var url = new Url("system", "ajax_edit_ex_class");
    url.addParam("ex_class_id", this.id);
    url.requestUpdate("exClassEditor");
  },
  setEvent: function(select) {
    var form = select.form;
    var parts = $V(select).split(".");
    $V(form.host_class, parts[0]);
    $V(form.event, parts[1]);
  }
};

var ExField = {
  edit: function(id, ex_class_id) {
    var url = new Url("system", "ajax_edit_ex_field");
    url.addParam("ex_field_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exFieldEditor");
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  }
};

var ExFieldSpec = {
  options: {},
  edit: function(specType, prop, className, field, otherFields, ex_field_id){
    var url = new Url("system", "ajax_edit_ex_field_spec");
    url.addParam("spec_type", specType);
    url.addParam("prop", prop);
    url.addParam("class", className);
    url.addParam("field", field);
    url.addParam("other_fields", otherFields, true);
    url.addParam("ex_field_id", ex_field_id);
    url.requestUpdate("fieldSpecEditor");
  }
};

var ExConstraint = {
  edit: function(id, ex_class_id) {
    var url = new Url("system", "ajax_edit_ex_constraint");
    url.addParam("ex_constraint_id", id);
    url.addParam("ex_class_id", ex_class_id);
    url.requestUpdate("exConstraintEditor");
  },
  create: function(ex_class_id) {
    this.edit("0", ex_class_id);
  }
};

Main.add(ExClass.edit.curry({{$ex_class->_id}}));
</script>

<table class="main">
  <tr>
    <td style="width: 20%">
      <button type="button" class="new" onclick="ExClass.edit('0')">
        {{tr}}CExClass-title-create{{/tr}}
      </button>
      
      <table class="main tbl">
        <tr>
          <th>{{mb_title class=CExClass field=host_class}}</th>
          <th>{{mb_title class=CExClass field=event}}</th>
          <th>{{tr}}CExClass-back-fields{{/tr}}</th>
        </tr>
        {{foreach from=$list_ex_class item=_ex_class}}
          <tr>
            <td>
              <a href="#1" onclick="ExClass.edit({{$_ex_class->_id}})">
                <strong>{{mb_value object=$_ex_class field=host_class}}</strong>
              </a>
            </td>
            <td>{{mb_value object=$_ex_class field=event}}</td>
            <td>{{$_ex_class->countBackRefs("fields")}}</td>
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="4">{{tr}}CExClass.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    
    </td>
    <td id="exClassEditor">
      <!-- exClassEditor -->
    </td>
  </tr>
</table>