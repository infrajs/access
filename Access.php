<?php
namespace infrajs\access;
use infrajs\hash\Hash;
use infrajs\once\Once;
use infrajs\mem\Mem;
use infrajs\nostore\Nostore;
use akiyatkin\boo\MemCache;
use infrajs\view\View;
use infrajs\path\Path;


class Access {
	public static $conf = array(
		"test"=>array("127.0.0.1","::1"),
		"debug"=>array("127.0.0.1","::1"),
		"admin"=>array('login'=>"admin", 'password'=>"admin")
	);
	public static function isTest()
	{
		if (self::isDebug()) return true;

		$conf = static::$conf;
		$ips = $conf['test'];
		if (is_array($ips)) {
			$is = in_array($_SERVER['REMOTE_ADDR'], $ips);
		} elseif (is_string($ips)) {
			$is = ($_SERVER['REMOTE_ADDR'] == $ips);
		} else {
			$is = !!$ips;
		}

		return $is;
	}
	public static function isDebug()
	{
		if (self::isAdmin()) return true;

		
		

		$conf = static::$conf;
		$ips = $conf['debug'];
		if (is_array($ips)) {
			$is = in_array($_SERVER['REMOTE_ADDR'], $ips);
		} elseif (is_string($ips)) {
			$is = ($_SERVER['REMOTE_ADDR'] == $ips);
		} else {
			$is = !!$ips;
		}
		return $is;
	}
	
	/**
	* Активируем зависимость текущего кода 
	* от того для кого этот код работает
	**/
	public static function nostore($is) {
		if ($is) {
			Nostore::on();
		} else {
			Once::$item['conds']['debug'] = array(
				'fn' => ['infrajs\\access\\Access','getDebugTime'],
				'args' => array()
			);
		}
	}
	public static function test($die = false)
	{
		$is = self::isTest();
		//Тестировщик Никак не влияет на кэш
		if (!$die) return $is;
		if ($is) return;
		header('HTTP/1.0 403 Forbidden');
		die('{"msg":"Недостаточно прав для доступа. Требуется config.access.test:['.$_SERVER['REMOTE_ADDR'].']"}');
	}

	public static function debug($die = false)
	{
		$is = self::isDebug();
		Access::nostore($is);
		if (!$die) return $is;
		if ($is) return;
		header('HTTP/1.0 403 Forbidden');
		die('{"msg":"Недостаточно прав для доступа. Требуется config.access.debug:['.$_SERVER['REMOTE_ADDR'].']"}');
	}
	public static function headers() {
		if (Access::isTest()) {
			//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
			//ini_set('display_errors', 1);
			header('Infrajs-Test:true');
		} else {
			//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
			header('Infrajs-Test:false');
			//ini_set('display_errors', 0);
		}
		if (Access::isDebug()) {
			header('Infrajs-Debug:true');
			Nostore::on(); //Браузер не кэширует no-store.
		} else {
			header('Infrajs-Debug:false');
		}
		if (Access::isAdmin()) {
			header('Infrajs-Admin:true');
			Access::adminSetTime();
		} else {
			header('Infrajs-Admin:false');
		}
	}
	/**
	 * Тихая функция, только проверка, без отметок.
	 */
	public static function isAdmin()
	{
		if(!Path::theme('~.infra.json')) {
			return false;
			//echo '<pre>';
			//throw new \Exception('Для авторизации требуется создать конфигурационный файл ~.infra.json с данными {"admin": {"login":"admin", "password":"admin"}}');
		}
		$conf = static::$conf;
		$data = $conf['admin'];
		$_ADM_NAME = $data['login'];
		$_ADM_PASS = $data['password'];
		if (empty($_SERVER['HTTP_USER_AGENT'])) {
			$_SERVER['HTTP_USER_AGENT'] = '';
		}
		$realkey = md5($_ADM_NAME.$_ADM_PASS.$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
		$key = View::getCookie('infra_admin');

		return ($key === $realkey);
	}
	/**
	 * Access::admin(true) - пропускает только если ты администратор, иначе выкидывает окно авторизации
	 * Access::admin(false) - пропускает только если ты НЕ администратор, иначе выкидывает окно авторизации
	 * $ans выводится в json если нажать отмена
	 * Access::admin(array('login','pass'));.
	 */
	public static function admin($break = null, $ans = array('msg' => 'Требуется авторизация', 'result' => 0))
	{
		$data = static::$conf['admin'];
		$_ADM_NAME = $data['login'];
		$_ADM_PASS = $data['password'];
		$admin = null;//Неизвестно

		$realkey = md5($_ADM_NAME.$_ADM_PASS.$_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);

		

		if (is_array($break)) {
			Nostore::on();
			//Если имя в конфиге указано, и переданные данные в массиве соответствуют
			$admin = ($_ADM_NAME && $break[0] === $_ADM_NAME && $break[1] === $_ADM_PASS);
			if ($admin) {
				View::setCookie('infra_admin', $realkey);
			} else {
				View::setCookie('infra_admin');
			}
		} else {
			$key = View::getCookie('infra_admin');
			$admin = ($key === $realkey);
			if ($break === false) {
				Nostore::on();
				View::setCookie('infra_admin');
				$admin = false;
			} elseif ($break === true && !$admin) {
				Nostore::on();
				//Если имя в конфиге указано, и переданные данные по HTTP соответствуют
				if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
					$admin = ($_ADM_NAME && $_SERVER['PHP_AUTH_USER'] == $_ADM_NAME && $_SERVER['PHP_AUTH_PW'] == $_ADM_PASS);
				} else {
					$admin = false;
				}
				if ($admin) {
					View::setCookie('infra_admin', $realkey);
				} else {
					header('WWW-Authenticate: Basic realm="Protected Area"');
					header('HTTP/1.0 401 Unauthorized');
					echo json_encode($ans, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
					exit;
				}
			} else {
				Access::nostore($admin);//Иногда сохраняем кэш иногда нет. Админ всегда проверяет кэш. А данный вызов добавляет проверку которая возвращает всегда true для админа
			}
		}
		return $admin;
	}
	public static function adminSetTime($t = null)
	{
		if (is_null($t)) $t = time();
		Access::$time = $t;
		$adm = array('time' => $t);
		Mem::set('infra_admin_time', $adm);
	}
	/**
	 * Отвечает на вопрос! Время настало для сложной обработки?
	 * Функция стремится сказать что время ещё не пришло... и последней инстанцией будет выполнение фукции которая должна вернуть true или false
	 * Если передать метку времени и функцию это будет означать запуск функции если метка времени старей админской метки
	 * В debug режиме функция запускается всегда.
	 */
	public static function adminIsTime($cachetime = 0, $callback = false, $re = false)
	{
		if (!$cachetime || $re) {
			return true; //Нет кэша... пришло всремя для сложной обработки
		};
		//Мы тут не меняем содержание.. а только отвечам на вопрос можно ли закэшировать... cache_no от Infra_debug тут неуместен
		if (self::isDebug() || $cachetime < self::adminTime()) {
			if ($callback) {
				return !!$callback($cachetime); //Только функция сможет сказать надо или нет
			}
			return true;
		}

		return false;
	}
	public static function getDebugTime()
	{
		if (Access::isDebug()) return time();
		else return Access::adminTime();
	}
	public static $time = false;
	/**
	 * Время когда админ что-то сделал (время последнего обращения к функции infra_admin и её результате true)
	 * Функция работает без параметров...возвращает дату последних изменений админа для всей системы
	 */
	public static function getAdminTime()
	{
		if (Access::isAdmin()) return time();
		return adminTime();
	}
	public static function adminTime()
	{
		if (Access::$time === false) {
			$adm = Mem::get('infra_admin_time');
			if (!$adm) {
				$adm = array();
			}
			if (!isset($adm['time'])) {
				$adm['time'] = 0;
			}

			Access::$time = $adm['time'];
		}
		return Access::$time;
	}
	public static function func($fn, $args = array()) {
		return MemCache::func( $fn, $args, ['infrajs\\access\\Access','getDebugTime'], [], 1);
	}
	public static function cache($name, $fn, $args = array(), $re = false) {
		Once::$nextgid = $name;
		return MemCache::func( $fn, $args, ['infrajs\\access\\Access','getDebugTime'], [], 1);
		//}, [$name, $args],['infrajs\\access\\Access','adminTime'],[], 1);
	}
	/*public static function cache($name, $fn, $args = array(), $re = false)
	{
		//Запускается один раз для админа, остальные разы возвращает кэш из памяти
		$name = 'Access::cache '.$name;
		return Once::exec($name, function ($args, $name) use ($fn, $re) {
			$path = $name.'_'.Hash::make($args);
			$data = Mem::get($path);
			if (!$data) {
				$data = array('time' => 0);
			}
			$execute = self::adminIsTime($data['time'], function () {
				return true;
			}, $re);

			if ($execute) {
				$cache = !Nostore::check(function () use (&$data, $fn, $args, $re) {
					$data['result'] = call_user_func_array($fn, array_merge($args, array($re)));
				});
				if ($cache) {
					$data['time'] = time();
					Mem::set($path, $data);
				} else {
					Mem::delete($path);
				}
			}

			return $data['result'];
		}, array($args, $name), $re);
	}*/
	public static function modified($etag = '')
	{
		//$v изменение которой должно создавать новую копию кэша
		if (self::isDebug()) return;


		/*if ($etag) {
			//Мы осознано включаем возможность кэшировать, даже если были запреты до этого! 
			//так ак есть Etag и в нём срыты эти неявные условия
			//Таким образом отменяется обращение к базе даных, инициализация сессии и тп.
			Nostore::off();

			Если так то вручную надо выставлять Nostore::off(); мы не знаем что там за etag
		}*/

		if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$last_modified = self::adminTime();
			
			/*
				Warning: strtotime(): It is not safe to rely on the system's timezone settings. You are *required* to use the date.timezone setting or the date_default_timezone_set() function. In case you used any of those methods and you are still getting this warning, you most likely misspelled the timezone identifier. We selected the timezone 'UTC' for now, but please set date.timezone to select your timezone
			*/
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) > $last_modified) {
				if (empty($_SERVER['HTTP_IF_NONE_MATCH']) || $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
					//header('ETag: '.$etag);
					//header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE']);
					header('HTTP/1.0 304 Not Modified');

					
					
					


					exit;
				}
			}
		}
		header('ETag: '.$etag);

		$now = gmdate('D, d M Y H:i:s', time()).' GMT';
		header('Last-Modified: '.$now);
	}
}