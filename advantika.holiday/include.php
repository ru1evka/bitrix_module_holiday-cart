<?php

namespace Advantika\Holiday;

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\IO\Directory;
use \Bitrix\Main;

const ID = 'advantika.holiday';
const APP = __DIR__ . '/';
const LIB = APP . 'lib/';

define(
	__NAMESPACE__ . '\\CONFIG_DIR',
	$_SERVER['DOCUMENT_ROOT'] . '/local/config'
);
const CONFIG = CONFIG_DIR . '/.' . ID . '.';

const IGNORED_TEMPLATES = [
	'bitrix24' => 1,
	'desktop_app' => 1,
	'learning' => 1,
	'login' => 1,
	'mail_user' => 1,
	'mobile_app' => 1,
	'pub' => 1,
];

require LIB . 'encoding/include.php';

function AppendValues($data, $n, $v)
{
	yield from $data;
	for ($i = 0; $i < $n; ++$i)
	{
		yield  $v;
	}
}

function Options($siteId)
{
	$fname = OptionsFilename('options', $siteId);
	return is_readable($fname) ?
		include $fname : [
			'active_form' => 'N',
			'template' => 'not_holiday',
			'text_new_year_after' => '',
			'text_new_year_before' => '',
			'active_snow' => 'N',
			'color_snow' => '#a6e7ff',
			'size_snow' => '15',
			'count_snow' => '100',
			'ball_snow' => '10',
			'clear_snow' => '20000',
			'year-after' => '',
			'year-before' => '',
			'logo' => '',
		];
}

function OptionsUpdate($data, $siteId)
{

    $fname = OptionsFilename('options', $siteId);
	if (!is_dir(CONFIG_DIR))
	{
		Directory::createDirectory(CONFIG_DIR);
	}
	\Encoding\PhpArray\Write($fname, [
        'active_form' => $data['active_form'],
        'template' => $data['template'],
        'text_new_year_after' => $data['text_new_year_after'],
        'text_new_year_before' => $data['text_new_year_before'],
        'active_snow' => $data['active_snow'],
        'color_snow' => $data['color_snow'],
        'size_snow' => $data['size_snow'],
        'count_snow' => $data['count_snow'],
        'ball_snow' => $data['ball_snow'],
        'clear_snow' => $data['clear_snow'],
        'year-after' => $data['year-after'],
        'year-before' => $data['year-before'],
        'logo' => $data['logo'],
	]);
}

function OptionsFilename($group, $siteId, $ext = '.php')
{
	return CONFIG . $group . '.' . $siteId . $ext;
}

function Postcard(){
    $currentOptions = Options(SITE_ID);
    if($currentOptions['active_form'] == 'Y'){
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS(SITE_DIR."bitrix/modules/advantika.holiday/assets/css/style-postcard.css");
        $APPLICATION->AddHeadScript(SITE_DIR."bitrix/modules/advantika.holiday/assets/js/script_postcard.js");
        if(time() > strtotime($currentOptions['year-after']) && time() < strtotime($currentOptions['year-before'])){
            if (file_exists(__DIR__."/template/".$currentOptions['template'].".php")){
                require_once __DIR__."/template/".$currentOptions['template'].".php";
            }
        }
    }
}

function Snow(){
    $currentOptions = Options(SITE_ID);
    if($currentOptions['active_snow'] == 'Y'){
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS(SITE_DIR."bitrix/modules/advantika.holiday/assets/css/snow.min.css");
        $APPLICATION->AddHeadScript(SITE_DIR."bitrix/modules/advantika.holiday/assets/js/Snow.min.js");
        $icon = file_get_contents(__DIR__."/assets/img/icon_draft/".$currentOptions['template'].".php") ;
        print '
        <script>
            let size = '.$currentOptions['size_snow'].';
            let tempalate = "'.$currentOptions['template'].'";
            let icon = '.$icon.';
            if(tempalate == "new_years"){
                new Snow({
                    iconColor : "'.$currentOptions['color_snow'].'",
                    iconSize :'.$currentOptions['size_snow'].',
			        countSnowflake: '.$currentOptions['count_snow'].',
			        snowBallsLength: '.$currentOptions['ball_snow'].',
			        clearSnowBalls: '.$currentOptions['clear_snow'].',
			        showSnowBalls: true,
			        showSnowBallsIsMobile: true,
			        showSnowflakes: true
		        });
            } else{
                new Snow({
                    icon: icon,
                    iconSize :'.$currentOptions['size_snow'].',
			        countSnowflake: '.$currentOptions['count_snow'].',
			        snowBallsLength: '.$currentOptions['ball_snow'].',
			        clearSnowBalls: '.$currentOptions['clear_snow'].',
			        showSnowBalls: false,
			        showSnowBallsIsMobile: false,
			        showSnowflakes: true
		        });
            };
	    </script>
	    ';
    }
}

function SwitchLogo(){
    $currentOptions = Options(SITE_ID);
    if($currentOptions['logo'] == 'Y'){
        global $APPLICATION;
        $APPLICATION->AddHeadScript(SITE_DIR."bitrix/modules/advantika.holiday/assets/js/script_logo.js");
    }
}

function init()
{
	Loc::loadMessages(__FILE__);

	AddEventHandler(
		'main',
		'OnEpilog',
		__NAMESPACE__ . '\\Postcard'
	);

    AddEventHandler(
		'main',
		'OnEpilog',
		__NAMESPACE__ . '\\Snow'
	);

    AddEventHandler(
		'main',
		'OnEpilog',
		__NAMESPACE__ . '\\SwitchLogo'
	);
}

init();
