<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>Les SVN properties</title>

<style type="text/css">
	body {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
	}
</style>

</head>

<body>

<?php
$list = array_merge(
  glob ("../*"),
  glob ("../*/*"),
  glob ("../*/*/*"),
  glob ("../*/*/*/*")
);

$issues = array();
foreach ($list as $path) {
	if (is_dir($path) ||
	    strpos($path, 'templates_c/') !== false ||
	    strpos($path, 'libpkg/') !== false ||
	    strpos($path, 'lib/') !== false ||
	    strpos($path, 'tmp/') !== false ||
			strpos($path, 'locales/') !== false) continue;
	
	$info = pathinfo($path);
	
  $ext = strtolower($info['extension']);
  if (!in_array($ext, array('php', 'js', 'css', 'sql', 'tpl'))) continue;
	//if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'zip', 'gz', 'bz', 'xml', 'xsd', 'csv', 'htm'))) continue;
	
  $dir = dirname($path);
  $file = basename($path);
	
	$svn_file = "$dir/.svn/prop-base/$file.svn-base";
	if (!is_file($svn_file)) {
		$issues[$path] = 'Missing prop file';
		continue;
	}
	
	if (strpos(file_get_contents($svn_file), 'svn:keywords') === false) {
		$issues[$path] = 'Missing svn:keywords';
		continue;
	}
}

echo count($issues)." issues";
?>
<table>
<?php
foreach($issues as $path => $issue) {
	echo "<tr><td>$path</td><td>$issue</td></tr>";
}
?>
</table>
</body>
</html>