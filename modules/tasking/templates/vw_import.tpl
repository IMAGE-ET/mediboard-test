{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    // Autocomplete des users
    var form = getForm("import");
    var element = form.elements._view;
    var url = new Url("system", "ajax_seek_autocomplete");
    url.addParam("object_class", "CMediusers");
    url.addParam("input_field", element.name);
    url.autoComplete(element, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field, selected) {
        var id = selected.get("id");
        $V(form.elements.user_id, id);
        if ($V(element) == "") {
          $V(element, selected.down('.view').innerHTML);
        }
      }
    });
  });
</script>

<form name="import" action="?m=tasking&amp;tab=vw_import" enctype="multipart/form-data" method="post">
  <input type="hidden" name="dosql" value="do_rtm_import" />
  <input type="hidden" name="user_id" value="" />
  <h3>{{tr}}XML-Import{{/tr}}</h3>

  <div style="text-align: center;">
    <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
    <input type="file" name="datafile" size="40" accept="application/xml">
    <br />
    {{tr}}CTaskingTicket-list-of{{/tr}} : <input type="text" name="_view" class="autocomplete"
           value="{{if $user}}{{$user->_view}}{{else}}&mdash; Tous les utilisateurs{{/if}}" />
    <button type="button" class="cancel notext" onclick="$V(this.form.elements.user_id, ''); $V(this.form.elements._view, '');"></button>
    <br />
    <button type="submit" class="submit">{{tr}}Import{{/tr}}</button>
  </div>
</form>