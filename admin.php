<?php
namespace infrajs\access;
use infrajs\ans\Ans;

if(!is_file('vendor/autoload.php')) {
	chdir('../../../');
	require_once('vendor/autoload.php');
}

	if (isset($_REQUEST['json'])) {
		//Для данных для слоя
		$ans = array();
		$ans['admin'] = Access::admin();

		return Ans::ret($ans);
	}
?>



<?php
	if (isset($_REQUEST['login'])) {
		Access::admin(true);
		?>
		<div style="padding:50px 100px">
			<p>Вы администратор</p>
			<p><a href="?">Проверить</a></p>
		</div>
<?php

	} elseif (isset($_REQUEST['logout'])) {
		Access::admin(false);
		?>

		<div style="padding:50px 100px">

			<p>Вы обычный посетитель</p>
			<p><a href="?">Проверить</a></p>
		</div>

<?php

	} else {
		$r = Access::admin();
		if ($r) {
			?>
		<div style="padding:50px 100px">
			<p>Вы администратор</p>
			<p><a href="?logout">Выход</a></p>
		</div>
<?php	
		} else {
			?>
		<div style="padding:50px 100px">
			<p>Вы обычный посетитель</p>
			<p><a href="?login">Вход</a></p>
		</div>
<?php	
		}
	}
?>
