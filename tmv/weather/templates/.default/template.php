<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<? if (!empty($arResult['WEATHER'])) : ?>

	<div class="weather_container">
		<div class="weather_title weather_element">Погода в <?= $arResult['WEATHER']['CITY_NAME'] ?>:</div>
		<div class="weather_wrapper">
            <? if($arParams['TEMPERATURE']) : ?>
			    <div class="weather_temperature weather_element">Температура: <?= $arResult['WEATHER']['TEMPERATURE'] ?>C</div>
            <? endif ?>

	        <? if($arParams['HUMIDITY']) : ?>
			    <div class="weather_humidity weather_element">Влажость: <?= $arResult['WEATHER']['HUMIDITY'] ?>%</div>
	        <? endif ?>

	        <? if($arParams['PRESSURE']) : ?>
			    <div class="weather_pressure weather_element">Давление: <?= $arResult['WEATHER']['PRESSURE'] ?> мм рт. ст.</div>
	        <? endif ?>

	        <? if($arParams['WIND']) : ?>
                <div class="weather_wind weather_element">Ветер: <?= $arResult['WEATHER']['WIND']['SPEED'] ?> м/с
                    <?= ($arResult['WEATHER']['WIND']['DEG'] > 0 && $arResult['WEATHER']['WIND']['DEG'] < 90 ) ? 'СВ' : '' ?>
                    <?= ($arResult['WEATHER']['WIND']['DEG'] == 90 ) ? 'В' : '' ?>
                    <?= ($arResult['WEATHER']['WIND']['DEG'] > 90 &&  $arResult['WEATHER']['WIND']['DEG'] < 180) ? 'ЮВ' : '' ?>
                    <?= ($arResult['WEATHER']['WIND']['DEG'] == 180 ) ? 'Ю' : '' ?>
                    <?= ($arResult['WEATHER']['WIND']['DEG'] > 180 &&  $arResult['WEATHER']['WIND']['DEG'] < 270) ? 'ЮЗ' : '' ?>
                    <?= ($arResult['WEATHER']['WIND']['DEG'] == 270 ) ? 'З' : '' ?>
                    <?= ($arResult['WEATHER']['WIND']['DEG'] > 270 &&  $arResult['WEATHER']['WIND']['DEG'] < 360) ? 'СЗ' : '' ?>
                    <?= ($arResult['WEATHER']['WIND']['DEG'] == 360 ) ? 'С' : '' ?>
                </div>
	        <? endif ?>
		</div>
	</div>

<? endif ?>