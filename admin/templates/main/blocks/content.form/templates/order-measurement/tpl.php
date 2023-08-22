<?php
$arItem = $arResult['items'][0];
$arProperties = $arResult['properties']?>

<div class="container">

    <div class="measurement-wrapper">

        <form id="measurementForm" autocomplete="off">

            <h2>Заказать замер</h2>

            <input type="hidden" name="token_form" value="<?=$token_form?>" />

            <div class="measurement">
                <div class="row">
                    <div class="col-s-4">
                        <div class="form-row">
                            <div class="form-input">
                                <input type="text" placeholder="<?=$arProperties['name']['name']?>" name="<?=$arProperties['name']['code']?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-s-4">
                        <div class="form-row">
                            <div class="form-input" data-error="phone">
                                <input type="text" placeholder="<?=$arProperties['phone']['name']?>" name="<?=$arProperties['phone']['code']?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-s-4">
                        <div class="form-row">
                            <div class="form-input">
                                <input type="text" placeholder="<?=$arProperties['email']['name']?>" name="<?=$arProperties['email']['code']?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-m-12">
                        <div class="form-row">
                            <div class="form-input">
                                <textarea placeholder="<?=$arProperties['text']['name']?>" name="<?=$arProperties['text']['code']?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="measurement-footer">
                <div><input class="button" type="submit" value="Отправить" /></div>
                <div>Нажимая кнопку «Подтвердить», я даю своё согласие на обработку персональной информации в соответствии с <a href="/privacy-policy.php" target="_blank">Политикой конфиденциальности</a></div>
            </div>

        </form>

    </div>

</div>