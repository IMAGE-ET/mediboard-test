<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<title>Les do_*_aed.php qui sont presque pareils</title>
<script type="text/javascript">
function $(str) {
  return document.getElementById(str);
}

function toggle(element) {
  if (element.style.display != 'none')
    element.style.display = 'none';
  else 
    element.style.display = '';
}
</script>
</head>

<body>

<?php
$list = glob ("../*/*/do_*.php");
$n = 0;
foreach ($list as $file) {
  $size = filesize($file);
  if ($size < 380) {
    echo '<a href="javascript:toggle($(\'elt-'.$n.'\'))" style="display: block;">'.str_replace('../modules/', '', $file) . ' : ' . $size . '</a>';
    echo '<blockquote style="display: normal;" id="elt-'.$n.'">' . highlight_file($file, true) . '</blockquote>';
    $n++;
  }
}
echo $n . ' fichiers';
?>
</body>
</html>