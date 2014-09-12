<?php

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler('exception_error_handler');

$commits = (int)`git rev-list HEAD --count`;
$stats = ['added' => array_fill(0, $commits-1, 0), 'removed' => array_fill(0, $commits-1, 0)];


for($i = 0; $i < $commits-1; $i++) {
	$diff = shell_exec("git diff --numstat HEAD~".($i+1)."..HEAD~".$i);
	$lines = explode("\n", $diff);

	foreach($lines as &$l) {
		$l = preg_replace('/\s+/', ' ', $l);

		if ($l === "")
			continue;

		$arr = explode(" ", $l);
		$stats['added'][$i] += $arr[0];
		$stats['removed'][$i] += $arr[1];
	}
}

$maxAdded = max($stats['added']);
$maxRemoved = max($stats['removed']);

$result = array_reverse(array_map(function($a, $r){
	return ['added' => $a, 'removed' => $r];
}, $stats['added'], $stats['removed']));

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Test</title>
	</head>
	<body>
		<?php foreach($result as $k => $r):?>
		<div style="width: 800px; margin: 0; margin-top: 10px; border-bottom: black 1px solid; font-weight: bold">
			<div style="width: 400px; margin: 0; display: inline-block; float: left">
				<span style="color:green; font-size: <?php if($r['added'] > 0) echo ceil(($r['added']/$maxAdded)*10); else echo 1;?>em "><?=$r['added'] ?></span>
			</div>
			<div style="width: 400px; margin: 0; display: inline-block; float: right">
				<span style="color:red; font-size: <?php if($r['removed'] > 0) echo ceil(($r['removed']/$maxRemoved)*10); else echo 1;?>em "><?=$r['removed'] ?></span>
			</div>
			<div style="clear: both"></div>
		</div>
		<?php endforeach ?>
	</body>
</html>