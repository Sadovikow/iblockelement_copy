<?

$idForCopy = 12345; // На основе этого элемента создадутся новые
$iblockId  = 85; // ID инфоблока, куда генерируем элементы
$copycount = 150; // Количество генерируемых элементов

$resource  = CIBlockElement::GetByID($idForCopy);
if ($ob = $resource->GetNextElement()) {
    $arFields                  = $ob->GetFields();
    $arFields['PROPERTIES']    = $ob->GetProperties();
    $arFieldsCopy['NAME']      = $arFields['NAME'];
    $arFieldsCopy['CODE']      = $arFields['CODE'];
    $arFieldsCopy['IBLOCK_ID'] = $iblockId;

    $arFieldsCopy['PROPERTY_VALUES'] = [];

    foreach ($arFields['PROPERTIES'] as $property) {
        $arFieldsCopy['PROPERTY_VALUES'][$property['CODE']] = $property['VALUE'];
        if ($arProp['PROPERTY_TYPE'] == 'L') {
            if ($arProp['MULTIPLE'] == 'Y') {
                $arFieldsCopy['PROPERTY_VALUES'][$arProp['CODE']] = [];
                foreach ($arProp['VALUE_ENUM_ID'] as $enumID) {
                    $arFieldsCopy['PROPERTY_VALUES'][$arProp['CODE']][] = [
                        'VALUE' => $enumID
                    ];
                }
            } else {
                $arFieldsCopy['PROPERTY_VALUES'][$arProp['CODE']] = [
                    'VALUE' => $arProp['VALUE_ENUM_ID']
                ];
            }
        }
        if ($property['PROPERTY_TYPE'] == 'F') {
            if ($property['MULTIPLE'] == 'Y') {
                if (is_array($property['VALUE'])) {
                    foreach ($property['VALUE'] as $key => $arElEnum) {
                        $arFieldsCopy['PROPERTY_VALUES'][$property['CODE']][$key] = CFile::CopyFile($arElEnum);
                    }
                }
            } else {
                $arFieldsCopy['PROPERTY_VALUES'][$property['CODE']] = CFile::CopyFile($property['VALUE']);
            }
        }
    }

    $i = 1;
    if(!$copycount) {
      $copycount = 10;
    }
    while ($i <= $copycount):
        echo $i;
        $i++;
        $arFieldsCopy['NAME'] = 'Wonderful item ' . $i;
        $el                   = new CIBlockElement();
        $NEW_ID               = $el->Add($arFieldsCopy);

        if (!$NEW_ID) {
            echo $el->LAST_ERROR;
        } else {
            echo 'Item copied. New item ID: ' . $NEW_ID;
        }

    endwhile;

}
