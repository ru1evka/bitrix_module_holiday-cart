<?php
namespace Advantika\Holiday;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages(__FILE__);

$siteId = $request->get('site_id');
if (!$siteId)
{
	$site = \CSite::GetList($by = 'sort', $order = 'asc', [
		'DEFAULT' => 'Y',
	])->Fetch();
	if (!$site)
	{
		$site = \CSite::GetList($by = 'sort', $order = 'asc', [
			'ACTIVE' => 'Y',
		])->Fetch();
	}
	if (!$site)
	{
		$site = \CSite::GetList($by = 'sort', $order = 'asc')->Fetch();
	}
}
else
{
	$site = \CSite::GetByID($siteId)->Fetch();
}
if (empty($site['LID']))
{
	\CAdminMessage::ShowMessage(
		Loc::getMessage('ADVANTIKA_CHRISTMAS_SITE_NOT_DEFINED')
	);
	return;
}

if (check_bitrix_sessid() && $request->isPost())
{

	if ($request->getPost('save') != '')
	{
		$data = $request->getPostList();

        //Получаем загруженый файл и определяем его местоположение
        $result_url = [];
        if ($request->get('advantika_action') == 'CHRISTMASs')
		{
			Update($data, $site['LID'], $result_url);
           \CFile::Delete($data['file_url_CHRISTMAS'][0]);
		}
		else
		{
			OptionsUpdate($data, $site['LID']);
		}
		\CAdminMessage::ShowNote(
			Loc::getMessage('ADVANTIKA_CHRISTMAS_SETTINGS_SAVED')
		);
	}
}
$currentOptions = Options($site['LID']);

$title = '[' . $site['LID'] . '] ' .  $site['NAME'];
if ($request->get('advantika_action') == 'CHRISTMASs')
{
	$description = Loc::getMessage('ADVANTIKA_CHRISTMAS_TITLE_CHRISTMASS');
}
else
{
	$description = Loc::getMessage('ADVANTIKA_CHRISTMAS_OPTIONS_TITLE');
}
$aTabs[] = array(
    'DIV' => 'edit1',
	'TAB' => $title,
	'TITLE' => $description
);
$APPLICATION->SetAdditionalCSS(SITE_DIR."/bitrix/modules/advantika.holiday/assets/css/style.css");
$tabControl = new \CAdminTabControl("tabControl", $aTabs);
$tabControl->begin();

?>

<form method="post" action="">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>
        <tr class="heading ">
            <td class="adm-detail-content-cell-l adm-detail-title gray-section_down" colspan="2">
                <label>
                    <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_TITLE_ALL") ?>
                </label>
            </td>
        </tr>
	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_WWW_TITLE") ?>
			</label>
		</td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input name="active_form" value="Y" type="checkbox"
                <?= $currentOptions["active_form"] == "Y"? "checked" : "" ?>>
        </td>
	</tr>
    <tr>
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_TEMPLATE") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <select id="template" name="template">
                <option <?=$currentOptions["template"] == "not_holiday"? "selected" : ""?> value="not_holiday">Никакого праздника😢</option>
                <option <?=$currentOptions["template"] == "new_years"? "selected" : ""?> value="new_years">Новый год 🎆</option>
                <option <?=$currentOptions["template"] == "defender_day"? "selected" : ""?> value="defender_day">23 февраля 🪖</option>
                <option <?=$currentOptions["template"] == "women_day"? "selected" : ""?> value="women_day">8 Марта 💐</option>
                <option <?=$currentOptions["template"] == "may_holiday"? "selected" : ""?> value="may_holiday">Майские праздники ☭</option>
                <option <?=$currentOptions["template"] == "birthday_day"? "selected" : ""?> value="birthday_day">День рожденье 🎉</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_LOGO") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input name="logo" value="Y" type="checkbox"
                <?= $currentOptions["logo"] == "Y"? "checked" : "" ?>>
        </td>
    </tr>

    <tr>
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_YEAR_AFTER") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input type="date" required name="year-after" value="<?= $currentOptions["year-after"] != ""? $currentOptions["year-after"] : date('Y-m-d') ?>" >
        </td>
    </tr>
    <tr>
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_YEAR_BEFORE") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input type="date" required name="year-before" value="<?= $currentOptions["year-before"] != ""? $currentOptions["year-before"] : date('Y-m-d') ?>" >
        </td>
    </tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>
				<?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_SLASH_TITLE") ?>
			</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<textarea style="width: 98%;height: 248px;" name="text_new_year_after"><?= $currentOptions["text_new_year_after"] != ""? $currentOptions["text_new_year_after"] : "" ?></textarea>
		</td>
	</tr>
    <tr>
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_SLASH_TITLE_BEFORE") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <textarea style="width: 98%;height: 248px;" name="text_new_year_before"><?= $currentOptions["text_new_year_before"] != ""? $currentOptions["text_new_year_before"] : "" ?></textarea>
        </td>
    </tr>
    <tr class="heading">
        <td class="adm-detail-content-cell-l adm-detail-title gray-section" colspan="2">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_OPTIONS_TITLE_CATALOG") ?>
            </label>
        </td>
    </tr>
        <tr>
            <td class="adm-detail-content-cell-l" width="50%">
                <label>
                    <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_FROM_ACTIVE_SHOW") ?>
                </label>
            </td>
            <td class="adm-detail-content-cell-r" width="50%">
                <input name="active_snow" value="Y" type="checkbox"
                    <?= $currentOptions["active_snow"] == "Y"? "checked" : "" ?>>
            </td>
        </tr>
    <tr id="color">
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_FROM_COLOR") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input name="color_snow"  value="<?= $currentOptions["color_snow"] != ""? $currentOptions["color_snow"] : "#a6e7ff" ?>" type="text">
        </td>
    </tr>
    <tr>
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_FROM_SIZE") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input name="size_snow" value="<?= $currentOptions["size_snow"] != ""? $currentOptions["size_snow"] : "15" ?>" type="text">
        </td>
    </tr>
    <tr id="count">
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_FROM_COUNT") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input name="count_snow" value="<?= $currentOptions["count_snow"] != ""? $currentOptions["count_snow"] : "100" ?>" type="text">
        </td>
    </tr>
    <tr id="ball">
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_FROM_BALL") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input name="ball_snow" value="<?= $currentOptions["ball_snow"] != ""? $currentOptions["ball_snow"] : "10" ?>" type="text">
        </td>
    </tr>
    <tr id="clear">
        <td class="adm-detail-content-cell-l" width="50%">
            <label>
                <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_FROM_CLEAR") ?>
            </label>
        </td>
        <td class="adm-detail-content-cell-r" width="50%">
            <input name="clear_snow" value="<?= $currentOptions["clear_snow"] != ""? $currentOptions["clear_snow"] : "20000" ?>" type="text">
        </td>
    </tr>
        <tr class="internal">
            <td class="align-center gray-section_options" colspan="2">
                <label>
                    <?= Loc::getMessage("ADVANTIKA_CHRISTMAS_FROM_CATALOG_SECTION") ?>
                </label>
            </td>
        </tr>
<?

?>
<?$tabControl->BeginNextTab();
?>
    <?php $tabControl->buttons(); ?>

    <input class="adm-btn-save" type="submit" name="save"
           value="<?= Loc::getMessage("ADVANTIKA_CHRISTMAS_SAVE_SETTINGS") ?>">
<?php
$tabControl->end();?>
</form>

<script>
    // $(document).ready(function () {
    //     $('select[name="template"]').on('change', function (e) {
    //         $(this).find("option:selected").each( function() {
    //             console.log($(this).value());
    //         })
    //     });
    // })
BX.ready(function () {
	"use strict";
	// autoappend rows
	function makeAutoAppend($table) {
		function bindEvents($row) {
			for (let $input of $row.querySelectorAll('input[type="text"]')) {
				$input.addEventListener("change", function (event) {
					let $tr = event.target.closest("tr");
					let $trLast = $table.rows[$table.rows.length - 1];
					if ($tr != $trLast) {
						return;
					}
					$table.insertRow(-1);
					$trLast = $table.rows[$table.rows.length - 1];
					$trLast.innerHTML = $tr.innerHTML;
					let idx = parseInt($tr.getAttribute("data-idx")) + 1;
					$trLast.setAttribute("data-idx", idx);
					for (let $input of $trLast.querySelectorAll("input,select")) {
						let name = $input.getAttribute("name");
						if (name) {
							$input.setAttribute("name", name.replace(/([a-zA-Z0-9])\[\d+\]/, "$1[" + idx + "]"));
						}
					}
					bindEvents($trLast);
				});
			}
		}
		for (let $row of document.querySelectorAll(".js-table-autoappendrows tr")) {
			bindEvents($row);
		}
	}
	for (let $table of document.querySelectorAll(".js-table-autoappendrows")) {
		makeAutoAppend($table);
	}
    const select = document.getElementById('template');
    let options = ['color', 'ball', 'count', 'clear'];
    if (select.options[select.selectedIndex].value == 'new_years'){
        for(var i = 0; i < options.length; i++) {
            document.getElementById(''+options[i]+'').style.display = 'table-row';
        }
    } else {
        for(var i = 0; i < options.length; i++) {
            document.getElementById(''+options[i]+'').style.display = 'none';
        }
    }
    select.addEventListener('change', function(event) {
        if (event.target.value == 'new_years'){
            for(var i = 0; i < options.length; i++) {
                document.getElementById(''+options[i]+'').style.display = 'table-row';
            }
        } else {
            for(var i = 0; i < options.length; i++) {
                document.getElementById(''+options[i]+'').style.display = 'none';
            }
        }
    });
});

</script>
