{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage cahpp
 * @version $Revision$
 * @author SARL OpenXtrem
*}}

<script type="text/javascript">
  window.parent.onUploadComplete({{$message|smarty:nodefaults|utf8_encode|@json}});
</script>
