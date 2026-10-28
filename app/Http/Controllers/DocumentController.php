<?php

namespace App\Http\Controllers;

// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class DocumentController extends Controller
{

    public function streamTailwindDeliveryNote()
    {
        $data = [
            'company' => [
                'name_kh' => 'អរប៊ីត ភី.អេ ឯ.ក',
                'name_en' => 'ORBIT P.A CO., LTD',
                'address' => 'Borey Vimean Phnom Penh Thmey, #45P, St.209B, Phum 4, Sangkat Chrang Chamres 1, Khan Russeykeo, Phnom Penh, Kingdom of Cambodia',
                'email' => 'info@orbitpa.com',
                'tel' => '(855) 81 461 855',
                'website' => 'www.orbitpa.com',
            ],
            'dn' => [
                'dn_number' => 'OBDN-2026-0064',
                'delivery_date' => '06-Oct-2026',
                'reference_no' => 'OBQ-000010840',
                'po_number' => 'N/A',
            ],
            'project' => 'Delivery UPS 20KVA and AVR 30KVA Project',
            'customer' => [
                'name' => 'SPACELOGIC INTERNATIONAL (CAMBODIA) CO., LTD.',
                'address' => '#5R Street 252, Sangkat Chaktomuk, Daun Penh District, Phnom Penh City.',
                'contact_person' => 'Chanthorn Long',
                'tel' => '+855 81 775 557',
            ],
            'items' => [
                [
                    'item_no' => '01',
                    'description' => 'Easy UPS 3S 20kVA 400V 3:3 UPS for Internal Batteries APC UPS',
                    'reference' => 'E3SUPS20KHB',
                    'qty' => 1,
                    'uom' => 'UNIT',
                    'remarks' => '',
                    'serial_numbers' => ['9E2601T15023'],
                ],
                [
                    'item_no' => '02',
                    'description' => 'QPS Three Phase Automatic Voltage Stabilizer 30KVA 3Phase',
                    'reference' => 'T30-3',
                    'qty' => 1,
                    'uom' => 'UNIT',
                    'remarks' => '',
                    'serial_numbers' => ['726A001'],
                ],
            ],
            'status' => 'Approved',
            'note' => 'Please inspect all goods upon delivery. Goods received in good order and condition.',
        ];

        return Pdf::view('documents.delivery-note.tailwind_delivery_note', compact('data'))
            ->format(Format::A4)
            ->withBrowsershot(function ($browsershot) {
                // Path to installed Google Chrome on Windows
                $chromePath = 'C:\Program Files\Google\Chrome\Application\chrome.exe';

                // Fallback for 32-bit Program Files if needed
                if (!file_exists($chromePath)) {
                    $chromePath = 'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe';
                }

                $browsershot->setChromePath($chromePath)
                    ->noSandbox();
            })
            ->name('OBDN-2026-0064.pdf');
    }
}