<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var string $componentPath
 * @var string $componentName
 * @var array $arCurrentValues
 * */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if( !Loader::includeModule("iblock") ) {
	throw new \Exception('Не загружены модули необходимые для работы компонента');
}

// типы инфоблоков
$arIBlockType = CIBlockParameters::GetIBlockTypes();

// инфоблоки выбранного типа
$arIBlock = [];
$iblockFilter = !empty($arCurrentValues['IBLOCK_TYPE'])
	? ['TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y']
	: ['ACTIVE' => 'Y'];

$rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
}
unset($arr, $rsIBlock, $iblockFilter);

$arComponentParameters = [
	// группы в левой части окна
	"GROUPS" => [
		"SETTINGS" => [
			"NAME" => Loc::getMessage('WEATHER_COMPONENT_PROP_SETTINGS'),
			"SORT" => 550,
		],
	],

	"PARAMETERS" => [
		"TEMPERATURE"  =>  array(
			"PARENT"    =>  "BASE",
			"NAME"      =>  Loc::getMessage('WEATHER_COMPONENT_TEMPERATURE'),
			"TYPE"      =>  "CHECKBOX",
		),
		"WIND"  =>  array(
			"PARENT"    =>  "BASE",
			"NAME"      =>  Loc::getMessage('WEATHER_COMPONENT_WIND'),
			"TYPE"      =>  "CHECKBOX",
		),
		"HUMIDITY"  =>  array(
			"PARENT"    =>  "BASE",
			"NAME"      =>  Loc::getMessage('WEATHER_COMPONENT_HUMIDITY'),
			"TYPE"      =>  "CHECKBOX",
		),
		"PRESSURE"  =>  array(
			"PARENT"    =>  "BASE",
			"NAME"      =>  Loc::getMessage('WEATHER_COMPONENT_PRESSURE'),
			"TYPE"      =>  "CHECKBOX",
		),
		"CURRENT_WEATHER"  =>  array(
			"PARENT"    =>  "BASE",
			"NAME"      =>  Loc::getMessage('WEATHER_COMPONENT_CURRENT_WEATHER'),
			"TYPE"      =>  "CHECKBOX",
		),
		// Настройки кэширования
		'CACHE_TIME' => ['DEFAULT' => 3600],
	]
];