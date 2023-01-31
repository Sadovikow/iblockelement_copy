# iblockelement_copy
Генерация элементов определенного инфоблока на базе одного элемента в Битрикс.

Можно использовать для генерации контента во время разработки сайта или его тестирования, необходимо просто подставить ID элемента, на основе которого будет генерироваться остальные элементы. ID инфоблока и количество необходимых элементов для создания


```php
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
        $arFieldsCopy['NAME'] = 'Баннер 2' . $i;
        $el                   = new CIBlockElement();
        $NEW_ID               = $el->Add($arFieldsCopy);

        if (!$NEW_ID) {
            echo $el->LAST_ERROR;
        } else {
            echo 'Элемент скопирован. ID нового элемента: ' . $NEW_ID;
        }

    endwhile;

}

```
