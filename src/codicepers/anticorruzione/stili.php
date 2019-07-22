<?php
/*
 * Created on 01/dic/2015
 */

$phpWord->setDefaultFontName('Calibri');
$phpWord->setDefaultFontSize(10);

$arrayStili = array(
	/* 'titolo1' => array('name' => 'Calibri', 'size' => 22, 'color' => '000000', 'bold' => false), */	//al momeno non utilizzato
	'titolo2' => array('name' => 'Calibri', 'size' => 16, 'color' => '000000', 'bold' => true, 'underline' => 'single', 'italic' => true),
	'titolo3' => array('name' => 'Calibri', 'size' => 14, 'color' => '000000', 'bold' => false, 'underline' => 'single'),
	'titolo4' => array('name' => 'Calibri', 'size' => 12, 'color' => '000000', 'bold' => true),
	'testo'   => array('name' => 'Calibri', 'size' => 10, 'color' => '000000', 'bold' => false),
	'bold'    => array('bold' => true),
	'toc'	  => array('spaceBefore' => 0, 'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(5), 'size' => 10, 'lineHeight' => '1.0'),
);

$tocStyle = $arrayStili['toc'];

$phpWord->addTitleStyle('1', $arrayStili['titolo1']);
$phpWord->addFontStyle(
    'titolo1',
    $arrayStili['titolo1']
);

$phpWord->addTitleStyle('2', $arrayStili['titolo2']);
$phpWord->addFontStyle(
    'titolo2',
    $arrayStili['titolo2']
);

$phpWord->addTitleStyle('3', $arrayStili['titolo3']);
$phpWord->addFontStyle(
    'titolo3',
    $arrayStili['titolo3']
);

$phpWord->addTitleStyle('4', $arrayStili['titolo4']);
$phpWord->addFontStyle(
    'titolo4',
    $arrayStili['titolo4']
);

$phpWord->addFontStyle(
    'testo',
    $arrayStili['testo']
);

$paragraphStyle = array('align' => 'both');


$styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '000000');
$phpWord->addTableStyle('table1', $styleTable, $styleFirstRow);

$styleCell = array('valign' => 'center');
?>