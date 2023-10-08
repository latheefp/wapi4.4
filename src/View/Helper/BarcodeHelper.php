<?php
namespace App\View\Helper;
//https://github.com/codeitnowin/barcode-generator
use Cake\View\Helper;
use Cake\View\View;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use CodeItNow\BarcodeBundle\Utils\QrCode;

class BarcodeHelper extends Helper
{
    public function initialize(array $config): void
    {
   //     debug($config);
    }
    
    function create($option=[]){
       // debug ($option);
        $barcode = new BarcodeGenerator();
        $text=$option['text'];
        $barcode->setText("$text");
        $barcode->setScale($option['scale']);
        $barcode->setThickness($option['thickness']);
        $barcode->setFontSize($option['fontsize']);
        switch ($option['type']){
            case "Code128":
                $barcode->setType(BarcodeGenerator::Code128);
                break;
            case "Codabar":
                $barcode->setType(BarcodeGenerator::Codabar);
                break;
            case "Code11":
                $barcode->setType(BarcodeGenerator::Code11);
                break;
            case "Code39":
                $barcode->setType(BarcodeGenerator::Code39);
                break;
            case "Code39-Extended":
                $barcode->setType(BarcodeGenerator::Code39-Extended);
                break;
            case "Ean128":
                $barcode->setType(BarcodeGenerator::Ean128);
                break;
             case "Ean13":
                $barcode->setType(BarcodeGenerator::Ean13);
                break;
            case "Gs1128":
                $barcode->setType(BarcodeGenerator::Gs1128);
                break;
            case "I25":
                $barcode->setType(BarcodeGenerator::I25);
                break;
            case "Isbn":
                $barcode->setType(BarcodeGenerator::Isbn);
                break;
            case "Msi":
                $barcode->setType(BarcodeGenerator::Msi);
                break;
            case "S25":
                $barcode->setType(BarcodeGenerator::S25);
                break;
            case "Upca":
                $barcode->setType(BarcodeGenerator::Upca);
                break;
        }
        $code = $barcode->generate();
        return $code;
    }
    
    
}

