<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

//Определяем цену выбранной конфигурации медальона(при оформлении заказа)
if($_GET["TYPE"] == "GETPRICE"){
    $scuIblockId = CCatalogSKU::GetInfoByIBlock($_GET["IBLOCK_ID"])["PRODUCT_IBLOCK_ID"];

    $filter = array();
    foreach ($_POST["selectedProperties"] as $key => $value) {
        if($value != -1){
            $filter["PROPERTY_".$key.""] = $value;
        }
    }
    $filter["IBLOCK_ID"] = $scuIblockId;

    $offersList = CCatalogSKU::getOffersList(
        $_GET["PRODUCT_ID"],
        $scuIblockId,
        $filter,
        array("ID", "NAME", "CATALOG_PRICE_1")
    );

    $minPrice = PHP_INT_MAX;
    $maxPrice = PHP_INT_MIN;
    $minPriceProductId = 0;
    $maxPriceProductId = 0;

    foreach ($offersList[$_GET["PRODUCT_ID"]] as $key => $value) {
        if($value["CATALOG_PRICE_1"] > $maxPrice){
            $maxPrice = $value["CATALOG_PRICE_1"];
            $maxPriceProductId = $value["ID"];
        }

        if($value["CATALOG_PRICE_1"] < $minPrice){
            $minPrice = $value["CATALOG_PRICE_1"];
            $minPriceProductId = $value["ID"];
        }
    }

    $resultArray = [];
    $resultArray["minPrice"] = [
        "PRODUCT_ID" => $minPriceProductId,
        "PRICE" => CCatalogProduct::GetOptimalPrice($minPriceProductId, 1, $USER->GetUserGroupArray()),
    ];
    $resultArray["maxPrice"] = [
        "PRODUCT_ID" => $maxPriceProductId,
        "PRICE" => CCatalogProduct::GetOptimalPrice($maxPriceProductId, 1, $USER->GetUserGroupArray()),
    ];


    $resultArray["minPrice"]["PRICE"]["RESULT_PRICE"]["PRINT_BASE_PRICE"] = CCurrencyLang::CurrencyFormat($resultArray["minPrice"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $resultArray["minPrice"]["PRICE"]["RESULT_PRICE"]["CURRENCY"]);
    $resultArray["minPrice"]["PRICE"]["RESULT_PRICE"]["PRINT_DISCOUNT_PRICE"] = CCurrencyLang::CurrencyFormat($resultArray["minPrice"]["PRICE"]["RESULT_PRICE"]["DISCOUNT_PRICE"], $resultArray["minPrice"]["PRICE"]["RESULT_PRICE"]["CURRENCY"]);


    echo json_encode($resultArray);
}
else{
    $signer = new \Bitrix\Main\Security\Sign\Signer;
    $params = unserialize(base64_decode($signer->unsign($_POST["params"], 'medalpetBasket')));
    $params["AJAX"] = "Y";

    $APPLICATION->IncludeComponent(
        "sigodinweb:medalpetBasket",
        "",
        $params
    );
}

?>