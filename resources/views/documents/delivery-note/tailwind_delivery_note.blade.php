<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Note — {{ $data['dn_number'] ?? '' }}</title>
    <style>
        {!! file_get_contents(public_path('build/' . json_decode(file_get_contents(public_path('build/manifest.json')), true)['resources/css/pdf.css']['file'])) !!}

        /* Print reliability */
        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tr,
            img {
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }

            /* repeat headers on new pages */
            tfoot {
                display: table-footer-group;
            }
        }

        /* Typographic refinement */
        .khmer {
            font-family: 'Khmer Muol Light', 'Khmer OS Muol Light', sans-serif;
        }

        /* Long-text safety */
        .wrap-break-wordrds {
            overflow-wrap: anywhere;
            word-break: break-word;
        }
    </style>
</head>

<body class="p-8 text-slate-800 text-[11px] font-sans">

    <?php
        /* ---------- Logo resolution (multi-format, null-safe) ---------- */
        $relativePath = $data['company']['logo_path'] ?? 'logos/default-logo.png';
        $fullPath = storage_path('app/public/' . $relativePath);
        $logoBase64 = null;

        if (file_exists($fullPath)) {
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                default => 'image/png',
            };
            $logoBase64 = "data:{$mime};base64," . base64_encode(file_get_contents($fullPath));
        }

        /* ---------- Null-safe accessors ---------- */
        $company = $data['company'] ?? [];
        $customer = $data['customer'] ?? [];
        $items = $data['items'] ?? [];
        $val = fn($arr, $key, $default = '—') => filled($arr[$key] ?? null) ? $arr[$key] : $default;
    ?>

    {{-- ============ HEADER ============ --}}
    <div class="flex items-start justify-between border-b-2 border-[#0b3954] pb-4 mb-3">
        <div class="flex items-center gap-3">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ $val($company, 'name_en', 'Company') }} logo"
                    class="h-14 w-auto object-contain">
            @else
                <img src="{{ asset('logos/default-logo.png') }}" alt="ORBIT PA logo" class="h-14 w-auto object-contain">
            @endif
        </div>

        <div class="text-right max-w-md">
            @if(!empty($company['name_kh']))
                <div class="khmer text-sm text-green-500 leading-normal">{{ $company['name_kh'] }}</div>
            @endif
            <div class="text-sm font-bold text-green-500">{{ $val($company, 'name_en') }}</div>
            <div class="text-[9px] text-slate-500 mt-1 leading-relaxed">
                {{ $val($company, 'address') }}<br>
                @if(!empty($company['email'])) EMAIL: {{ $company['email'] }} @endif
                @if(!empty($company['tel'])) · TEL: {{ $company['tel'] }} @endif
                @if(!empty($company['website']))· {{ $company['website'] }} @endif
            </div>
        </div>
    </div>

    {{-- ============ TITLE ============ --}}
    <div class="text-center font-bold text-base text-[#0b3954] uppercase tracking-wider py-1.5
                border-y border-[#0b3954] mb-4 flex items-center justify-center gap-3">
        <span>Delivery Note</span>
        @if(!empty($data['status']))
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full
                bg-[#0b3954] text-white normal-case tracking-normal">
                {{ $data['status'] }}
            </span>
        @endif
    </div>

    {{-- ============ META GRID ============ --}}
    <div class="grid grid-cols-12 gap-3 mb-4">
        {{-- Customer --}}
        <div class="col-span-7 border border-slate-300 p-2.5 rounded text-[10px] space-y-0.5">
            <div class="wrap-wrap-break-wordrds"><span class="font-semibold text-slate-700">Customer:</span>
                {{ $val($customer, 'name') }}
            </div>
            <div class="wrap-wrap-break-wordrds"><span class="font-semibold text-slate-700">Address:</span>
                {{ $val($customer, 'address') }}
            </div>
            <div class="wrap-wrap-break-wordrds"><span class="font-semibold text-slate-700">Contact Person:</span>
                {{ $val($customer, 'contact_person') }}
            </div>
            <div><span class="font-semibold text-slate-700">Tel:</span> {{ $val($customer, 'tel') }}</div>
            <div class="wrap-wrap-break-wordrds"><span class="font-semibold text-slate-700">Project:</span>
                {{ $val($data, 'project') }}
            </div>
        </div>

        {{-- Document meta --}}
        <div class="col-span-5 border border-slate-300 p-2.5 rounded text-[10px]">
            @foreach([
                    'Delivery Note #' => $data['dn']['dn_number'] ?? null,
                    'Delivery Date' => $data['dn']['delivery_date'] ?? null,
                    'Reference' => $data['dn']['reference_no'] ?? null,
                    'PO Number' => $data['dn']['po_number'] ?? null,
                ] as $label => $value)
                <div class="flex justify-between py-0.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                    <span class="font-bold">{{ $label }}:</span>
                    <span class="text-right wrap-break-wordrds">{{ filled($value) ? $value : '—' }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ============ ITEMS TABLE ============ --}}
    <table class="w-full border-collapse border border-slate-800 text-[10px]">
        <thead>
            <tr class="bg-slate-100 border-b border-slate-800">
                <th class="border-r border-slate-800 py-1.5 px-2 text-center w-12">Item No</th>
                <th class="border-r border-slate-800 py-1.5 px-2 text-left">Description</th>
                <th class="border-r border-slate-800 py-1.5 px-2 text-left w-32">Reference</th>
                <th class="border-r border-slate-800 py-1.5 px-2 text-center w-12">Qty</th>
                <th class="border-r border-slate-800 py-1.5 px-2 text-center w-14">UoM</th>
                <th class="py-1.5 px-2 text-left w-20">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr class="border-b border-slate-300 even:bg-slate-50">
                    <td class="border-r border-slate-800 py-2 px-2 text-center font-bold">
                        {{ $item['item_no'] ?? $loop->iteration }}
                    </td>
                    <td class="border-r border-slate-800 py-2 px-2 wrap-break-wordrds">
                        <div class="font-semibold text-slate-900">{{ $item['description'] ?? '—' }}</div>
                        @if(!empty($item['serial_numbers']))
                            <div class="text-[9px] text-slate-500 mt-1 italic">
                                Serial Number:<br>
                                @foreach($item['serial_numbers'] as $sn)
                                    &bull; {{ $sn }}<br>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="border-r border-slate-800 py-2 px-2 text-slate-700 wrap-break-wordrds">
                        {{ $item['reference'] ?? '—' }}
                    </td>
                    <td class="border-r border-slate-800 py-2 px-2 text-center font-bold">{{ $item['qty'] ?? 0 }}</td>
                    <td class="border-r border-slate-800 py-2 px-2 text-center">{{ $item['uom'] ?? '—' }}</td>
                    <td class="py-2 px-2 wrap-break-wordrds">{{ $item['remarks'] ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-400 italic">
                        No items listed on this delivery note.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($items) > 0)
            <tfoot>
                <tr class="bg-slate-50 border-t-2 border-slate-800 font-bold">
                    <td colspan="3" class="border-r border-slate-800 py-1.5 px-2 text-right">Total Items:</td>
                    <td class="border-r border-slate-800 py-1.5 px-2 text-center">{{ count($items) }}</td>
                    <td colspan="2" class="py-1.5 px-2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ============ NOTES ============ --}}
    <div class="border-x border-b border-slate-800 p-2 text-[9.5px] wrap-break-wordrds">
        <span class="font-bold">Note:</span> {{ $val($data, 'note', 'No additional notes.') }}
    </div>

    {{-- ============ SIGNATURES ============ --}}
    <div class="grid grid-cols-4 border border-slate-800 mt-6 divide-x divide-slate-800 text-center">
        @foreach([
                ['Approved By', 'Name / Signature'],
                ['Deliver By', 'Name / Signature'],
                ['Check By', 'Name / Signature'],
                ['Receive By', 'Customer Signature'],
            ] as [$title, $hint])
            <div class="p-2 flex flex-col justify-between h-24">
                <span class="font-bold text-[10px]">{{ $title }}</span>
                <span class="text-[9px] text-slate-400 border-t border-dotted border-slate-400 pt-1">{{ $hint }}</span>
            </div>
        @endforeach
    </div>

    {{-- ============ FOOTER ============ --}}
    <div class="mt-3 pt-2 border-t border-slate-200 flex justify-between text-[8px] text-slate-400">
        <span>Generated {{ now()->format('d M Y, H:i') }}</span>
        <span>{{ $val($data, 'dn_number', 'Delivery Note') }}</span>
    </div>

</body>

</html>