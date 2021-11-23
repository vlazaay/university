<?php

namespace App\Controller\Front;

//use App\Document\Documents;
//use App\Message\MyMessage;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use PhpOffice\PhpWord\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class ApiCurrencyController
 * @package  App\Controller\Front
 */
class WordController extends AbstractController
{
    /**
     * @param DocumentManager $dm
     * @param UserRepository $userRepository
     * @param MessageBusInterface $bus
     * @param UserInterface $user
     * @throws Exception
     * @Route("/word/report", name="word_deal_report")
     */
    public function getWordDeal(
        DocumentManager       $dm,
        UserRepository $userRepository,
        MessageBusInterface $bus,
        UserInterface $user
    )
    {
        //https://phpword.readthedocs.io/en/latest/styles.html
        $languageUA = new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::UK_UA);

        $number = $user->getId();
        $city = 'Вінниця';
        $buy = 'ФОП Сидорчук Василь Григорович';
        $seller = 'Бендери Петра Володимировича';
        $stockExchange = 'Жмерінське лісове господарство';
        $lorVendorCode = '3232';

        $tables = [
            1 => [
                'name' => 'необроб.деревин.',
                'type' => 'дуб',
                'class' => 'А',
                'D' => '30 -34',
                'L' => 'L2',
                'waterhouse' => 'пр',
                'coll' => '18',
                'price' => '8250'
            ],
            2 => [
                'name' => 'необроб.деревин.',
                'type' => 'береза',
                'class' => 'C',
                'D' => '25-29',
                'L' => 'L2',
                'waterhouse' => 'пр',
                'coll' => '15',
                'price' => '3284.8'
            ]
        ];

        $underLots = [];
        $priceNumber = null;
        foreach ($tables as $key => $table) {
            $underLots[$key] = "{$table['type']}, класс {$table['class']}, D {$table['D']} см, {$table['L']} - {$table['coll']}";
            $priceNumber += $table['coll'] * $table['price'];
        }

        $underLot = implode(",", array_keys($underLots));

        $certificate = '7';

        $sellerData = [
            'name' => 'Жмерінське ЛГ',
            'usreou' => '24456784',
            'yrladdress' => '__________',
            'fizaddress' => '__________',
            'p/p' => '__________',
            'in' => '__________',
            'mfis' => '__________',
            'phone' => '__________',
            'vat' => '__________',
            'ipn' => '__________'

        ];
        $buyData = [
            'name' => 'Жмерінське ЛГ',
            'usreou' => '24456784',
            'yrladdress' => '__________',
            'fizaddress' => '__________',
            'p/p' => '__________',
            'in' => '__________',
            'mfis' => '__________',
            'phone' => '__________'
        ];

        $resNumber = '____';
        $addition = '____';

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $phpWord->getSettings()->setThemeFontLang($languageUA);

        $section = $phpWord->addSection();

        $fontStyleHeader = 'fontStyleHeader';
        $phpWord->addFontStyle(
            $fontStyleHeader,
            array(
                //Курсив
                //'italic' => true,
                //Размер
                'size' => 11,
                //Жирность
                'bold' => true,
                //Заглавные буквы
                'allCaps' => true,
                //двойное перечеркивание
                //'doubleStrikethrough' => true,
                //отступы
                //'spaceAfter' => 100,
                //Межбуквенные отсутпы
                //'spacing' => 100,
            )
        );

        $paragraphStyleCenter = 'paragraphStyleCenter';
        $phpWord->addParagraphStyle(
            $paragraphStyleCenter,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            )
        );

        $paragraphStyleLeft = 'paragraphStyleLeft';
        $phpWord->addParagraphStyle(
            $paragraphStyleLeft,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
            )
        );
        $paragraphStyleRight = 'paragraphStyleRight';
        $phpWord->addParagraphStyle(
            $paragraphStyleRight,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT,
            )
        );

        $paragraphStyleBoth = 'paragraphStyleBoth';
        $phpWord->addParagraphStyle(
            $paragraphStyleBoth,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
                //Оступ сверху
                //'spaceBefore' => 1000,
                //Оступ снизу
                //'spaceAfter' => 1000
                //Отсып верх и низ
                // 'spacing'=>100
                //Правило межстрочного интервала. авто, точное, по крайней мере
                //'spacingLineRule'=>\PhpOffice\PhpWord\SimpleType\LineSpacingRule::EXACT
                //отсуп в дюймах
                //'indent'=>1
                'keepNext' => true,
                //Отстып первого абзаца
                'indentation' => array('firstLine' => 240)
            )
        );

        $paragraphStyleBoth2 = 'paragraphStyleBoth2';
        $phpWord->addParagraphStyle(
            $paragraphStyleBoth2,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
                //Оступ сверху
                //'spaceBefore' => 1000,
                //Оступ снизу
                //'spaceAfter' => 1000
                //Отсып верх и низ
                // 'spacing'=>100
                //Правило межстрочного интервала. авто, точное, по крайней мере
                //'spacingLineRule'=>\PhpOffice\PhpWord\SimpleType\LineSpacingRule::EXACT
                //отсуп в дюймах
                //'indent'=>1
                'keepNext' => true,
                //Отстып первого абзаца
                'indentation' => array('firstLine' => 1000)
            )
        );

        $section->addText(
            'ДОГОВІР',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        if (!isset($number) && empty($number)) {
            $number = '___/_____';
        }

        $section->addText(
            "КУПІВЛІ-ПРОДАЖУ НЕОБРОБЛЕНОЇ ДЕРЕВИНИ  № {$number} ",
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        if (!isset($city) && empty($city)) {
            $city = '_____________';
        }

       // $date = $this->transformDateUA();
        if (!isset($date) && empty($date)) {
            $date = '“__” ___________ 202_ р.';
        }

        $section->addText(
            "м.{$city}&#9;&#9;&#9;&#9;&#9;&#9;&#9;&#9;{$date} ",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleCenter
        );

        if (!isset($buy) && empty($buy)) {
            $buy = '___________________';
        }

        if (!isset($seller) && empty($seller)) {
            $seller = '___________________';
        }

        if (!isset($stockExchange) && empty($stockExchange)) {
            $stockExchange = '____________________';
        }
        $section->addText(
            'Учасник біржових торгів  '
            . "{$stockExchange}"
            . ', в особi  директора '
            . "{$seller}"
            . ', що діє на підставі Статуту, надалі по тексту "Продавець", з одного боку, та учасник біржових торгів'
            . "{$buy}"
            . ', що діє на підставі Статуту, в особi директора '
            . "{$seller}"
            . ' , надалі по тексту "Покупець", з іншого боку, разом надалі іменовані "Сторони", за результатами проведення біржових торгів, які відбулись '
            . "{$date}"
            . ' на товарній біржі '
            . "Всі Трейд "
            . '(надалі Біржа) уклали даний договір про наступне:',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '1. ПРЕДМЕТ ДОГОВОРУ.',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        //$quarter = $this->getCurrentQuartalNumber();
        if (!isset($quarter) && empty($quarter)) {
            $quarter = '__';
        }

        $year = date('Y');
        if (!isset($year) && empty($year)) {
            $year = '____';
        }

        if (!isset($lorVendorCode) && empty($lorVendorCode)) {
            $lorVendorCode = '____';
        }

        $section->addText(
            '1.1. За результатами проведення біржових торгів із продажу ресурсів необробленої деревини заготівлі '
            . "{$quarter}"
            . ' кварталу '
            . "{$year}"
            . 'року, які відбулись '
            . "{$date}"
            . ', Продавець передає у власність на умовах франко-склад Продавця (франко - нижній, франко - верхній, франко - проміжний) необроблену деревину, (надалі - Товар), /лоти №№ '
            . "{$lorVendorCode}"
            . ',підлоти №№ '
            . "{$underLot}"
            . '/, а Покупець зобов\'язується прийняти Товар і сплатити за нього ціну відповідно до умов, що визначені в цьому Договорі.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '1.2. Згідно чинного законодавства України та Регламенту Біржі Продавець продає, а Покупець купує Товар для власної переробки.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,

            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '1.3. Право власності на майно переходить до Покупця з моменту повної оплати Продавцю вартості купленого на біржових торгах Товару та підписання товарно-транспортної накладної на необроблену деревину.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '2. ЯКІСТЬ ТА ОБМІР ТОВАРУ.',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '2.1. По якості необроблена деревина відповідає вимогам чинних стандартів, а саме: ДСТУ EN 1315-1-2001, ДСТУ EN 1315-2-2001, ДСТУ EN 1316-1-2005, ДСТУ EN 1316-2:2005, ДСТУ EN 1316-3:2005, ДСТУ ENV 1927-1:2005, ДСТУ ENV 1927-2:2005, ДСТУ ENV 1927-3:2005.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '2.2. Об\'єм Товару   визначається  згідно ДСТУ  4020-2-2001 "Методи обмірювання та визначення об\'ємів". Обмір по верхньому діаметру.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '3. КІЛЬКІСТЬ ТОВАРУ.',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );


        $str = null;
        foreach ($underLots as $underLotsItem) {
            $str .= "{$underLotsItem} /";
        }

        $section->addText(
            "3.1. Асортимент: "
            . " {$str}",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'При взаємній згоді Сторін можливе збільшення або зменшення кількості кожного сортименту в межах загального об\'єму лоту. Ціна за кубометр кожного докупленого сортименту повинна відповідати ціні, вказаної в п.4.1. цього Договору.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'Взаємною згодою по ціні та об\'єму є підписана Продавцем та Покупцем товарно-транспортна накладна.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '4. ЦІНА ТА ЗАГАЛЬНА СУМА ДОГОВОРУ.',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );


        $section->addText(
            '4.1. Ціна на Товар встановлена в гривнах за 1 куб.м на умовах франко-склад (франко-нижній, франко - верхній, франко - проміжний) Продавця згідно Біржового(аукціонного) свідоцтва '
            . '№'
            . "{$certificate}"
            . ' від '
            . "{$date}"
            . ' (Протоколу) про результати проведення біржових торгів із продажу ресурсів '
            . "{$quarter}"
            . " кварталу {$year}року необробленої деревини складає:",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $rows = count($tables) + 1;
        $cols = 11;

        $fancyTableStyleName = 'Fancy Table';
        $fancyTableStyle = array(
            'borderSize' => 6,
            'borderColor' => '999999',
            'valign' => 'center',
            //'cellMargin' => 1,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            //'cellSpacing' => 1
        );
        $fancyTableFirstRowStyle = array(
            //'borderBottomSize' => 1,
            //'borderBottomColor' => '0000FF',
            //'bgColor' => '66BBFF'
        );
        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
        $table = $section->addTable($fancyTableStyleName);
        $table->addRow();
        for ($c = 1; $c <= $cols; $c++) {
            if ($c == 1) {
                $table->addCell(1750)->addText(
                    "№ лоту",
                    $fancyTableStyle
                );
            } elseif ($c == 2) {
                $table->addCell(1750)->addText(
                    "№ п лоту",
                    $fancyTableStyle
                );
            } elseif ($c == 3) {
                $table->addCell(1750)->addText(
                    "Продукція",
                    $fancyTableStyle
                );
            } elseif ($c == 4) {
                $table->addCell(1750)->addText(
                    "Порода",
                    $fancyTableStyle
                );
            } elseif ($c == 5) {
                $table->addCell(1750)->addText(
                    "Клас якості",
                    $fancyTableStyle
                );
            } elseif ($c == 6) {
                $table->addCell(1750)->addText(
                    "Клас діаметру, см",
                    $fancyTableStyle
                );
            } elseif ($c == 7) {
                $table->addCell(1750)->addText(
                    "Клас довжини, м",
                    $fancyTableStyle
                );
            } elseif ($c == 8) {
                $table->addCell(1750)->addText(
                    "Склад",
                    $fancyTableStyle
                );
            } elseif ($c == 9) {
                $table->addCell(1750)->addText(
                    "Обсяг, куб.м",
                    $fancyTableStyle
                );
            } elseif ($c == 10) {
                $table->addCell(1750)->addText(
                    "Ціна продажу за куб.м, грн з ПДВ",
                    $fancyTableStyle
                );
            } elseif ($c == 11) {
                $table->addCell(1750)->addText(
                    "Вартість продажу за лот, грн з ПДВ",
                    $fancyTableStyle
                );
            }
        }


        foreach ($tables as $keys => $item) {
            $table->addRow();
            $table->addCell(1750)->addText(
                "{$lorVendorCode}",
                $fancyTableStyle
            );

            $table->addCell(1750)->addText(
                "{$keys}",
                $fancyTableStyle
            );

            $table->addCell(1750)->addText(
                "{$item['name']}",
                $fancyTableStyle
            );

            $table->addCell(1750)->addText(
                "{$item['type']}",
                $fancyTableStyle
            );

            $table->addCell(1750)->addText(
                "{$item['class']}",
                $fancyTableStyle
            );

            $table->addCell(1750)->addText(
                "{$item['D']}",
                $fancyTableStyle
            );
            $table->addCell(1750)->addText(
                "{$item['L']}",
                $fancyTableStyle
            );
            $table->addCell(1750)->addText(
                "{$item['waterhouse']}",
                $fancyTableStyle
            );

            $table->addCell(1750)->addText(
                "{$item['coll']}",
                $fancyTableStyle
            );

            $table->addCell(1750)->addText(
                "{$item['price']}",
                $fancyTableStyle
            );

            $calc = $item['price'] * $item['coll'];
            $table->addCell(1750)->addText(
                "{$calc}",
                $fancyTableStyle
            );
        }


        if (!isset($priceNumber) && empty($priceNumber)) {
            $priceNumber = '_________';
        }

        //$priceString = $this->num2text_ua($priceNumber);
        if (!isset($priceString) && empty($priceString)) {
            $priceString = '_________';
        }

        $section->addText(
            '4.2. Загальна сума договору складає '
            . "{$priceNumber}"
            . ' грн. ('
            . "{$priceString}"
            . ') в т.ч. ПДВ ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '4.3. За взаємною згодою сторін ціна продажу, що зафіксована на біржових торгах Біржовим(аукціонним) свідоцтвом №'
            . "{$certificate}"
            . ' від '
            . "{$date}"
            . ' (Протоколом), може бути змінена на рівень інфляції, який склався на момент оплати коштів за придбану деревину або при значній зміні вартості складових лісозаготівельного виробництва.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'Дані зміни оформлюються як Додаткова угода та є невід\'ємною частиною цього Договору. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '5. УМОВИ  ПОСТАВКИ.',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '5.1. Поставка Товару по даному договору здійснюється згідно щомісячного графіку поставок, який є невід\'ємною частиною даного договору, на умовах франко-склад Продавця. (Додаток №1)',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '5.2. Місячна партія становить пропорційну частину загальної кількості Товару, що забезпечує рівномірну поставку та погоджується Сторонами графіком поставки. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '5.3. Покупець оплачує вартість витрат на реквізит ( вагонні стійки, дошки для оббивки, цвяхи, дріт та інші).',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '5.4. Кожна поставка має бути забезпечена таким комплектом документів:',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '- товарно-транспортна (залізнична) накладна; ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth2
        );

        $section->addText(
            '- рахунок-фактура;',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth2
        );

        $section->addText(
            '- податкова накладна; ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth2
        );

        $section->addText(
            '- специфікація.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth2
        );

        $section->addText(
            '5.5. В кінці кожного місяця Сторони підписують акти взаєморозрахунків.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '6. ПРИЙОМ - ПЕРЕДАЧА ТОВАРУ.',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '6.1. Прийом-передача Товару здійснюється на складі Продавця за умовами франко-склад Продавця: ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '- за якістю - у відповідності з нормами відповідних ГОСТ, ДСТУ, інших умов згідно законодавства України;  ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '- за   кількістю  -  у  відповідності  з  товарно  -  транспортними   чи   залізничними накладними  та специфікаціями до них з підписом уповноваженої особи.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '6.2. Не пізніше ніж за одну добу до передачі Товару Продавець повідомляє Покупця про дату та місце поставки. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '6.3. Датою передачі Товару Продавцем та прийому його Покупцем, тобто датою поставки, вважається дата товарно - транспортної накладної.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '6.4. Приймання Товару по кількості і якості здійснюється у відповідності з вимогами інструкцій про порядок приймання лісопродукції по кількості і якості - П-6, П-7; ДСТУ 4020-2-2001,  2034-92',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '6.5. В разі, якщо при прийманні-передачі Товару Покупець виявить неякісний Товар розходження по кількості Товару між фактично наявним і зазначеним в товаросупровідних документах обсягом, складається  Акт за підписами представників обох Сторін в двох екземплярах по одному для кожної зі Сторін. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '7. ПОРЯДОК РОЗРАХУНКІВ',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '7.1. Платіж (у розмірі 100%) здійснюється шляхом банківського переказу грошових коштів на розрахунковий рахунок Продавця за кожну партію Товару, згідно виставленого рахунку-фактури протягом 5-ти календарних днів з дати пред\'явлення рахунку до оплати.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '7.2. В разі придбання Покупцем заявлених лотів(підлотів), гарантійний внесок перерахоувується Біржею Продавцю, як попередня оплата за товар, згідно чинного законодавства України.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'Гарантійний внесок зараховується в попередню оплату, пропорційно обсягам вибраної лісопродукції, згідно графіку поставки . ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'Податкову накладну Покупцю видає Продавець. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '7.3. Банківські витрати, пов\'язані із перерахуванням коштів, оплачуються Покупцем. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '8. ОБОВ\'ЯЗКИ СТОРІН',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '8.1. Покупець зобов\'язаний приймати кожну партію Товару та оплачувати її за ціною та у строки, визначені даними Договором. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '8.2. Покупець  купує Товар  для   власної  переробки  без  права  подальшої  реалізації в необробленому вигляді.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '8.3. Продавець зобов\'язаний здійснювати поставку Товару на умовах даного Договору відповідно до погодженого Сторонами графіку поставки, який є невід\'ємною частиною даного Договору.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '8.4. Продавець зобов\'язаний видати Покупцю за першою подією (отримання грошей чи відвантаження товарів) податкову накладну, оформлену відповідно до правил, установлених пунктом 201.1 Податкового кодексу України. Оформлена Продавцем податкова накладна має бути зареєстрована Продавцем у Єдиному реєстрі податкових накладних протягом 15 днів від дня виникнення податкових зобов\'язань. У разі порушення Продавцем даного пункту Договору Продавець сплачує Покупцю штраф за втрату податкового кредиту у розмірі суми податку на додану вартість.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9. ВІДПОВІДАЛЬНІСТЬ СТОРІН ЗА ПОРУШЕННЯ ДОГОВОРУ',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '9.1. Порушенням Договору є його невиконання або неналежне виконання, тобто виконання з порушенням умов, визначених змістом цього Договору. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9.2. У разі порушення Покупцем пункту 1.2. цього Договору Продавець, керуючись встановленим Регламентом, має право відмовитися від подальшого постачання Товару на адресу Покупця. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9.3. У разі відмови Покупця від отримання Товару, згідно даного договору, сума в розмірі 5% від сплаченої вартості згідно п.7.1 цього договору, залишається Продавцю в якості компенсації для перекриття витрат на заготівлю деревини. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9.4. У разі несвоєчасної оплати Покупцем партії Товару згідно умов, визначених цим Договором, Покупець сплачує Продавцеві пеню у розмірі подвійної облікової ставки НБУ за кожен день прострочення.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9.5. У випадку ненадходження коштів на рахунок Продавця протягом 5 банківських днів з моменту виставлення рахунку-фактури для оплати, Покупець втрачає право на придбання неоплаченої партії Товару. Неоплачена партія Товару не буде поставлена в наступних місяцях протягом яких діє даний Договір і реалізовується Продавцем на свій розсуд. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9.6. У разі несвоєчасної виборки Товару, згідно графіку поставки (Додаток № 1), Покупець сплачує Продавцеві пеню в розмірі подвійної облікової ставки НБУ.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9.7. У разі несвоєчасного надання (відвантаження) Товару, згідно графіку поставки (Додаток № 1),  Продавець сплачує Покупцю пеню в розмірі подвійної облікової ставки НБУ. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '9.7. У разі несвоєчасного надання (відвантаження) Товару, згідно графіку поставки (Додаток № 1),  Продавець сплачує Покупцю пеню в розмірі подвійної облікової ставки НБУ. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '10. ФОРС-МАЖОРНІ ОБСТАВИНИ',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '10.1. Сторона звільняється від визначеної цим Договором та (або) чинним законодавством України відповідальності за повне чи часткове порушення Договору, якщо вона доведе, що таке порушення сталося внаслідок дії форс-мажорних обставин, визначених у цьому Договорі, за умови, що їх настання було засвідчено у визначеному цим Договором порядку.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '10.2. Під непереборною силою у цьому Договорі розуміються будь-які надзвичайні події зовнішнього щодо Сторін характеру, які виникають без вини Сторін, поза їх волею або всупереч волі чи бажанню Сторін, і які не можна за умови вжиття звичайних для цього заходів передбачити та не можна при всій турботливості та обачності відвернути (уникнути),включаючи (але не обмежуючись) стихійні явища природного характеру (землетруси, повені, урагани, руйнування в результаті блискавки тощо), лиха біологічного, техногенного та антропогенного походження (вибухи, пожежі, вихід з ладу машин й обладнання, масові епідемії, епізоотії, епіфітотії тощо), обставини суспільного життя (війна, воєнні дії, блокади,громадські заворушення, прояви тероризму, масові страйки та локаути, бойкоти тощо), а також видання заборонних або обмежуючих нормативних актів органів державної влади чи місцевого самоврядування, інші законні або незаконні заборонні чи обмежуючі заходи названих органів, які унеможливлюють виконання Сторонами цього Договору або тимчасово перешкоджають його виконанню, в т.ч. заборона державними органами санітарних рубок, а також дії, що виникають при виконанні ЗУ "Про оцінку впливу на довкілля". ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '10.3. Факт настання та існування непереборної сили має бути засвідчений компетентним органом, що визначений чинним законодавством України.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '10.4. Сторона, що має намір послатися на форс-мажорні обставини, зобов\'язана невідкладно із врахуванням можливостей технічних засобів зв\'язку та характеру існуючих перешкод повідомити іншу Сторону про наявність форс-мажорних обставин та їх вплив на виконання цього Договору.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '11. ВИРІШЕННЯ СПОРІВ',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '11.1. Усі спори, що виникають з цього Договору або пов\'язані із ним, вирішуються шляхом переговорів між Сторонами.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '11.2. Якщо дійти згоди шляхом переговорів та взаємних домовленостей неможливо, спір вирішується  в судовому порядку за встановленою  підвідомчістю та підсудністю такого спору відповідно до чинного законодавства України.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '12. СТРОК ДІЇ, ПІДСТАВИ ДЛЯ ЗМІНИ, РОЗІРВАННЯ  ДОГОВОРУ',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '12.1. Цей Договір вважається укладеним з моменту його підписання та набирає чинності з першого числа кварталу і діє до кінця кварталу на який він укладений.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '12.2. Термін дії договору може бути продовжений за взаємною згодою сторін.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '12.3. Закінчення строку дії (або дострокове розірвання) цього Договору не звільняє Сторони  від відповідальності   за виконання в повному обсязі всіх зобов\'язань, взятих на себе під час дії Договору, та від його порушення, яке мало місце під час дії цього Договору. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '12.4. Будь-які зміни до цього Договору приймаються Сторонами за взаємною згодою та оформляються у письмовій формі, що є невід\'ємною частиною цього Договору.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '12.5. Дострокове припинення Договору допускається лише при взаємній згоді та домовленості Сторін з урахуванням виконання на момент такого припинення всіх існуючих зобов\'язань. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '12.6. У разі односторонньої відмови від Договору, Сторона, яка прийняла таке рішення, повинна повідомити про це іншу Сторону за один календарний місяць у письмовому вигляді.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '12.7. Даний Договір складений у двох примірниках, що мають однакову юридичну силу по одному для кожної зі Сторін.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '13. ІНШІ УМОВИ',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $section->addText(
            '13.1. Відповідно  до  Закону  України  "Про захист персональних даних" від 01.06.2010 року № 2297-VI Сторони надають згоду на збір та обробку персональних даних. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            '14. РЕКВІЗИТИ ТА ПІДПИСИ СТОРІН',
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $rows = 2;
        $cols = 2;

        $fancyTableStyleName = 'Fancy Table';
        $fancyTableStyle = array(
            //'borderSize' => 6,
            //'borderColor' => '999999',
            //'valign' => 'center',
            //'cellMargin' => 1,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            //'cellSpacing' => 1
            //'cellMargin'  => 500
            'spaceAfter' => 300,
        );
        $fancyTableFirstRowStyle = array(
            //'borderBottomSize' => 1,
            //'borderBottomColor' => '0000FF',
            //'bgColor' => '66BBFF'
        );
        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
        $table = $section->addTable($fancyTableStyleName);

        //вопрос тут
        for ($r = 1; $r <= $rows; $r++) {
            $table->addRow(100);
            for ($c = 1; $c <= $cols; $c++) {
                if ($r == 1) {
                    if ($c == 1) {
                        $table->addCell(7500)->addText(
                            "ПРОДАВЕЦЬ ",
                            $fancyTableStyle
                        );
                    } elseif ($c == 2) {
                        $table->addCell(7500)->addText(
                            "ПОКУПЕЦЬ",
                            $fancyTableStyle
                        );
                    }
                } else {

                    if ($c == 1) {
                        $cell = $table->addCell(7500);
                        $cell->addText(
                            "{$sellerData['name']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "Код за ЄДРПОУ {$sellerData['usreou']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "Юридична адреса: {$sellerData['yrladdress']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "Фактична адреса: {$sellerData['fizaddress']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );

                        $cell->addText(
                            "р/р {$sellerData['p/p']}   в {$sellerData['in']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "МФО {$sellerData['mfis']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            'Статус платника податку на прибуток:',
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            'є платником податку на прибуток на загальних підставах',
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "тел. {$sellerData['phone']}",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "Свідоцтво ПДВ № {$sellerData['vat']}",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "ІПН {$sellerData['ipn']}",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            'Директор _____________________ ',
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            '______________________',
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            '       (підпис)         (ПІБ)',
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            'М.П.',
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "{$date}",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                    } elseif ($c == 2) {
                        $cell = $table->addCell(7500);
                        $cell->addText(
                            "{$buyData['name']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "Код за ЄДРПОУ {$buyData['usreou']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "Юридична адреса: {$buyData['yrladdress']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "Фактична адреса: {$buyData['fizaddress']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "р/р {$buyData['p/p']}   в {$buyData['in']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "МФО {$buyData['mfis']}&#10;",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                        $cell->addText(
                            "тел. {$buyData['phone']}",
                            array(
                                'size' => 11,
                                'bold' => false,
                                'allCaps' => false,
                            )
                        );
                    }
                }
            }
        }

        $section->addText(
            " ",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
                'spaceBefore' => 300,
                'spaceAfter' => 300,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            "Реєстрація договору Біржєю №{$resNumber}від {$date}",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
                'spaceBefore' => 300,
                'spaceAfter' => 300,
            ),
            $paragraphStyleBoth
        );
        $section->addText(
            " ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );

        $section->addText(
            "Додаток № {$addition}",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,

            ),
            $paragraphStyleRight
        );

        $section->addText(
            "До договору № {$number} від {$date}",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
                'spaceAfter' => 300,

            ),
            $paragraphStyleRight
        );
        $section->addText(
            " ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            "Подекадний графік поставки",
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => false,
                'spaceAfter' => 10,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            "необробленої деревини",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );


        $rows = 15;
        $cols = 4;

        $fancyTableStyleName = 'Fancy Table';
        $fancyTableStyle = array(
            'borderSize' => 6,
            'borderColor' => '999999',
            'valign' => 'center',
            //'cellMargin' => 1,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            //'cellSpacing' => 1
            //'cellMargin'  => 500
        );
        $fancyTableFirstRowStyle = array(
            //'borderBottomSize' => 1,
            //'borderBottomColor' => '0000FF',
            //'bgColor' => '66BBFF'
        );
        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
        $table = $section->addTable($fancyTableStyleName);

        //вопрос тут
        for ($r = 1; $r <= $rows; $r++) {
            $table->addRow(100);
            for ($c = 1; $c <= $cols; $c++) {
                if ($r == 1) {
                    if ($c == 1) {
                        $table->addCell(7500)->addText(
                            "№ з/п ",
                            $fancyTableStyle,
                            $paragraphStyleCenter
                        );
                    } elseif ($c == 2) {
                        $table->addCell(7500)->addText(
                            "Квартал, місяць, декада",
                            $fancyTableStyle,
                            $paragraphStyleCenter
                        );
                    } elseif ($c == 3) {
                        $table->addCell(7500)->addText(
                            "Обсяг, куб.м.",
                            $fancyTableStyle,
                            $paragraphStyleCenter
                        );
                    } elseif ($c == 4) {
                        $table->addCell(7500)->addText(
                            "Вартість, грн., в т.ч. ПДВ",
                            $fancyTableStyle,
                            $paragraphStyleCenter
                        );
                    }
                } else {
                    $cell = $table->addCell(7500);
                    $cell->addText(
                        '',
                        array(
                            'size' => 11,
                            'bold' => false,
                            'allCaps' => false,
                        ),
                        $paragraphStyleCenter
                    );
                }
            }
        }
        $section->addText(
            " ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            " ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            "Додаток є невід'ємною частиною договору.",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            " ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            " ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            " ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => true,
                'allCaps' => false,

            ),
            $paragraphStyleCenter
        );
        $section->addText(
            "Продавець __________________                                Покупець __________________ ",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => false,
                'allCaps' => false,

            ),
            $paragraphStyleLeft
        );
        $section->addText(
            " {$date}                                                     {$date}",
            array(
                'spaceBefore' => 10,
                'size' => 11,
                'bold' => false,
                'allCaps' => false,

            ),
            $paragraphStyleLeft
        );
        $footer = $section->addFooter();
        $footer->addText(
            'Продавець __________________                                Покупець __________________ ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addTextBreak();

        $folder = date('d_m_Y');
        if (!is_dir("docs")) {
            mkdir("docs", 0777);
        }
        if (!is_dir("docs/deal")) {
            mkdir("docs/deal", 0777);
        }
        if (!is_dir("docs/deal/$folder")) {
            mkdir("docs/deal/$folder", 0777);
        }
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        if (isset($number) && !empty($number)) {
            $item = str_replace('/', '_', $number);
            $link = md5("ДОГОВІР КУПІВЛІ-ПРОДАЖУ НЕОБРОБЛЕНОЇ ДЕРЕВИНИ  № {$item}");
        } else {
            $link = 'example';
        }
        $fileName = 'hello_world_download_file.docx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Write in the temporal filepath
        $objWriter->save($temp_file);
        //$this->dispatchMessage(new MyMessage('Doc Saved ' . $slug . ' - at ' . $time));

//        $message = new MyMessage(1, $path);
//        $bus->dispatch($message);

//        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, "HTML");
//        $objWriter->save('helloWorld.html');
//
//        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
//        $objWriter->save('helloWorld.odt');
        // Send the temporal file as response (as an attachment)
        $response = new BinaryFileResponse($temp_file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        return $response;

    }

    /**
     * @param DocumentManager $dm
     * @param MessageBusInterface $bus
     * @Route("/word/certificate", name="word_certificate_api", methods={"GET"})
     */

    public function getWordCertificate(DocumentManager $dm, MessageBusInterface $bus)
    {
        //https://phpword.readthedocs.io/en/latest/styles.html?highlight=spaceBefore#paragraph
        $languageUA = new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::UK_UA);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $phpWord->getSettings()->setThemeFontLang($languageUA);

        $section = $phpWord->addSection();

        $fontStyleHeader = 'fontStyleHeader';
        $phpWord->addFontStyle(
            $fontStyleHeader,
            array(
                //Курсив
                //'italic' => true,
                //Размер
                'size' => 11,
                //Жирность
                'bold' => true,
                //Заглавные буквы
                'allCaps' => true,
                //двойное перечеркивание
                //'doubleStrikethrough' => true,
                //отступы
                //'spaceAfter' => 100,
                //Межбуквенные отсутпы
                //'spacing' => 100,
            )
        );

        $paragraphStyleCenter = 'paragraphStyleCenter';
        $phpWord->addParagraphStyle(
            $paragraphStyleCenter,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            )
        );

        $paragraphStyleLeft = 'paragraphStyleLeft';
        $phpWord->addParagraphStyle(
            $paragraphStyleLeft,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
            )
        );
        $paragraphStyleRight = 'paragraphStyleRight';
        $phpWord->addParagraphStyle(
            $paragraphStyleRight,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT,
            )
        );

        $paragraphStyleBoth = 'paragraphStyleBoth';
        $phpWord->addParagraphStyle(
            $paragraphStyleBoth,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
                //Оступ сверху
                //'spaceBefore' => 1000,
                //Оступ снизу
                //'spaceAfter' => 1000
                //Отсып верх и низ
                // 'spacing'=>100
                //Правило межстрочного интервала. авто, точное, по крайней мере
                //'spacingLineRule'=>\PhpOffice\PhpWord\SimpleType\LineSpacingRule::EXACT
                //отсуп в дюймах
                //'indent'=>1
                'keepNext' => true,
                //Отстып первого абзаца
                'indentation' => array('firstLine' => 240)
            )
        );

        $paragraphStyleBoth2 = 'paragraphStyleBoth2';
        $phpWord->addParagraphStyle(
            $paragraphStyleBoth2,
            array(
                //Выравнивание по центру
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
                //Оступ сверху
                //'spaceBefore' => 1000,
                //Оступ снизу
                //'spaceAfter' => 1000
                //Отсып верх и низ
                // 'spacing'=>100
                //Правило межстрочного интервала. авто, точное, по крайней мере
                //'spacingLineRule'=>\PhpOffice\PhpWord\SimpleType\LineSpacingRule::EXACT
                //отсуп в дюймах
                //'indent'=>1
                'keepNext' => true,
                //Отстып первого абзаца
                'indentation' => array('firstLine' => 1000)
            )
        );

        $number = "111";
        if (!isset($number) && empty($number)) {
            $number = '___';
        }

        $section->addText(
            "БІРЖОВЕ (АУКЦІОННЕ) СВІДОЦТВО № {$number} ",
            array(
                'size' => 11,
                'bold' => true,
                'allCaps' => true,
            ),
            $paragraphStyleCenter
        );

        $city = 'Киев';
        if (!isset($city) && empty($city)) {
            $city = '_____________';
        }

        $date = $this->transformDateUA();
        if (!isset($date) && empty($date)) {
            $date = '“__” ___________ 202_ р.';
        }
        $section->addText(
            "м.{$city}&#9;&#9;&#9;&#9;&#9;&#9;&#9;&#9;&#9;{$date} ",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleCenter
        );

        $stockExchange = $this->getParameter('stockExchange');
        $buyerIden = '123 : 123456789';
        if (!isset($buyerIden) && empty($buyerIden)) {
            $buyerIden = '___ : ______________________';
        }
        $salesman = $this->getParameter('salesman');
        if (!isset($salesman) && empty($salesman)) {
            $salesman = '___ : ______________________';
        }

        $soldName = 'Дрова';
        if (!isset($soldName) && empty($soldName)) {
            $soldName = '________________________';
        }

        //обсягом тонн, м3
        $amount = 10;
        if (!isset($amount) && empty($amount)) {
            $amount = '__________';
        }

        //Не ясно что
        $unknown = 'unknown';
        if (!isset($unknown) && empty($unknown)) {
            $unknown = '________________________';
        }
        $lotNumber = '99';
        if (!isset($lotNumber) && empty($lotNumber)) {
            $lotNumber = '__';
        }
        $underLotNumber = '99';
        if (!isset($underLotNumber) && empty($underLotNumber)) {
            $underLotNumber = '__';
        }
        $price = '9999999';
        $priceUa = $this->num2text_ua($price);
        if (!isset($price) && empty($price)) {
            $price = '__';
        }
        $section->addText(
            'На аукціоні з продажу (купівлі, зустрічному) №'
            . "{$number}"//???
            . ' , що відбувся '
            . "{$date}"
            . ' на товарній біржі'
            . "{$stockExchange}"
            . ' покупець іден.№'
            . "{$buyerIden}"
            . ' купив, а продавець іден.№'
            . "{$salesman}"
            . ' продав '
            . "{$soldName}"
            . ' обсягом '
            . "{$amount}"
            . ' (тонн, м3) ('
            . "{$unknown}"
            . ') (лот(и) №'
            . "{$lotNumber}"
            . ', підлот(и)№'
            . "{$underLotNumber}"
            . ') на загальну суму '
            . "{$price}грн."
            . '00коп.('
            . "{$priceUa}"
            . ') (з урахуванням ПДВ) на умовах транспортування(зберігання, передачі) '
            . "{$unknown}"
            . '.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );


        $section->addText(
            'Біржове (аукціонне) свідоцтво є підставою для укладання договору купівлі-продажу між покупцем :'
            . "{$buyerIden}"
            . ' та продавцем :'
            . "{$salesman}",
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'Відповідно до п.'
            . '__'
            . ' Регламенту, покупець зобов’язаний протягом '
            . '__'
            . ' днів з дати проведення аукціону укласти с продавцем договір купівлі-продажу згідно з цим біржовим (аукціонним) свідоцтвом.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );
        $section->addText(
            'Покупець/Продавець зобов’язаний протягом '
            . '__'
            . ' днів з дати проведення аукціону перерахувати комісійний збір товарній біржі '
            . '__________________________'
            . ' за організацію та проведення аукціону у розмірі '
            . '__'
            . '% від загальної суми, зазначеної в цьому біржовому (аукціонному) свідоцтві, що становить '
            . '_____________________________'
            . ' грн., в т.ч. ПДВ '
            . '______________________'
            . ' на рахунок '
            . '_______________________.',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'У випадку не підписання договору купівлі-продажу з вини Покупця/Продавця у термін, зазначений у п.'
            . '__'
            . ' Регламенту, біржова угода розривається (припиняється), та сплачений Покупцем/Продавцем гарантійний внесок йому не повертається. Після підписання договору купівлі-продажу (гарантийній внесок Продавця повертається йому у термін, визначений у п.'
            . '__'
            . ' Регламенту)  гарантийний внесок Покупця перераховується товарною біржою Продавцю за вирахуванням комісійного збору, у розмірі визначеному тарифами товарної біржі. ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );
        $section->addText(
            ' ',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );
        $section->addText(
            'Переможець аукціону (Продавець/Покупець)',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );
        $section->addText(
            '______________________________ /______________________/',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addText(
            'Товарна біржа',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );
        $section->addText(
            '______________________________ /______________________/',
            array(
                'size' => 11,
                'bold' => false,
                'allCaps' => false,
            ),
            $paragraphStyleBoth
        );

        $section->addTextBreak();

        $folder = date('d_m_Y');
        if (!is_dir("docs")) {
            mkdir("docs", 0777);
        }
        if (!is_dir("docs/certificate")) {
            mkdir("docs/certificate", 0777);
        }
        if (!is_dir("docs/certificate/$folder")) {
            mkdir("docs/certificate/$folder", 0777);
        }
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        if (isset($number) && !empty($number)) {
            $item = str_replace('/', '_', $number);
            $link = md5("БІРЖОВЕ (АУКЦІОННЕ) СВІДОЦТВО № {$item}");
        } else {
            $link = 'example';
        }
        $objWriter->save("docs/certificate/" . $folder . "/" . $link . '.docx');
        $path = "docs/certificate/" . $folder . "/" . $link . ".docx";

        //$this->dispatchMessage(new MyMessage('Doc Saved ' . $slug . ' - at ' . $time));

//        $message = new MyMessage(1, $path);
//        $bus->dispatch($message);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, "HTML");
        $objWriter->save('helloWorld.html');
        return 1;
//
//        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
//        $objWriter->save('helloWorld.odt');
    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }
}