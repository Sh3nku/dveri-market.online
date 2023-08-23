<?php
$arItem = $arResult['items'][0];
$arProperties = $arResult['properties']?>

<form class="p30" id="orderForm" autocomplete="off">
    <h1>Заказать звонок</h1>

    <input type="hidden" name="token_form" value="<?=$token_form?>" />

    <div class="row">
        <div class="col-s-12">
            <div class="form-input">
                <input type="text" class="grey" name="<?=$arProperties['fio']['code']?>" placeholder="<?=$arProperties['fio']['name']?>">
            </div>
        </div>

        <div class="col-s-12">
            <div class="form-input">
                <input type="text" class="grey" name="<?=$arProperties['phone']['code']?>" placeholder="<?=$arProperties['phone']['name']?>">
            </div>
        </div>

        <div class="col-s-12">
            <input class="button" type="submit" value="Отправить" />
        </div>
    </div>
</form>