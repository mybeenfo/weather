<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Service\GeoIp;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Компонент погода на сайте
 *
 * Класс реализует компонент погоды. Определяется геопозиция пользователя.
 * По геопозиции зпрашиваются данные сервиса погоды.
 */
class ExampleCompSimple extends CBitrixComponent
{
	private $_request;

	/**
	 * Проверка наличия модулей требуемых для работы компонента
	 * @return bool
	 * @throws Exception
	 */
	private function _checkModules()
	{
		if (!Loader::includeModule('iblock')
			|| !Loader::includeModule('sale')
		) {
			throw new \Exception('Не загружены модули необходимые для работы модуля');
		}

		return true;
	}

	/**
	 * Подготовка параметров компонента
	 * @param array $arParams с параметрами компонента
	 * @return array $arParams с изменёнными параметрами компонента
	 */
	public function onPrepareComponentParams($arParams)
	{
		// Параметр температуры
		$arParams['TEMPERATURE'] = ($arParams['TEMPERATURE'] == 'Y');
		// Параметр влажности
		$arParams['HUMIDITY'] = ($arParams['HUMIDITY'] == 'Y');
		// Параметр давления
		$arParams['PRESSURE'] = ($arParams['PRESSURE'] == 'Y');
		// Параметр ветра
		$arParams['WIND'] = ($arParams['WIND'] == 'Y');
		// Текущая погода
		$arParams['CURRENT_WEATHER'] = ($arParams['CURRENT_WEATHER'] == 'Y');

		return $arParams;
	}

	/**
	 * Точка входа в компонент
	 * @throws Exception
	 */
	public function executeComponent()
	{

		$this->_checkModules();

		$this->_request = Application::getInstance()->getContext()->getRequest();

		// Если есть сессия с погодой
		if (isset($_SESSION['WEATHER'])) {
			// Запись данных в $arResult из сессии с погоды
			$this->arResult['WEATHER'] = $_SESSION['WEATHER'];
		} else {
			// Запрос данных о погоде
			$this->arResult['WEATHER'] = $this->getWeather();
			// Запись данных о погоде в сессию
			$_SESSION['WEATHER'] = $this->arResult['WEATHER'];
		}
		// Подключение шаблона
		$this->includeComponentTemplate();
	}

	/**
	 * Получение погоды по ip пользователя
	 * @return array Массив с данными о температуре, силе ветра, влажности, город
	 * @throws Exception
	 */
	public function getWeather()
	{

		$weather = [];
		// Ip адрес пользователя
		$ipAddress = GeoIp\Manager::getRealIp();
		// Даные о геопозиции
		$geo_data = $result = GeoIp\Manager::getDataResult($ipAddress, "ru")->getGeoData();
		// Тестовые данные для локального сервера
		//$geo_data = GeoIp\Manager::getDataResult('151.252.109.208', "ru")->getGeoData();
		// Если местоположение найдено
		if (isset($geo_data->cityName)) {
			// Запрос к api погоды
			$weather_api_result = $this->weatherApi($geo_data->latitude, $geo_data->longitude);
			// Если запрос удачно осуществлён
			if ((integer)$weather_api_result->cod == 200) {
				// Получение склонения города по подежам
				$morphy_object = $this->morphy($geo_data->cityName);
				// Название города в предложном падеже
				$weather['CITY_NAME'] = (!empty($morphy_object)) ? $morphy_object->П : 'городе ' . $geo_data->cityName;
				// Температура
				if ($this->arParams['TEMPERATURE']) $weather['TEMPERATURE'] = $weather_api_result->main->temp;
				// Влажность
				if ($this->arParams['HUMIDITY']) $weather['HUMIDITY'] = $weather_api_result->main->humidity;
				// Давление
				if ($this->arParams['PRESSURE']) $weather['PRESSURE'] = $weather_api_result->main->pressure;
				// Текущая погода
				if ($this->arParams['CURRENT_WEATHER']) $weather['CURRENT_WEATHER'] = [
					'MAIN' => $weather_api_result->weather[0]->main,
					'IMG' => $weather_api_result->weather[0]->icon,
				];
				// Ветер
				if ($this->arParams['WIND']) $weather['WIND'] = [
					// Скорость ветра
					'SPEED' => $weather_api_result->wind->speed,
					// Направление ветра
					'DEG' => $weather_api_result->wind->deg,
				];
			}
		}

		return $weather;
	}

	/**
	 * Получение данных о погоде от api
	 * @param float $lat широта
	 * @param float $lng долгота
	 * @return object данные о погоде
	 */
	public function weatherApi($lat, $lng)
	{

		$url = 'http://api.openweathermap.org/data/2.5/weather?lat=' . $lat . '&lon=' . $lng . '&units=metric&APPID=d392eab5e573d5ae0af03023c0e74662';

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($curl));
		curl_close($curl);

		return $result;

	}

	/**
	 * Склонение слова по падежам и числу
	 * @param string слово на русском языке в ИМ падеже
	 * @return object слова по падежам и числу
	 * @todo API используется мной только в тестовом задании. В реале лучше подключить библиотеку, например phpMorphy.
	 */
	public function morphy($word)
	{

		$url = 'http://ws3.morpher.ru/russian/declension?format=json&s=' . urlencode($word);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($curl));
		curl_close($curl);

		return $result;
	}
}