<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;
?>

<section class="block-feedback">
    <div class="container">
        <div class="block-feedback__grid">
            <div class="block-feedback__left">
                <h2 class="title2">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include", "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => "/include/block/questions/questions_title.php",
                        ),
                        false,
                        array('HIDE_ICONS' => 'N')
                    );
                    ?>
                </h2>
                <div class="block-feedback__form">
                    <div class="block-feedback__text">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.include", "",
                            array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => "/include/block/questions/questions_text.php",
                            ),
                            false,
                            array('HIDE_ICONS' => 'N')
                        );
                        ?>
                    </div>
                    <?
                    $APPLICATION->IncludeComponent(
                        "acroweb:universal.form",
                        "questions",
                        [
                            "FORM_SID" => "acroweb_questions_s1",
                            "AJAX" => "Y",
                        ]
                    );
                    ?>
                </div>
            </div>
            <div class="block-feedback__right">
                <?
                $APPLICATION->IncludeComponent(
                    "bitrix:main.include", "",
                    array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => "/include/block/questions/questions.php",
                    ),
                    false,
                    array('HIDE_ICONS' => 'N')
                );
                ?>
            </div>
        </div>
    </div>
</section>