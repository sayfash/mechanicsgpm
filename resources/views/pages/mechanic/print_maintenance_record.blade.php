<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order - {{ $record['job_id'] ?? $record['id'] }}</title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Courier New', 'monospace'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        @page {
            size: 80mm auto;
            margin: 0;
        }
        @media print {
            html, body {
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .printable-container {
                width: 80mm !important;
                max-width: 80mm !important;
                padding: 3mm 2mm !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 p-2 sm:p-4 flex flex-col items-center">

    <!-- Action Bar (Hidden on print) -->
    <div class="w-[80mm] max-w-full mb-3 flex justify-between items-center no-print">
        <span class="text-xs font-bold text-slate-600">80mm Thermal Paper Work Order</span>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1">
                <i class="fa-solid fa-print"></i> Print
            </button>
            <button onclick="window.close()" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition flex items-center gap-1">
                <i class="fa-solid fa-xmark"></i> Close
            </button>
        </div>
    </div>

    <!-- 80mm Thermal Paper Wrapper -->
    <div class="printable-container w-[80mm] max-w-[80mm] bg-white border border-slate-300 p-3 shadow-md text-xs leading-tight font-sans text-black">
        
        <!-- Header -->
        <div class="text-center border-b border-black pb-2 mb-2">
            <h1 class="text-base font-extrabold tracking-tight uppercase">SGPM SERVICE CENTER</h1>
            <p class="text-[10px] font-bold uppercase tracking-wider">{{ $record['branch_name'] ?? 'Main Branch' }}</p>
            <p class="text-[11px] font-bold mt-1 border-t border-b border-dashed border-black py-0.5 uppercase">SURAT PERINTAH KERJA (WORK ORDER)</p>
        </div>

        <!-- Large Noticeable Queue Number Banner -->
        @php
            $queueFormatted = $record['daily_queue_formatted'] ?? sprintf('#%02d', $record['daily_queue_number'] ?? 1);
        @endphp
        <div class="my-2 py-1.5 px-2 border-2 border-black rounded text-center bg-slate-50">
            <span class="text-[9px] uppercase font-extrabold tracking-widest block text-slate-700">NO. ANTREAN HARI INI</span>
            <span class="text-4xl font-black font-sans tracking-tight block leading-none py-1">{{ $queueFormatted }}</span>
        </div>

        <!-- Meta Details -->
        <div class="text-[11px] font-mono border-b border-dashed border-black pb-2 mb-2 space-y-0.5">
            <div class="flex justify-between">
                <span>Job ID:</span>
                <strong class="font-bold">{{ $record['job_id'] ?? $record['id'] }}</strong>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span>{{ $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('d/m/Y H:i') : '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Mekanik:</span>
                <strong class="truncate max-w-[120px]">{{ $record['mechanic_name'] ?? $record['mechanic_display_name'] ?? $record['mechanic_username'] ?? 'Unassigned' }}</strong>
            </div>
            <div class="flex justify-between">
                <span>Kategori:</span>
                <span class="uppercase font-bold">{{ $record['repair_category'] ?? 'REPAIR' }}</span>
            </div>
        </div>

        <!-- Customer & Vehicle Info -->
        <div class="border-b border-dashed border-black pb-2 mb-2 space-y-1">
            <div>
                <span class="text-[9px] uppercase font-bold text-slate-500 block">PELANGGAN:</span>
                <div class="font-bold text-xs">{{ $record['customer_name'] ?? 'N/A' }}</div>
                <div class="text-[10px] font-mono">{{ $record['customer_phone'] ?? '' }}</div>
            </div>
            <div class="pt-1 border-t border-slate-200">
                <span class="text-[9px] uppercase font-bold text-slate-500 block">KENDARAAN:</span>
                <div class="flex justify-between items-center font-bold">
                    <span>{{ $record['make'] ?? '' }} {{ $record['vehicle_type'] ?? '' }}</span>
                    <span class="font-mono text-sm border border-black px-1 rounded">{{ $record['license_plate'] ?? 'N/A' }}</span>
                </div>
                <div class="text-[10px] font-mono flex justify-between mt-0.5">
                    <span>VIN: {{ $record['vin'] ?? $record['frame_number'] ?? 'N/A' }}</span>
                    <span>{{ $record['km_reached'] ? number_format($record['km_reached']) . ' KM' : '0 KM' }}</span>
                </div>
            </div>
        </div>

        <!-- Diagnostic & Notes -->
        @if(!empty($record['common_issues']) || !empty($record['other_issues']) || !empty($record['notes']) || !empty($record['description']))
        <div class="border-b border-dashed border-black pb-2 mb-2">
            <span class="text-[9px] uppercase font-bold text-slate-500 block">DIAGNOSA & KELUHAN:</span>
            @if(!empty($record['common_issues']))
                <div class="text-[10px] font-semibold text-slate-800 mt-0.5">
                    • {{ str_replace(',', ', ', $record['common_issues']) }}
                </div>
            @endif
            @php
                $customNotes = !empty($record['other_issues']) ? $record['other_issues'] : (!empty($record['notes']) ? $record['notes'] : (!empty($record['description']) && $record['description'] !== 'General Intake Repair Service' ? $record['description'] : ''));
            @endphp
            @if(!empty($customNotes))
                <div class="text-[10px] italic text-slate-700 mt-0.5">
                    Catatan: {{ $customNotes }}
                </div>
            @endif
        </div>
        @endif

        <!-- Spare Parts List (NO PRICES + NOTES SPACE) -->
        <div class="border-b border-dashed border-black pb-2.5 mb-2">
            <div class="flex justify-between items-center border-b border-black pb-1 mb-1 font-bold text-[10px] uppercase">
                <span>DAFTAR SUKU CADANG</span>
                <span>QTY</span>
            </div>
            @if(!empty($record['parts']) && count($record['parts']) > 0)
                <div class="space-y-1.5">
                    @foreach($record['parts'] as $index => $part)
                        @php
                            $partQty = $part['quantity_used'] ?? 1;
                            $pNotes = $part['notes'] ?? $part['part_notes'] ?? '';
                        @endphp
                        <div class="text-[11px] leading-tight">
                            <div class="flex justify-between items-start font-bold">
                                <span class="pr-2">{{ $index + 1 }}. {{ $part['part_name'] }}</span>
                                <span class="font-mono text-xs shrink-0">x{{ $partQty }}</span>
                            </div>
                            @if(!empty($part['sku']))
                                <div class="text-[9px] font-mono text-slate-600 pl-3">SKU: {{ $part['sku'] }}</div>
                            @endif
                            @if(!empty($pNotes))
                                <div class="text-[9px] italic text-slate-700 pl-3">Catatan: {{ $pNotes }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-[10px] italic text-slate-500 py-0.5 text-center">TIDAK ADA PEMAKAIAN SUKU CADANG</div>
            @endif

            <!-- Dedicated Notes Space for Spare Parts -->
            <div class="mt-2 pt-1 border-t border-dotted border-slate-400 text-[9.5px]">
                <span class="font-semibold text-slate-600 block">Catatan Suku Cadang / Part Remarks:</span>
                <div class="h-4 border-b border-dotted border-slate-400 mt-1"></div>
            </div>
        </div>

        <!-- Services & Labor List (LIST FORMAT + NO PRICES + NOTES SPACE) -->
        <div class="border-b border-black pb-2.5 mb-3">
            <div class="border-b border-black pb-1 mb-1.5 font-bold text-[10px] uppercase">
                <span>DAFTAR JASA & LAYANAN PERBAIKAN</span>
            </div>
            @php
                $servicesList = [];

                // 1. Process Service Options (Jasa Utama)
                if (!empty($record['service_options']) && is_array($record['service_options'])) {
                    foreach ($record['service_options'] as $sOpt) {
                        $sName = is_array($sOpt) ? ($sOpt['name'] ?? $sOpt['service_name'] ?? '') : (string)$sOpt;
                        if (!empty(trim($sName))) {
                            $servicesList[] = [
                                'name' => trim($sName),
                                'sku' => null,
                                'type' => 'Jasa Utama'
                            ];
                        }
                    }
                } elseif (!empty($record['service_name'])) {
                    $splitNames = array_map('trim', explode(',', $record['service_name']));
                    foreach ($splitNames as $sName) {
                        if (!empty($sName)) {
                            $servicesList[] = [
                                'name' => $sName,
                                'sku' => null,
                                'type' => 'Jasa Utama'
                            ];
                        }
                    }
                } elseif (!empty($record['labor_fee']) && $record['labor_fee'] > 0) {
                    $servicesList[] = [
                        'name' => 'Jasa Perbaikan / Labor Service',
                        'sku' => null,
                        'type' => 'Jasa Utama'
                    ];
                }

                // 2. Process Other Services (Layanan Tambahan)
                if (!empty($record['other_services']) && is_array($record['other_services'])) {
                    foreach ($record['other_services'] as $oOpt) {
                        $oName = is_array($oOpt) ? ($oOpt['category'] ?? $oOpt['name'] ?? '') : (string)$oOpt;
                        $oSku = is_array($oOpt) ? ($oOpt['sku'] ?? null) : null;
                        if (!empty(trim($oName))) {
                            $servicesList[] = [
                                'name' => trim($oName),
                                'sku' => $oSku,
                                'type' => 'Layanan Tambahan'
                            ];
                        }
                    }
                } elseif (!empty($record['other_expenses_category'])) {
                    $splitOtherNames = array_map('trim', explode(',', $record['other_expenses_category']));
                    $splitOtherSkus = !empty($record['other_expenses_sku']) ? array_map('trim', explode(',', $record['other_expenses_sku'])) : [];
                    foreach ($splitOtherNames as $oIdx => $oName) {
                        if (!empty($oName)) {
                            $servicesList[] = [
                                'name' => $oName,
                                'sku' => $splitOtherSkus[$oIdx] ?? null,
                                'type' => 'Layanan Tambahan'
                            ];
                        }
                    }
                } elseif (!empty($record['other_expenses_fee']) && $record['other_expenses_fee'] > 0) {
                    $servicesList[] = [
                        'name' => 'Layanan / Biaya Tambahan',
                        'sku' => null,
                        'type' => 'Layanan Tambahan'
                    ];
                }
            @endphp

            @if(count($servicesList) > 0)
                <div class="space-y-1.5">
                    @foreach($servicesList as $sIndex => $sItem)
                        <div class="text-[11px] leading-tight">
                            <div class="font-bold flex items-start justify-between">
                                <span>{{ $sIndex + 1 }}. {{ $sItem['name'] }}</span>
                                <span class="text-[8.5px] font-mono font-semibold px-1 bg-slate-100 border border-slate-300 rounded text-slate-700 ml-1 shrink-0">{{ $sItem['type'] }}</span>
                            </div>
                            @if(!empty($sItem['sku']))
                                <div class="text-[9px] font-mono text-slate-600 pl-3">Kode: {{ $sItem['sku'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-[10px] italic text-slate-500 py-0.5 text-center">TIDAK ADA JASA ATAU LAYANAN TERDAFTAR</div>
            @endif

            <!-- Dedicated Notes Space for Services -->
            <div class="mt-2 pt-1 border-t border-dotted border-slate-400 text-[9.5px]">
                <span class="font-semibold text-slate-600 block">Catatan Pengerjaan / Service Notes:</span>
                <div class="h-4 border-b border-dotted border-slate-400 mt-1"></div>
            </div>
        </div>

        <!-- Signatures Section -->
        <div class="pt-1 text-center">
            <div class="grid grid-cols-2 gap-2 text-[10px]">
                <div>
                    <span class="block text-slate-600 font-semibold mb-8">Tanda Tangan Pelanggan</span>
                    <div class="border-b border-black mx-auto w-24 mb-1"></div>
                    <span class="font-bold truncate block">{{ $record['customer_name'] ?? 'Pelanggan' }}</span>
                </div>
                <div>
                    <span class="block text-slate-600 font-semibold mb-8">Mekanik Spesialis</span>
                    <div class="border-b border-black mx-auto w-24 mb-1"></div>
                    <span class="font-bold truncate block">{{ $record['mechanic_name'] ?? $record['mechanic_display_name'] ?? 'Mekanik' }}</span>
                </div>
            </div>
            <p class="text-[8px] italic text-slate-500 mt-4">Terima kasih atas kepercayaan Anda di SGPM Service Center.</p>
        </div>

    </div>

    <!-- Trigger browser print automatically -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
