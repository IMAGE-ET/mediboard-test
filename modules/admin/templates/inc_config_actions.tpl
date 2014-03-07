{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


<table class="tbl">
  <tr>
    <th>{{tr}}Classname{{/tr}}</th>
    <th>{{tr}}Action{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
      {{tr}}CUser{{/tr}}
    </td>
    <td>
      <script type="text/javascript">
        CUser = {
          checkSiblings: function () {
            new Url('admin', 'check_siblings').requestModal();
          }
        }
      </script>
      <button class="tick" onclick="CUser.checkSiblings()">{{tr}}mod-admin-action-check_siblings{{/tr}}</button>
    </td>
  </tr>


  <tr>
    <td>
      {{tr}}CUserLog{{/tr}}
    </td>
    <td>
      <script type="text/javascript">
        CUserLog = {
          sanitize: function(form) {
            var url = new Url('admin', 'sanitize_userlogs');

            if (form) {
              url.addNotNullElement(form.purge );
              url.addNotNullElement(form.offset);
              url.addNotNullElement(form.step  );
              url.addElement(form.auto);
            }

            var modal = Control.Modal.stack.last();
            if (modal) {
              url.requestUpdate(modal.container.down('.content'));
            }
            else {
              url.requestModal();
            }

            return false;
          },

          auto: function() {
            if ($('Sanitize_auto').checked) {
              CUserLog.sanitize(document.Sanitize);
            }
          }
        }
      </script>
      <button class="tick" onclick="CUserLog.sanitize()">{{tr}}mod-admin-action-sanitize_logs{{/tr}}</button>
    </td>
  </tr>

</table>