<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;
?>

<div class="contact-form">
    <div class="container">
        <div class="block-form">
            <div class="container-grid1__inner">
                <h2 class="title2 title">
                    <?
                    $APPLICATION->IncludeComponent(
                            "bitrix:main.include", "",
                            array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR . "include/block/questions/questions_title.php",
                            ),
                            false,
                            array('HIDE_ICONS' => 'N')
                    );
                    ?>
                </h2>
                <p class="desk">
                    <?
                    $APPLICATION->IncludeComponent(
                            "bitrix:main.include", "",
                            array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR . "include/block/questions/questions_text.php",
                            ),
                            false,
                            array('HIDE_ICONS' => 'N')
                    );
                    ?>
                </p>
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
            <?
            $APPLICATION->IncludeComponent(
                    "bitrix:main.include", "",
                    array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => SITE_DIR . "include/block/questions/questions_img.php",
                    ),
                    false,
                    array('HIDE_ICONS' => 'N')
            );
            ?>
        </div>
    </div>
</div>