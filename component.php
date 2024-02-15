<?
define('TG_BOT_TOKEN', '****');


Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");
Bitrix\Main\Loader::includeModule("highloadblock");
Bitrix\Main\Loader::includeModule("iblock");

$application  =  \Bitrix\Main\Application::getInstance();
$context  =  $application ->getContext();
$fuser = \Bitrix\Sale\Fuser::getId();

//Получение информации о медальонах в корзине
use Bitrix\Highloadblock as HL;
$hlblock   = HL\HighloadBlockTable::getById( $arParams["HBL_ID"] )->fetch();
$entity   = HL\HighloadBlockTable::compileEntity( $hlblock ); //генерация класса 
$entityClass = $entity->getDataClass();

$rsData = $entityClass::getList(array(
   'order' => array(),
   'select' => array('*'),
   'filter' => array('UF_FUSER' => $fuser)
));

$products = $rsData->fetchAll();

//Добавление медальона в корзину/Увеличение кол-ва в корзине
if(( $_GET["MODE"] == "AJAX" ) && ( $_GET["TYPE"] == "ADD" )){
    $arrayId = -1;

    foreach ($products as $key => $value) {
        if( $value["UF_PRODUCT_ID"] == $_GET["ID"] ){
            $arrayId = $key;    
        }
    }

    if( $arrayId == -1 ){
        $newArrData = array(
            'UF_PRODUCT_ID' => $_GET["ID"],
            'UF_FUSER' => $fuser,
            'UF_QUANTITY' => 1,
        );

        $result = $entityClass::add($newArrData);
    
        $products[] = $newArrData;
        
    }
    else{
        $result = $entityClass::update($products[$arrayId]["ID"], array(
            'UF_QUANTITY'   => $products[$arrayId]["UF_QUANTITY"] + 1
        ));

        $products[$arrayId]["UF_QUANTITY"] += 1;
    }
}

//Удаление медальона из корзины/Уменьшение кол-ва к корзине
if(( $_GET["MODE"] == "AJAX" ) && ( $_GET["TYPE"] == "DELETE" )){
    $arrayId = -1;

    foreach ($products as $key => $value) {
        if( $value["UF_PRODUCT_ID"] == $_GET["ID"] ){
            $arrayId = $key;    
        }
    }

    if( $arrayId != -1 ){
        if(( ($products[$arrayId]["UF_QUANTITY"] - 1) <= 0 ) || ( isset($_GET["QUANTITY"]) && ($_GET["QUANTITY"] == 0) ) ){
            $result = $entityClass::delete($products[$arrayId]["ID"]);
            unset($products[$arrayId]);
        }
        else{
            $result = $entityClass::update($products[$arrayId]["ID"], array(
                'UF_QUANTITY'   => $products[$arrayId]["UF_QUANTITY"] - 1
            ));

            $products[$arrayId]["UF_QUANTITY"] -= 1;
        }
    }
}
$arResult["ORDER_STATUS"] = "";

//"Оформление заказа" - Отправка уведомления в телегу и удаление товаров из корзины
if(( $_GET["MODE"] == "AJAX" ) && ( $_GET["TYPE"] == "SAVE" )){
    $itemNum = 0;
    $dbSaveArr = [
        "UF_USER_ID" => $fuser,
        "UF_PRODUCTS_STRINGS" => [],
        "UF_USER_INFO" => [],
    ];

    if( isset($_POST["savedData"]["product"]) ){

        $ids = array_keys($_POST["savedData"]["product"]);
        $rs = CIBlockElement::GetList(
            array(), 
            array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"], 
                "ID" => $ids, 
            ),
            false, 
            false,
            array("ID", "NAME", "DETAIL_PICTURE", "CODE", "PROPERTY_ART", "PROPERTY_WEIGHT"),
        );

        $messageText = "";

        while($item = $rs->GetNext()){
            $count = count($_POST["savedData"]["product"][$item["ID"]]);
            $propertiesData = array();
            foreach ($_POST["savedData"]["product"][$item["ID"]] as $key => $value) {
                $itemNum += 1;

                $tempMessage = "№{$itemNum}  [{$item["ID"]}]{$item["NAME"]}:\n";

                foreach ($_POST["savedData"]["product"][$item["ID"]][$key] as $key1 => $value1) {
                    if( ($key1 != "price") && ($key1 != "basePrice") && ($key1 != "number") ){
                        if($key1 == 'side'){
                            $propertyName = 'Обратная сторона';
                        }
                        else                
                        if($key1 == 'face'){
                            $propertyName = 'Лицевая сторона';
                        }
                        else                
                        if($key1 == 'price'){
                            $propertyName = 'Цена';
                        }
                        else                
                        if($key1 == 'basePrice'){
                            $propertyName = 'Цена без скидок';
                        }
                        else{
                            if(!in_array($key1, $propertiesData)){
                                $propertiesData[$key1] = CIBlockProperty::GetByID($key1)->Fetch();
                            }
                            $propertyName = $propertiesData[$key1]["NAME"];
                        }
                        $tempMessage .= "{$propertyName} - {$value1["text"]}\n";
                    }
                }

                $tempMessage .= "-------------------\n";
                $tempMessage .= "Цена - {$_POST["savedData"]["product"][$item["ID"]][$key]["price"]["text"]}\n";
                if(isset($_POST["savedData"]["product"][$item["ID"]][$key]["basePrice"])){
                    $tempMessage .= "Цена без скидок - {$_POST["savedData"]["product"][$item["ID"]][$key]["basePrice"]["text"]}\n";
                }
                $tempMessage .= "-------------------\n";


                $tempMessage .= "\n";
                $dbSaveArr["UF_PRODUCTS_STRINGS"][] = $tempMessage;

                $messageText .=  $tempMessage;
            }
        }

    }

    $messageText .= "\n---------ИТОГО----------\n";
    $messageText .= "Кол-во: {$_POST["savedData"]["customer"]["totalQuantity"]}\n";
    $messageText .= "Сумма: {$_POST["savedData"]["customer"]["totalPrice"]}\n";
    $messageText .= "-------------------\n";

    $dbSaveArr["UF_USER_INFO"][] = "Имя: ".$_POST["savedData"]["customer"]["name"];
    $dbSaveArr["UF_USER_INFO"][] = "Фамилиия: ".$_POST["savedData"]["customer"]["surname"];
    $dbSaveArr["UF_USER_INFO"][] = "Телефон: ".$_POST["savedData"]["customer"]["phone"];
    $dbSaveArr["UF_USER_INFO"][] = "E-mail: ".$_POST["savedData"]["customer"]["email"];
    $dbSaveArr["UF_USER_INFO"][] = "Комментарий: ".$_POST["savedData"]["customer"]["comment"];

    $hlbl = 11; 
    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 
    
    $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
    $entity_data_class = $entity->getDataClass(); 
    
    $res = $entity_data_class::add($dbSaveArr);

    $orderNum = $res->getId();



    if( $orderNum ){
        $messageText = "Получен новый заказ на медальоны! №{$orderNum}\n\n" . $messageText;

        foreach ($products as $key => $value) {
            $result = $entityClass::delete($value["ID"]);
            if(!$result->isSuccess())
                $errors = $result->getErrorMessages();
        }

        $products = array();

        $data = [
            'chat_id' => '1464862288',
            // 'chat_id' => '316129707',
            'text' => $messageText,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://api.telegram.org/bot".TG_BOT_TOKEN."/sendMessage",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
        ]);
        $result = curl_exec($curl);
        curl_close($curl);


        $messageText = 
            "Имя: ".$_POST["savedData"]["customer"]["name"]."\nФамилиия: ".$_POST["savedData"]["customer"]["surname"]."\nТелефон: [".$_POST["savedData"]["customer"]["phone"]."](tel:".$_POST["savedData"]["customer"]["phone"].")\nE-mail: ".$_POST["savedData"]["customer"]["email"]."\nКомментарий: \n".$_POST["savedData"]["customer"]["comment"];
        $messageText = "Личные данные покупателя из заказа №{$orderNum}\n\n" . $messageText;

        $data = [
            'chat_id' => '1464862288',
            // 'chat_id' => '316129707',
            'text' => $messageText,
            'parse_mode' => "markdown",
            'reply_markup' => [
                'inline_keyboard' => 
                [
                    [
                        [
                            "text" => "Позвонить",
                            "url" => "https://".$_SERVER["HTTP_HOST"].$componentPath."/tgBotPhoneOpen.php?phone_num=".$_POST["savedData"]["customer"]["phone"]
                        ]
                    ]
                ]
            ],
        ];

        

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://api.telegram.org/bot".TG_BOT_TOKEN."/sendMessage",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        $arResult["ORDER_STATUS"] = "SUCCESS";
    }
    else{
        $data = [
            'chat_id' => '1464862288',
            'text' => "Ошибка оформления заказа! RAWDATA - " . json_encode($_POST),
            'parse_mode' => "markdown",
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://api.telegram.org/bot".TG_BOT_TOKEN."/sendMessage",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"))
        ]);
        $result = curl_exec($curl);
        curl_close($curl);  
        $arResult["ORDER_STATUS"] = "ERROR";
    }
}

$cookieFuser  =  new   \Bitrix\Main\Web\Cookie( "MedalPetBasketFuser" ,  \Bitrix\Sale\Fuser::getId() , time() +  60 * 60 * 24 * 60 );
$cookieFuser ->setDomain( $context ->getServer()->getHttpHost());
$cookieFuser ->setHttpOnly( false );

$context ->getResponse()->addCookie( $cookieFuser );
$context->getResponse()->writeHeaders("");

$resultProducts = array();

$ids = array();
$quantity = array();
$total_price = 0;
$total_quantity = 0;

foreach ($products as $key => $value) {
    $ids[] = $value["UF_PRODUCT_ID"];
    $quantity[$value["UF_PRODUCT_ID"]] = $value["UF_QUANTITY"];
}

//Собираем информацию о медальонах в корзине
$arFilter = Array('IBLOCK_ID' => $arParams["IBLOCK_ID"], "DEPTH_LEVEL" => 1);
$db_list =  CIBlockSection::GetList(array(), $arFilter, true, array("UF_BLACK_PLACEHOLDER", "UF_GREY_PLACEHOLDER", "NAME", "ID", "CODE"));
$arFilesIds = array();
while($ar1 = $db_list->GetNext()) {
    $resultProducts[$ar1["ID"]] = array("INFO" => array(), "PRODUCTS" => array());
    $resultProducts[$ar1["ID"]]["INFO"] = $ar1;
    $resultProducts[$ar1["ID"]]["INFO"]["URL_PATH"] = str_replace("#SECTION_CODE#", $ar1["CODE"], $arParams["SECTION_URL_TEMPLATE"]);
    $resultProducts[$ar1["ID"]]["INFO"]["ITEMS_COUNT"] = 0;

    $resultProducts[$ar1["ID"]]["INFO"]["UF_BLACK_PLACEHOLDER_SRC"] = CFile::GetById($ar1["UF_BLACK_PLACEHOLDER"])->getNext()["SRC"];
    $resultProducts[$ar1["ID"]]["INFO"]["UF_GREY_PLACEHOLDER_SRC"] = CFile::GetById($ar1["UF_GREY_PLACEHOLDER"])->getNext()["SRC"];

    if(count($ids) > 0){
        $rs = CIBlockElement::GetList(
            array(), 
            array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"], 
                "ID" => $ids, 
                "SUBSECTION" => $ar1["ID"], 
            ),
            false, 
            false,
            array("ID", "NAME", "DETAIL_PICTURE", "PREVIEW_PICTURE", "CODE", "PROPERTY_ART", "PROPERTY_WEIGHT", "CATALOG_AVAILABLE", "AVAILABLE"),
        );
        
        while($ar = $rs->GetNext()) {

            $ar["UF_QUANTITY"] = $quantity[$ar["ID"]];

            $ar["HAS_OFFERS"] = CCatalogSKU::getExistOffers($ar["ID"])[$ar["ID"]];
            $ar["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
            $ar["PRICE"] = CCatalogProduct::GetOptimalPrice($ar["ID"], $ar["UF_QUANTITY"], $USER->GetUserGroupArray());

            $picID = ($ar["DETAIL_PICTURE"])?$ar["DETAIL_PICTURE"]:$ar["PREVIEW_PICTURE"];

            $ar["DETAIL_PICTURE_SRC"] = CFile::GetById($picID)->getNext()["SRC"];

            if(!$ar["DETAIL_PICTURE_SRC"]){
                $ar["DETAIL_PICTURE_SRC"] = $componentPath."/img/defaultPicture.png";
            }

            $canBuy = true;

            if (isset($ar['CATALOG_AVAILABLE']) && $ar['CATALOG_AVAILABLE'] === 'N')
                $canBuy = false;

            if (isset($ar['AVAILABLE']) && $ar['AVAILABLE'] === 'N')
                $canBuy = false;

            if (isset($ar['ACTIVE']) && $ar['ACTIVE'] === 'N')
                $canBuy = false;

            if (!isset($ar['PRICE']) || !is_array($ar['PRICE']) || !is_array($ar['PRICE']["RESULT_PRICE"])  )
                $canBuy = false;

            $ar["CAN_BUY"] = ($canBuy)?"Y":"N";

            if($canBuy)
                $resultProducts[$ar1["ID"]]["INFO"]["ITEMS_COUNT"]  += $ar["UF_QUANTITY"];



            if($canBuy){
                $total_price += intval($ar["PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"]) * intval($ar["UF_QUANTITY"]);
                $total_quantity  += intval($ar["UF_QUANTITY"]);
            }

            $resultProducts[$ar1["ID"]]["PRODUCTS"][] = $ar;
        }
    }
     
}
$arResult["TOTAL"] = array( "PRICE" => $total_price, "COUNT" => $total_quantity);


$arResult["PRODUCTS"] = $resultProducts;


//Если открывается окно оформления заказа то дополняем информацию о медальонах возможными вариантами
if(($arParams["AJAX"] == "Y") && ($_GET["TYPE"] == "BUY")){

    $offersArSelect = array("ID", "NAME", "DETAIL_PICTURE", "PREVIEW_PICTURE", "ACTIVE");

    $propertiesValues = array();
    $defFilter = [];

    foreach ($arParams["OFFERS_PROPS"] as $key => $value) {
        $offersArSelect[] = "PROPERTY_" . $value;

        $propertiesValues[$value] = array("INFO" => array(), "VALUES" => array());

        $propertyDbRes = CIBlockProperty::GetByID($value);

        if( $propertyArRes = $propertyDbRes->Fetch() ){
            $propertiesValues[$value]["INFO"] = $propertyArRes;

            $defFilter["PROPERTY_" . $propertyArRes["ID"]] = $propertyArRes["DEFAULT_VALUE"];

            $propertyHblDb = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('=TABLE_NAME' => $propertyArRes["USER_TYPE_SETTINGS"]["TABLE_NAME"])));
            if($propertyHblAr = $propertyHblDb->fetch())
            {
                $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($propertyHblAr["ID"])->fetch();
                $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
                $entityDataClass = $entity->getDataClass();

                $result = $entityDataClass::getList(array(
                    "select" => array("*"),
                ));
                
                while ($arRow = $result->Fetch())
                {
                    $propertiesValues[$value]["VALUES"][] = $arRow;
                }

                $files = array();
                foreach ($propertiesValues[$value]["VALUES"] as $key1 => $value1) {
                    if(isset($value1["UF_FILE"]) && ($value1["UF_FILE"] != 0) && ($value1["UF_FILE"] != "")){
                        $files[$value1["UF_FILE"]] = $key1;
                    }
                }

                $res = CFile::GetList(array("FILE_SIZE"=>"desc"), array("@ID"=>implode(",",array_keys($files))));
                while($res_arr = $res->GetNext()){
                    $propertiesValues[$value]["VALUES"][$files[$res_arr["ID"]]]["UF_FILE_PATH"] = "/" . COption::GetOptionString("main", "upload_dir", "upload") . "/" . $res_arr["SUBDIR"] . "/" . $res_arr["FILE_NAME"];
                }
            }            
        }
    }

    $arResult["PROPERTIES_INFO"] = $propertiesValues;


    $offers = CCatalogSKU::getOffersList(
        $ids, 
        0,
        array(),
        $offersArSelect
    );

    foreach ($arResult["PRODUCTS"] as $key => $value) {
        foreach ($value["PRODUCTS"] as $key1 => $value1) {
            $arResult["PRODUCTS"][$key]["PRODUCTS"][$key1]["OFFERS"] = $offers[$arResult["PRODUCTS"][$key]["PRODUCTS"][$key1]["ID"]];
        }
    }
}


$this->IncludeComponentTemplate();
?>