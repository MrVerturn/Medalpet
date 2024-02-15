<?
    define("FONT_PROPERTY_CODE", "medalPet_tp_font");
    define("COLOR_PROPERTY_CODE", "medalPet_tp_color");
    define("PATTERN_PROPERTY_CODE", "medalPet_tp_pattern");
    $APPLICATION->AddHeadString('<link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"  type="text/css" rel="stylesheet" />',true);
?>
<?if($arParams["AJAX"] != "Y"):?>
    <?$componentId = randString(7)?>
    <?$arParams["COMPONENT_ID"] = $componentId?>

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" /> -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <div class="medalPetBasket"  id="medalPetBasket-cont_<?=$componentId?>-wrapper">
<?else:?>
    <?$componentId = $arParams["COMPONENT_ID"]?>
<?endif;?>

<div class="medalPetBasket-cont swiper"  id="medalPetBasket-cont_<?=$componentId?>-swiper">
    <div class="swiper-wrapper"  id="medalPetBasket-cont_<?=$componentId?>">
    <?$first = true;?>
    <?foreach ($arResult["PRODUCTS"] as $key => $sectionObj):?>

        <?if(!$first):?>
            <div class="swiper-slide medalPetBasket-section-spliter">
                <img src='<?=$templateFolder?>/img/section_spliter.svg' alt="">
                <!-- <object data="<?=$templateFolder?>/img/section_spliter.svg" type="image/svg+xml" class="icon"></object> -->
                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16.388" height="37.384" viewBox="0 0 16.388 37./384">
                    <path id="Контур_1760" data-name="Контур 1760" d="M-320.967,4429.935l-15.182,18.813,15.182,17.163" transform="translate(336.649 -4429.232)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"/>
                </svg> -->
                <!-- div. -->
            </div>
        <?else:?>
            <?$first = false;?>
        <?endif;?>

        <div class="medalPetBasket-section swiper-slide <?=( ($sectionObj["INFO"]["CODE"] == $arParams["SECTION_CODE"])?"selected":"" )?> ">
            <div class="medalPetBasket-section_title">
                <a href="<?=$sectionObj["INFO"]["URL_PATH"]?>"><?=$sectionObj["INFO"]["NAME"]?></a>
            </div>
            <div class="medalPetBasket-section_items">
                <?if(count($sectionObj["PRODUCTS"]) > 0):?>
                    <?foreach ($sectionObj["PRODUCTS"] as $key1 => $productObj):?>
                        <?//print_p($productObj);?>
                        <div class="medalPetBasket-product">

                            <div class="products_lables">
                                <?if($productObj["CAN_BUY"] == "N"):?>
                                    <a class="not_avaible_label product_label" href="/contacts/?ask=<?=$productObj["ID"]?>" rel="nofollow">
                                        Нет в наличии				
                                    </a>
                                <?endif;?>
                            </div>

                            <div class="medalPetBasket-product_img-wrapper">
                                <div class="medalPetBasket-product_img" style="background-image:url(<?=$productObj["DETAIL_PICTURE_SRC"]?>)" ></div>
                                <!-- <img src="<?=$productObj["DETAIL_PICTURE_SRC"]?>" alt="<?=$productObj["NAME"]?>"> -->
                            </div>   

                            <div class="medalPetBasket-product_title">
                                <?=$productObj["NAME"]?>
                            </div>


                            <div class="medalPetBasket-product_bottom_line">
                                <?if($productObj["CAN_BUY"] != "N"):?>
                                    <div class="medalPetBasket-product_price">
                                        <?//print_p($productObj["PRICE"]);?>                                                        <?//print_p($value1["PRICE"]);?>
                                        <?=(($productObj["HAS_OFFERS"])?"от ":""  )?><?=CCurrencyLang::CurrencyFormat($productObj["PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"], $productObj["PRICE"]["RESULT_PRICE"]["CURRENCY"])?>
                                    </div>

                                    <div class="medalPetBasket-product_quantity">
                                        <div class="medalPetBasket-product_add"  onclick='addToBasket(<?=$productObj["ID"]?>)'>
                                            +
                                        </div>
                                        <div class="medalPetBasket-product_quantity_value">
                                            <?=$productObj["UF_QUANTITY"]?>
                                        </div>
                                        <div class="medalPetBasket-product_minus"  onclick='deleteFromBasket(<?=$productObj["ID"]?>)'>
                                            -
                                        </div>
                                    </div>
                                <?endif;?>
                            </div>

                            <div class="medalPetBasket-product_delete"  onclick='deleteAllFromBasket(<?=$productObj["ID"]?>)'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="13.749" height="13.749" viewBox="0 0 13.749 13.749">
                                    <g id="Сгруппировать_2352" data-name="Сгруппировать 2352" transform="translate(0.354 0.353)">
                                        <path id="Контур_1762" data-name="Контур 1762" d="M-358.336,6651.458l13.042,13.042" transform="translate(358.336 -6651.458)" fill="none" stroke="#b2b2b2" stroke-width="1"/>
                                        <path id="Контур_1763" data-name="Контур 1763" d="M-345.294,6651.458l-13.042,13.042" transform="translate(358.336 -6651.458)" fill="none" stroke="#b2b2b2" stroke-width="1"/>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    <?endforeach?>
                <?else:?>
                    <a href="<?=$sectionObj["INFO"]["URL_PATH"]?>" class="medalPetBasket-product medalPetBasket-productFiller">
                        <!-- <a href="<?=$sectionObj["INFO"]["URL_PATH"]?>"> -->
                        <img src="<?=$sectionObj["INFO"]["UF_BLACK_PLACEHOLDER_SRC"]?>" class="medalPetBasket-productFiller-black" alt="">
                        <img src="<?=$sectionObj["INFO"]["UF_GREY_PLACEHOLDER_SRC"]?>" class="medalPetBasket-productFiller-grey" alt="">
                        <!-- </a> -->
                    </a>
                <?endif;?>
            </div>
        </div>
    <?endforeach?>
    
    <div class="medalPetBasket-order swiper-slide">
        <?if(intval($arResult["TOTAL"]["PRICE"]) > 0):?>
            <div class="medalPetBasket-orderTotal"><b>Итого:&nbsp;</b><?=CCurrencyLang::CurrencyFormat($arResult["TOTAL"]["PRICE"], "RUB")?></div>
        <?endif;?>
        <a id="medalPetBasket-cont_<?=$componentId?>" class="strong_orange_btn" onclick="buyBtn()">Оформить заказ</a>
    </div>



    </div>
    <div id="medalPetBasket-cont_<?=$componentId?>-swiper-scrollbar" class="swiper-scrollbar"></div>
</div>

<?//print_p($arResult);?>

<?if($arResult["ORDER_STATUS"] == "SUCCESS"):?>
<div class="medalPetBasket-orderPopup-wrapper" onclick="console.log(event); if(this == event.target){this.remove()}" >
        <div class="medalPetBasket-orderPopup">
            <div class="medalPetBasket-orderPopup-close" onclick="this.parentElement.parentElement.remove()" ></div>
            <div class="medalPetBasket-orderPopup-title">
                
            </div>
            <div class="medalPetBasket-orderPopup-statusMessage-cont">
                <div class="medalPetBasket-orderPopup-statusMessage-img">
                    <img src="<?=$templateFolder?>/img/catpay.svg" alt="">
                </div>
                <div class="medalPetBasket-orderPopup-statusMessage-title">
                    Нам нужно обсудить все детали создания<br>медальона для Вашего питомца:)
                </div>
                <div class="medalPetBasket-orderPopup-statusMessage-subtitle">
                    Наш менеджер свяжется с Вами для оформления заказа 
                </div>
            </div>
        </div>
</div>
<?endif;?>


<?if($_GET["TYPE"] == "BUY"):?>
    <div class="medalPetBasket-orderPopup-wrapper" onclick="console.log(event); if(this == event.target){this.remove()}" >
        <div class="medalPetBasket-orderPopup">
            <div class="medalPetBasket-orderPopup-close" onclick="this.parentElement.parentElement.remove()" ></div>
            <div class="medalPetBasket-orderPopup-title">
                Оформление заказа
            </div>
            <form method="POST" class="medalPetBasket-orderPopup-cont" onsubmit="sendOrder(event, this)" onchange="updateProductPrice(event, this);">
                <?$sectNum = 0;?>
                <?foreach ($arResult["PRODUCTS"] as $key => $value):?>
                    <?if($value["INFO"]["ITEMS_COUNT"] > 0):?>
                            <div class="medalPetBasket-orderPopup-section">
                                <div class="medalPetBasket-orderPopup-sectionTitle">
                                    <span class="medalPetBasket-orderPopup-sectionNum"><?$sectNum += 1;?><?=$sectNum?></span>
                                    <span class="medalPetBasket-orderPopup-sectionName">
                                        <?=$value["INFO"]["NAME"]?>
                                    </span>
                                    <span class="medalPetBasket-orderPopup-sectionCount">
                                        <?=($value["INFO"]["ITEMS_COUNT"] > 1)?"(".$value["INFO"]["ITEMS_COUNT"]." шт)":""?>
                                    </span>                              
                                </div>
                                <div class="medalPetBasket-orderPopup-sectionProducts">
                                    <?$itemNum = 0?>
                                    <?foreach ($value["PRODUCTS"] as $key1 => $value1):?>
                                        <?if($value1["CAN_BUY"] != "N"):?>
                                            <?for ($i=0; $i < $value1["UF_QUANTITY"]; $i++):?>
                                                <div class="medalPetBasket-orderPopup-sectionProduct">
                                                    <div class="medalPetBasket-orderPopup-productLeftPart">
                                                        <div class="medalPetBasket-orderPopup-productImg">
                                                            <img src="<?=$value1["DETAIL_PICTURE_SRC"]?>" alt="">
                                                        </div>
                                                        <div class="medalPetBasket-orderPopup-productNum">
                                                            <?$itemNum += 1;?>
                                                            <?=($value["INFO"]["ITEMS_COUNT"] > 1)?$itemNum:""?>
                                                            <input type="hidden" name="<?="product_".$value1["ID"]."_".$i."_number"?>" value="<?=$itemNum?>">
                                                        </div>
                                                    </div>
                                                    <div class="medalPetBasket-orderPopup-productRightPart">
                                                        <div class="medalPetBasket-orderPopup-productTitle">
                                                            <?=$value1["NAME"]?>
                                                        </div>

                                                        <div class="medalPetBasket-orderPopup-productSubProps">
                                                            <?if($value1["PROPERTY_ART_VALUE"]):?>
                                                                <div class="medalPetBasket-orderPopup-productSubProp">
                                                                    Артикул: <?=$value1["PROPERTY_ART_VALUE"]?>
                                                                </div>
                                                            <?endif;?>
                                                            <?if($value1["PROPERTY_WEIGHT_VALUE"]):?>
                                                                <div class="medalPetBasket-orderPopup-productSubProp">
                                                                    Вес: <?=$value1["PROPERTY_WEIGHT_VALUE"]?>
                                                                </div>
                                                            <?endif;?>
                                                        </div>

                                                        <div class="medalPetBasket-orderPopup-priceCont" id="medalPetBasket_orderPopup_priceCont_<?=$value1["ID"]."_".$i?>">
                                                            <?//print_p($value1["PRICE"]);?>
                                                            <div class="medalPetBasket-orderPopup-orderPrice">
                                                                <?=(($value1["HAS_OFFERS"])?"от ":""  )?><?=CCurrencyLang::CurrencyFormat($value1["PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"], $value1["PRICE"]["RESULT_PRICE"]["CURRENCY"])?>
                                                            </div>

                                                            <?if( $value1["PRICE"]["DISCOUNT_PRICE"] != $value1["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]):?>
                                                                <div class="medalPetBasket-orderPopup-oldPrice">
                                                                    <?=(($value1["HAS_OFFERS"])?"от ":""  )?><?=CCurrencyLang::CurrencyFormat($value1["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $value1["PRICE"]["RESULT_PRICE"]["CURRENCY"])?>
                                                                </div>
                                                            <?endif;?>
                                                        </div>

                                                        <div class="medalPetBasket-orderPopup-productPropertys">
                                                            <?
                                                                $usedValues = array();

                                                                foreach ($arResult["PROPERTIES_INFO"] as $key2 => $value2){
                                                                    $usedValues[$key2] = array();
                                                                }


                                                                foreach ($value1["OFFERS"] as $key2 => $value2) {
                                                                    foreach ($arResult["PROPERTIES_INFO"] as $key3 => $value3){
                                                                        if(($value1["OFFERS"][$key2]["PROPERTY_".$key3."_VALUE"]) && ($value1["OFFERS"][$key2]["ACTIVE"] == "Y")){
                                                                            $usedValues[$key3][] = $value1["OFFERS"][$key2]["PROPERTY_".$key3."_VALUE"];
                                                                        }
                                                                    }
                                                                }

                                                            ?>
                                                                <?foreach ($arResult["PROPERTIES_INFO"] as $key2 => $value2):?>
                                                                    <?$itemPropertyId = "product_".$value1["ID"]."_".$i."_".$value2["INFO"]["ID"]?>
                                                                    <?if(count($usedValues[$key2]) > 0):?>
                                                                        <?if(( $value2["INFO"]["CODE"] == FONT_PROPERTY_CODE) || ( $value2["INFO"]["CODE"] == COLOR_PROPERTY_CODE) || ( $value2["INFO"]["CODE"] == PATTERN_PROPERTY_CODE)):?>
                                                                            <?$deffValKey = -1;?>
                                                                            <div class="medalPetBasket-orderPopup-productPropertyCont" id="<?=$itemPropertyId?>_cont">
                                                                                <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                                                                    <?=$value2["INFO"]["NAME"]?>
                                                                                </div>
                                                                                <div class="medalPetBasket-orderPopup-fontsInput" >
                                                                                    <div class="medalPetBasket-orderPopup-wrapper hidden" style="" id="<?=$itemPropertyId?>">
                                                                                        <div class="medalPetBasket-orderPopup">
                                                                                            <div class="medalPetBasket-orderPopup-close" data-value="<?=$itemPropertyId?>" onclick="fontChanged(this)" ></div>
                                                                                            <div class="medalPetBasket-orderPopup-title">
                                                                                                <?=( $value2["INFO"]["CODE"] == FONT_PROPERTY_CODE)?"Варианты шрифтов":(( $value2["INFO"]["CODE"] == COLOR_PROPERTY_CODE)?"Варианты цветов":(( $value2["INFO"]["CODE"] == PATTERN_PROPERTY_CODE)?"Варианты рисунков":""))?>
                                                                                            </div>
                                                                                            <div class="medalPetBasket-orderPopup-content fontInputs-cont">
                                                                                                <div class="medalPetBasket-orderPopup-content fontInputs-item">
                                                                                                    <input class="fontInputs-input" type="radio" name="<?=$itemPropertyId?>" id="<?=$itemPropertyId?>_-1" value="-1" checked>
                                                                                                    <label class="fontInputs-label" for="<?=$itemPropertyId?>_-1"><p>Не выбрано</p><img src="<?=$templateFolder . "/img/defaultPicture.png"?>" alt=""></label>
                                                                                                </div>
                                                                                                <!-- <input class="fontInputs-input" type="radio" name="<?=$itemPropertyId?>" id="<?=$itemPropertyId?>_-" value="-1" checked> -->
                                                                                                <?foreach ($value2["VALUES"] as $key3 => $value3):?>
                                                                                                    <?//print_p($value3);?>
                                                                                                    <?if(in_array($value3["UF_XML_ID"], $usedValues[$key2])):?>
                                                                                                        <?if($value3["UF_DEF"] == 1){
                                                                                                            $deffValKey = $key3;
                                                                                                        }?>
                                                                                                        <div class="medalPetBasket-orderPopup-content fontInputs-item">
                                                                                                            <input class="fontInputs-input" type="radio" name="<?=$itemPropertyId?>" id="<?=$itemPropertyId?>_<?=$value3["UF_XML_ID"]?>" value="<?=$value3["UF_XML_ID"]?>" <?=($value3["UF_DEF"] == 1)?"checked":""?>>
                                                                                                            <label class="fontInputs-label" for="<?=$itemPropertyId?>_<?=$value3["UF_XML_ID"]?>"><p><?=$value3["UF_NAME"]?></p>
                                                                                                                <img src="<?=($value3["UF_FILE_PATH"])?$value3["UF_FILE_PATH"]:$templateFolder . "/img/defaultPicture.png"?>" alt="">
                                                                                                            </label>
                                                                                                        </div>
                                                                                                    <?endif;?>
                                                                                                <?endforeach?>
                                                                                            </div>
                                                                                            <div class="strong_orange_btn" data-value="<?=$itemPropertyId?>" onclick="fontChanged(this)"> Готово </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="medalPetBasket-orderPopup-fontsInput-input" onclick="fontInputState(this.parentElement)" id="<?=$itemPropertyId?>_visible">
                                                                                        <p class="medalPetBasket-orderPopup-fontsInput-inputValue"><?=($deffValKey != -1)?$value2["VALUES"][$deffValKey]["UF_NAME"]:"Нажать чтобы выбрать"?></p>
                                                                                        
                                                                                        <p class="medalPetBasket-orderPopup-fontsInput-chevron">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="12.707" height="7.062" viewBox="0 0 12.707 7.062">
                                                                                                <path id="Контур_1744" data-name="Контур 1744" d="M2186,7111l5.96,6,6.04-6" transform="translate(-2185.645 -7110.646)" fill="none" stroke="#000" stroke-width="1"/>
                                                                                            </svg>
                                                                                        </p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <?//print_p($key2);?>
                                                                            <?//print_p($value2);?>
                                                                        <?else:;?>
                                                                                <div class="medalPetBasket-orderPopup-productPropertyCont" id="<?=$itemPropertyId?>_cont">
                                                                                    <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                                                                        <?=$value2["INFO"]["NAME"]?>
                                                                                    </div>
                                                                                    <select name="<?=$itemPropertyId?>">
                                                                                        <option value="-1">--</option>
                                                                                        <?foreach ($value2["VALUES"] as $key3 => $value3):?>
                                                                                            <?if(in_array($value3["UF_XML_ID"], $usedValues[$key2])):?>
                                                                                                <option value="<?=$value3["UF_XML_ID"]?>"> <img src="<?=$value1["DETAIL_PICTURE_SRC"]?>" alt=""> <?=$value3["UF_NAME"]?></option>
                                                                                            <?endif;?>
                                                                                        <?endforeach?>
                                                                                    </select>
                                                                                </div>
                                                                        <?endif;?>
                                                                    <?endif;?>
                                                                <?endforeach;?>

                                                                <?if($key == $arParams["MEDAL_SECT"]):?>
                                                                    <div class="medalPetBasket-orderPopup-productPropertyCont" id="product_<?=$value1["ID"]?>_<?=$i?>_face_cont">
                                                                        <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                                                            Текст на лицевой стороне
                                                                        </div>
                                                                        <textarea resize=none name="product_<?=$value1["ID"]?>_<?=$i?>_face" id="" cols="25" maxlength="25" rows="1" placeholder="От 1-ого до 25-ти символов"></textarea>
                                                                    </div>
                                                                    <div class="medalPetBasket-orderPopup-productPropertyCont" id="product_<?=$value1["ID"]?>_<?=$i?>_side_cont">
                                                                        <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                                                            Текст на обратной стороне
                                                                        </div>
                                                                        <textarea resize="none"  name="product_<?=$value1["ID"]?>_<?=$i?>_side" id="" cols="25" maxlength="25" rows="1" placeholder="От 1-ого до 25-ти символов"></textarea>
                                                                    </div>
                                                                <?endif;?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?endfor;?>
                                        <?endif;?>
                                    <?endforeach;?>
                                </div>
                            </div>
                    <?endif;?>
                <?endforeach;?>

                <div class="medalPetBasket-orderPopup-section">
                    <div class="medalPetBasket-orderPopup-sectionTitle">
                        <span class="medalPetBasket-orderPopup-sectionNum"><?$sectNum += 1;?><?=$sectNum?></span>
                        Информация о покупателе
                    </div>
                    <div class="medalPetBasket-orderPopup-customerPropertyCont">
                        <div class="medalPetBasket-orderPopup-customerPropertyPrivacy">
                            Укажите свои данные, чтобы быть в курсе изменений статуса заказа. 
                            Персональные данные обрабатываются в соответствии с <a href="/privacy/">политикой конфиденциальности</a>
                        </div>
                    </div>
                    <div class="medalPetBasket-orderPopup-sectionCustomer">
                        <div class="medalPetBasket-orderPopup-customerPropertyCont">
                            <!-- <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                
                            </div> -->
                            <input name="customer_name" type="text" placeholder="Имя"/>
                        </div>
                        <div class="medalPetBasket-orderPopup-customerPropertyCont">
                            <!-- <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                
                            </div> -->
                            <input name="customer_surname" type="text" placeholder="Фамилия"/>
                        </div>
                        <div class="medalPetBasket-orderPopup-customerPropertyCont">
                            <!-- <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                
                            </div> -->
                            <input name="customer_phone" type="phone" placeholder="Телефон"/>
                        </div>
                        <div class="medalPetBasket-orderPopup-customerPropertyCont">
                            <!-- <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                
                            </div> -->
                            <input name="customer_email" type="email" placeholder="E-mail"/>
                        </div>
                        <div class="medalPetBasket-orderPopup-customerPropertyCont">
                            <!-- <div class="medalPetBasket-orderPopup-productPropertyTitle">
                                
                            </div> -->
                            <textarea resize="none" placeholder="Комментарий" name="customer_comment"></textarea>
                        </div>
                    </div>
                    <div class="medalPetBasket-orderPopup-customerPropertyCont">
                        <div class="medalPetBasket-orderPopup-customerPropertyInfo">
                            Отправляя заявку вы соглашаетесь с политикой конфиденциальности. 
                            Все товары заказываются и производятся по заказу. Полную информацию о продукте уточняйте у менеджера. 
                        </div>
                    </div>
                </div>


                <div class="medalPetBasket-orderPopup-section">
                    <div class="medalPetBasket-orderPopup-sectionTitle">
                        <span class="medalPetBasket-orderPopup-sectionNum"><?$sectNum += 1;?><?=$sectNum?></span>
                        Итого
                    </div>
                    <div class="medalPetBasket-orderPopup-customerPropertyCont">
                        <div class="medalPetBasket-orderPopup-total">
                            <b>Количество:&nbsp;</b><span><?=$arResult["TOTAL"]["COUNT"]?> шт.</span><br>
                            <input name="customer_totalQuantity" type="hidden" value="<?=$arResult["TOTAL"]["COUNT"]?> шт.">
                            <b>Сумма:&nbsp;</b><span><?=CCurrencyLang::CurrencyFormat($arResult["TOTAL"]["PRICE"], "RUB")?></span>
                            <input name="customer_totalPrice" type="hidden" value="<?=CCurrencyLang::CurrencyFormat($arResult["TOTAL"]["PRICE"], "RUB")?>">
                        </div>
                    </div>
                </div>

                <input type="submit" class="medalPetBasket-orderPopup-btn strong_orange_btn" onclick="" />
            </form>
        </div>
    </div>

    

    <script>
        <?
            $propsMap = [
                "face" => "Текст на лицевой стороне", 
                "side" => "Текст на обратной стороне", 
            ];
            $productsMap = [];


            foreach ($arResult["PROPERTIES_INFO"] as $key2 => $value2){
                $propsMap[$value2["INFO"]["ID"]] = $value2["INFO"]["NAME"];
            }

            foreach ($arResult["PRODUCTS"] as $key => $value){
                foreach ($value["PRODUCTS"] as $key1 => $value1){
                    $productsMap[$value1["ID"]] = $value1["NAME"];
                }
            }
        ?>

        var props_<?=$componentId?> = <?=json_encode($propsMap)?>;
        var products_<?=$componentId?> = <?=json_encode($productsMap)?>;
    </script>

<?endif?>
<?if($arParams["AJAX"] != "Y"):?>
</div>
<?endif?>
<?if($arParams["AJAX"] != "Y"):?>


    <?CJSCore::Init(array("jquery"));?>
    <?
        $signer = new \Bitrix\Main\Security\Sign\Signer;
        $clearParams = array();
        foreach ($arParams as $key => $value) {
            if( $key[0] != "~" ){
                $clearParams[$key] = $value;
            }
        }
        $signedParams = $signer->sign(base64_encode(serialize($arParams)), 'medalpetBasket');
    ?>
    <script>

        function updateProductPrice(event, form, priceContId) {

            let propertysIds = <?=json_encode($arParams["OFFERS_PROPS"], true)?>;
            let productIdArray = event.srcElement.id.split("_");
            let formData = $(form).serializeArray();
            let propertiesCode = [];
            let post = {"selectedProperties":{}};

            propertysIds.forEach(propertyId => {
                propertiesCode.push("product_" + productIdArray[1] + "_" + productIdArray[2] + "_" + propertyId);
            });

            formData.forEach(formvalue => {

                if(propertiesCode.includes(formvalue.name)){
                    post["selectedProperties"][formvalue.name.split("_")[3]] = formvalue.value;
                }
            });    

            // console.log('onchange.event', event); 
            // console.log('onchange.form', form);
            // console.log('onchange.input_id', event.srcElement.id);
            // console.log('onchange.productIdArray', productIdArray);
            // console.log('onchange.propertysIds', propertysIds);
            // console.log('onchange.propertiesCode', propertiesCode);
            // console.log('onchange.post', post);

            if(productIdArray[0] == "product"){
                BX.showWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                $.ajax({
                    url: `<?=$componentPath?>/ajax.php?IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&TYPE=GETPRICE&MODE=AJAX&PRODUCT_ID=` + productIdArray[1],         /* Куда отправить запрос */
                    method: 'post',             /* Метод запроса (post или get) */
                    dataType: 'json',          /* Тип данных в ответе (xml, json, script, html). */
                    data: post,
                    success: function(data){   /* функция которая будет выполнена после успешного запроса.  */

                        console.log("medalPetBasket_orderPopup_priceCont_"+productIdArray[1]+"_"+productIdArray[2]); /* В переменной data содержится ответ от index.php. */
                        console.log(data); /* В переменной data содержится ответ от index.php. */

                        let priceCont = document.getElementById("medalPetBasket_orderPopup_priceCont_"+productIdArray[1]+"_"+productIdArray[2]);

                        let productPrice = document.createElement("div");
                        let oldPrice = document.createElement("div");
                        productPrice.className = "medalPetBasket-orderPopup-orderPrice";
                        productPrice.innerHTML = data.minPrice.PRICE.RESULT_PRICE.PRINT_BASE_PRICE;



                        if( data.maxPrice.PRICE.RESULT_PRICE.DISCOUNT_PRICE != data.minPrice.PRICE.RESULT_PRICE.BASE_PRICE){
                            oldPrice.className = "medalPetBasket-orderPopup-oldPrice";
                            oldPrice.innerHTML = data.minPrice.PRICE.RESULT_PRICE.PRINT_BASE_PRICE;
                            productPrice.innerHTML = data.minPrice.PRICE.RESULT_PRICE.PRINT_DISCOUNT_PRICE;
                        }

                        if(data.minPrice.PRICE.RESULT_PRICE.BASE_PRICE != data.maxPrice.PRICE.RESULT_PRICE.BASE_PRICE){
                            productPrice.innerHTML = "от " + productPrice.innerText;
                            oldPrice.innerHTML = "от " + oldPrice.innerText;
                        }
                        
                        priceCont.innerText = "";
                        priceCont.insertAdjacentElement("beforeend", productPrice);
                        priceCont.insertAdjacentElement("beforeend", oldPrice);
                            // <div class="">
                            //     от <?=CCurrencyLang::CurrencyFormat($value1["PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"], $value1["PRICE"]["RESULT_PRICE"]["CURRENCY"])?>
                            // </div>

                            // <?if( $value1["PRICE"]["DISCOUNT_PRICE"] != $value1["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]):?>
                            //     <div class="medalPetBasket-orderPopup-oldPrice">
                            //         <?=CCurrencyLang::CurrencyFormat($value1["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $value1["PRICE"]["RESULT_PRICE"]["CURRENCY"])?>
                            //     </div>
                            // <?endif;?>
                        BX.closeWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                    }
                });
            }
        }

        function initSwiper(){
            console.log("initSwiper");

            let medalpetBasketSwiper = new Swiper("#medalPetBasket-cont_<?=$componentId?>-swiper", {
                freeMode: true,
                slidesPerView: "auto",
                mousewheel: true,
                scrollbar: {
                    el: "#medalPetBasket-cont_<?=$componentId?>-swiper-scrollbar",
                },
            });

            console.log("initSwiper", medalpetBasketSwiper);


            return medalpetBasketSwiper;
        }

        initSwiper();


        let params_<?=$componentId?> = '<?=CUtil::JSEscape($signedParams)?>';

        function sendOrder(event, form) {
            event.preventDefault();

            console.log("props", props_<?=$componentId?>);
            console.log("products", products_<?=$componentId?>);

            let formData = $(form).serializeArray();

            let hasError = false;

            let propsArr = {};
            let errorsArr = [];


            formData.forEach(element => {


                let field_data = element.name.split('_');

                if(field_data[0] == "customer"){
                    // if(element.value == ""){
                    //     $("*[name="+element.name+"]")[0].classList.add("error");
                    //     hasError = true;
                    // }

                    if(!propsArr[field_data[0]]){
                        propsArr[field_data[0]] = {};
                    }

                    if(!propsArr[field_data[0]][field_data[1]]){
                        propsArr[field_data[0]][field_data[1]] = element.value;
                    }
                }
                else{
                    if(!propsArr[field_data[0]]){
                        propsArr[field_data[0]] = {};
                    }

                    if(!propsArr[field_data[0]][field_data[1]]){
                        propsArr[field_data[0]][field_data[1]] = {};
                    }

                    if(!propsArr[field_data[0]][field_data[1]][field_data[2]]){
                        propsArr[field_data[0]][field_data[1]][field_data[2]] = {};

                        let priceCont = document.getElementById("medalPetBasket_orderPopup_priceCont_" + field_data[1] + "_" + field_data[2] );

                        propsArr[field_data[0]][field_data[1]][field_data[2]].price = {};
                        propsArr[field_data[0]][field_data[1]][field_data[2]].price.value = priceCont.querySelector(".medalPetBasket-orderPopup-orderPrice")?.innerText;
                        propsArr[field_data[0]][field_data[1]][field_data[2]].price.text = priceCont.querySelector(".medalPetBasket-orderPopup-orderPrice")?.innerText;
                        propsArr[field_data[0]][field_data[1]][field_data[2]].basePrice = {};
                        propsArr[field_data[0]][field_data[1]][field_data[2]].basePrice.value = priceCont.querySelector(".medalPetBasket-orderPopup-oldPrice")?.innerText;
                        propsArr[field_data[0]][field_data[1]][field_data[2]].basePrice.text = priceCont.querySelector(".medalPetBasket-orderPopup-oldPrice")?.innerText;
                    }

                    if(!propsArr[field_data[0]][field_data[1]][field_data[2]][field_data[3]]){
                        if((field_data[3] != "side") && (field_data[3] != "face") && (field_data[3] != "number")){
                            if(element.value == "-1"){
                                $("#"+element.name+"_cont")[0].classList.add("error");

                                let field_name = field_data[3];
                                let product_name = field_data[1];

                                errorsArr.push(`Не указано свойство '${props_<?=$componentId?>[field_name]}' у товара '${products_<?=$componentId?>[product_name]}'`);
                            }
                            else{
                                $("#"+element.name+"_cont")[0].classList.remove("error");
                            }

                            propsArr[field_data[0]][field_data[1]][field_data[2]][field_data[3]] = {};
                            propsArr[field_data[0]][field_data[1]][field_data[2]][field_data[3]].value = element.value;
                            propsArr[field_data[0]][field_data[1]][field_data[2]][field_data[3]].text = document.querySelector("label[for="+(element.name + "_" + element.value)+"] p").innerText;
                        }
                        else{
                            console.log(element);
                            if(field_data[3] != "number"){
                                if(element.value == ""){
                                    $("#"+element.name+"_cont")[0].classList.add("error");
                                    
                                    let field_name = field_data[3];
                                    let product_name = field_data[1];

                                    errorsArr.push(`Не указано свойство '${props_<?=$componentId?>[field_name]}' у товара '${products_<?=$componentId?>[product_name]}'`);
                                }
                                else{
                                    $("#"+element.name+"_cont")[0].classList.remove("error");
                                }
                            }

                            propsArr[field_data[0]][field_data[1]][field_data[2]][field_data[3]] = {};
                            propsArr[field_data[0]][field_data[1]][field_data[2]][field_data[3]].value = element.value;
                            propsArr[field_data[0]][field_data[1]][field_data[2]][field_data[3]].text = element.value;
                        }
                    }
                }
            });

            if( (propsArr["customer"]["name"] == "") && (propsArr["customer"]["surname"] == "") ){
                $("*[name=customer_name]")[0].classList.add("error");
                $("*[name=customer_surname]")[0].classList.add("error");

                errorsArr.push("Не заполнена контактная информация: Имя или Фамилия");
            }
            else{
                $("*[name=customer_name]")[0].classList.remove("error");
                $("*[name=customer_surname]")[0].classList.remove("error");
            }

            if( (propsArr["customer"]["phone"] == "") && (propsArr["customer"]["email"] == "") ){
                $("*[name=customer_phone]")[0].classList.add("error");
                $("*[name=customer_email]")[0].classList.add("error");

                errorsArr.push("Не заполнена контактная информация: Телефон или Почта");
            }
            else{
                $("*[name=customer_phone]")[0].classList.remove("error");
                $("*[name=customer_email]")[0].classList.remove("error");
            }

            if(errorsArr.length > 0){
                // alert("Ошибка заполнения!");
                // console.log("errors", errorsArr);
                // console.log("errors", propsArr);
                openErrorPopup(errorsArr);
            }
            else{
                BX.showWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                $.ajax({
                    url: `<?=$componentPath?>/ajax.php?IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&HBL_ID=<?=$arParams["HBL_ID"]?>&TYPE=SAVE&MODE=AJAX`,         /* Куда отправить запрос */
                    method: 'post',             /* Метод запроса (post или get) */
                    dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
                    data: {"params": params_<?=$componentId?>, "savedData": propsArr},
                    success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                        $("#medalPetBasket-cont_<?=$componentId?>-wrapper").html(data);
                        console.log(data); /* В переменной data содержится ответ от index.php. */
                        initSwiper();
                        BX.closeWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                    }
                });
                console.log("success", propsArr);
            }
        }

        function fontInputState(elem){
            elem.querySelector(".medalPetBasket-orderPopup-wrapper").classList.toggle('hidden');
        }

        function fontChanged(elem){
            console.log(elem.dataset.value);

            let selectedValueText = document.getElementById(elem.dataset.value).querySelector(".fontInputs-input:checked + label > p").innerText;

            document.getElementById(elem.dataset.value + "_visible").querySelector(".medalPetBasket-orderPopup-fontsInput-inputValue").innerText = selectedValueText;
            fontInputState(document.getElementById(elem.dataset.value).parentElement);
            // console.log("fontChanged", );
            // console.log("fontChanged", elem.parentElement.querySelector("label[for='" + elem.id + "'] p").innerText);
        }


        function addToBasket(id){
            BX.showWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
            $.ajax({
                url: `<?=$componentPath?>/ajax.php?ID=${id}&IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&HBL_ID=<?=$arParams["HBL_ID"]?>&TYPE=ADD&MODE=AJAX`,         /* Куда отправить запрос */
                method: 'post',             /* Метод запроса (post или get) */
                dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
                data: {"params": params_<?=$componentId?>},
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    $("#medalPetBasket-cont_<?=$componentId?>-wrapper").html(data);
                    console.log(data); /* В переменной data содержится ответ от index.php. */
                    initSwiper();
                    BX.closeWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                }
            });
        }

        function buyBtn(id){
            BX.showWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
            $.ajax({
                url: `<?=$componentPath?>/ajax.php?ID=${id}&IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&HBL_ID=<?=$arParams["HBL_ID"]?>&TYPE=BUY&MODE=AJAX`,         /* Куда отправить запрос */
                method: 'post',             /* Метод запроса (post или get) */
                dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
                data: {"params": params_<?=$componentId?>},
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    $("#medalPetBasket-cont_<?=$componentId?>-wrapper").html(data);
                    console.log(data); /* В переменной data содержится ответ от index.php. */
                    initSwiper();
                    BX.closeWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                }
            });
        }

        function deleteFromBasket(id){
            BX.showWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
            $.ajax({
                url: `<?=$componentPath?>/ajax.php?ID=${id}&IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&HBL_ID=<?=$arParams["HBL_ID"]?>&TYPE=DELETE&MODE=AJAX`,         /* Куда отправить запрос */
                method: 'post',             /* Метод запроса (post или get) */
                data: {"params": params_<?=$componentId?>},
                dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    $("#medalPetBasket-cont_<?=$componentId?>-wrapper").html(data);
                    console.log(data); /* В переменной data содержится ответ от index.php. */
                    initSwiper();
                    BX.closeWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                }
            });
        }

        function deleteAllFromBasket(id){
            BX.showWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
            $.ajax({
                url: `<?=$componentPath?>/ajax.php?ID=${id}&IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&HBL_ID=<?=$arParams["HBL_ID"]?>&TYPE=DELETE&MODE=AJAX&QUANTITY=0`,         /* Куда отправить запрос */
                method: 'post',             /* Метод запроса (post или get) */
                data: {"params": params_<?=$componentId?>},
                dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    $("#medalPetBasket-cont_<?=$componentId?>-wrapper").html(data);
                    console.log(data); /* В переменной data содержится ответ от index.php. */
                    initSwiper();
                    BX.closeWait($("#medalPetBasket-cont_<?=$componentId?>-wrapper")[0]);
                }
            });
        }

        function openErrorPopup(errorsArr){
            let popupBg =  document.createElement("div");
            popupBg.className = "medalPetBasket-errorPopup-bg";

            let popupCont =  document.createElement("div");
            popupCont.className = "medalPetBasket-errorPopup-cont";
            popupBg.insertAdjacentElement("beforeend", popupCont);

            let popupTitle =  document.createElement("div");
            popupTitle.className = "medalPetBasket-errorPopup-title";
            popupTitle.innerText = "Ошибка заполнения!";
            popupCont.insertAdjacentElement("beforeend", popupTitle);

            let popupText =  document.createElement("div");
            popupText.className = "medalPetBasket-errorPopup-text";
            errorsArr.forEach(element => {
                let errorLine =  document.createElement("p");
                errorLine.innerText = element;
                popupText.insertAdjacentElement("beforeend", errorLine);
            });
            popupCont.insertAdjacentElement("beforeend", popupText);

            let popupBtn =  document.createElement("div");
            popupBtn.className = "medalPetBasket-errorPopup-btn strong_orange_btn";
            popupBtn.innerText = "Ок";
            popupBtn.onclick = (e) => {popupBg.remove();};
            popupCont.insertAdjacentElement("beforeend", popupBtn);

            document.body.insertAdjacentElement("beforeend", popupBg);
        }

    </script>
<?endif?>