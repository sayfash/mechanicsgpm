<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Invoice - {{ $record['job_id'] ?? $record['id'] }}</title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @page {
            size: A5 landscape;
            margin: 8mm;
        }
        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border-color: #cbd5e1 !important;
            }
            .printable-container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 p-4 sm:p-6">

    <!-- Print Action Controls (Hidden when printed) -->
    <div class="max-w-5xl mx-auto mb-4 flex justify-between items-center no-print">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
            <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
            <span>Customer Invoice (A5 Landscape)</span>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf"></i> Simpan / Cetak PDF
            </button>
            <button onclick="window.close()" class="px-4 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition flex items-center gap-1.5">
                <i class="fa-solid fa-xmark"></i> Close
            </button>
        </div>
    </div>

    <!-- Printable Invoice Card Sheet -->
    <div class="printable-container max-w-5xl mx-auto bg-white border border-slate-200 rounded-xl shadow-md p-6 text-xs print-border">
        
        <!-- Header Row -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-3 mb-3 print-border">
            <div class="space-y-0.5">
                <div class="flex items-center gap-1.5">
                    <span class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-1.5">
                        <i class="fa-solid fa-infinity text-blue-600"></i> SGPM SERVICE CENTER
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold uppercase tracking-wider">OFFICIAL INVOICE</span>
                </div>
                <p class="text-[10px] text-slate-500 font-medium">Outlet: <strong class="text-slate-700">{{ $record['branch_name'] ?? 'Main Branch' }}</strong></p>
            </div>
            <div class="text-right space-y-0.5 font-mono text-[11px]">
                <div><span class="text-slate-400">Invoice No:</span> <strong class="text-slate-900 font-bold">{{ $record['job_id'] ?? $record['id'] }}</strong></div>
                <div><span class="text-slate-400">Date:</span> <span class="text-slate-700">{{ $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('d/m/Y H:i') : 'N/A' }}</span></div>
            </div>
        </div>

        <!-- 2-Column Info Grid: Customer & Vehicle Information -->
        <div class="grid grid-cols-2 gap-3 mb-3">
            <!-- Customer Info -->
            <div class="bg-slate-50/80 border border-slate-200 rounded-lg p-2.5 print-border">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1 flex items-center gap-1 border-b border-slate-200 pb-1">
                    <i class="fa-solid fa-user text-blue-600"></i> Customer Details
                </div>
                <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-[11px]">
                    <div><span class="text-slate-400">Name:</span> <strong class="text-slate-900">{{ $record['customer_name'] ?? 'N/A' }}</strong></div>
                    <div><span class="text-slate-400">Phone:</span> <span class="font-mono text-slate-800">{{ $record['customer_phone'] ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400">Status:</span> <span class="font-semibold text-slate-700">{{ $record['customer_status'] ?? 'Retail' }}</span></div>
                    <div class="col-span-2 truncate"><span class="text-slate-400">Address:</span> <span class="text-slate-800">{{ $record['customer_address'] ?? 'N/A' }}</span></div>
                </div>
            </div>

            <!-- Vehicle Info -->
            <div class="bg-slate-50/80 border border-slate-200 rounded-lg p-2.5 print-border">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1 flex items-center gap-1 border-b border-slate-200 pb-1">
                    <i class="fa-solid fa-motorcycle text-emerald-600"></i> Vehicle Specifications
                </div>
                <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-[11px]">
                    <div><span class="text-slate-400">Plate No:</span> <strong class="font-mono text-blue-600 font-bold">{{ $record['license_plate'] ?? 'N/A' }}</strong></div>
                    <div><span class="text-slate-400">Type/Model:</span> <span class="font-semibold text-slate-800">{{ $record['make'] ?? '' }} {{ $record['vehicle_type'] ?? '' }}</span></div>
                    <div><span class="text-slate-400">Odometer:</span> <span class="font-mono text-slate-800">{{ $record['km_reached'] ? number_format($record['km_reached']) . ' KM' : '0 KM' }}</span></div>
                    <div><span class="text-slate-400">VIN/Frame:</span> <span class="font-mono text-slate-800">{{ $record['vin'] ?? $record['frame_number'] ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Items & Services Table -->
        <div class="border border-slate-200 rounded-lg overflow-hidden mb-3 print-border">
            <table class="w-full text-[11px] text-left">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold uppercase text-[9px] tracking-wider print-border">
                        <th class="py-1.5 px-3">Description / Item Name</th>
                        <th class="py-1.5 px-2">SKU Code</th>
                        <th class="py-1.5 px-2 text-center w-12">Qty</th>
                        <th class="py-1.5 px-3 text-right w-28">Unit Price</th>
                        <th class="py-1.5 px-3 text-right w-32">Total (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- Spare Parts Section -->
                    @if(!empty($record['parts']) && count($record['parts']) > 0)
                        @foreach($record['parts'] as $part)
                            @php
                                $partQty = $part['quantity_used'] ?? 1;
                                $partPrice = $part['price_at_use'] ?? 0;
                                $partTotal = $partQty * $partPrice;
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-1 px-3 font-medium text-slate-800">{{ $part['part_name'] }}</td>
                                <td class="py-1 px-2 font-mono text-slate-400 text-[10px]">{{ $part['sku'] ?? '-' }}</td>
                                <td class="py-1 px-2 text-center font-mono font-bold">{{ $partQty }}</td>
                                <td class="py-1 px-3 text-right font-mono text-slate-600">Rp {{ number_format($partPrice, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($partTotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Service Fees Section -->
                    @if(!empty($record['labor_fee']) && $record['labor_fee'] > 0)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-1 px-3 font-medium text-slate-800">{{ $record['service_name'] ?? 'Labor / Work Service Fee' }}</td>
                            <td class="py-1 px-2 font-mono text-slate-400 text-[10px]">SVC-LABOR</td>
                            <td class="py-1 px-2 text-center font-mono font-bold">1</td>
                            <td class="py-1 px-3 text-right font-mono text-slate-600">Rp {{ number_format($record['labor_fee'], 0, ',', '.') }}</td>
                            <td class="py-1 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($record['labor_fee'], 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    @if(!empty($record['other_expenses_fee']) && $record['other_expenses_fee'] > 0)
                        @php
                            $otherSkuDisplay = $record['other_expenses_sku'] ?? $record['service_sku'] ?? 'SVC-MISC';
                        @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-1 px-3 font-medium text-slate-800">Additional Service: {{ $record['other_expenses_category'] ?? 'Other Fee' }}</td>
                            <td class="py-1 px-2 font-mono text-slate-400 text-[10px]">{{ $otherSkuDisplay }}</td>
                            <td class="py-1 px-2 text-center font-mono font-bold">1</td>
                            <td class="py-1 px-3 text-right font-mono text-slate-650">Rp {{ number_format($record['other_expenses_fee'], 0, ',', '.') }}</td>
                            <td class="py-1 px-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($record['other_expenses_fee'], 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    @if(empty($record['parts']) && (empty($record['labor_fee']) || $record['labor_fee'] == 0) && (empty($record['other_expenses_fee']) || $record['other_expenses_fee'] == 0))
                        <tr>
                            <td colspan="5" class="py-2 px-3 text-slate-400 italic text-center">No billable items or services recorded.</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    @php
                        $laborSum = floatval($record['labor_fee'] ?? 0) + floatval($record['other_expenses_fee'] ?? 0);
                        $partsSum = 0;
                        if (!empty($record['parts'])) {
                            foreach ($record['parts'] as $p) {
                                $partsSum += (floatval($p['price_at_use'] ?? 0) * intval($p['quantity_used'] ?? 1));
                            }
                        }
                        $grandTotal = $record['grand_total'] ?? ($laborSum + $partsSum);
                    @endphp
                    <tr class="bg-slate-50 border-t border-slate-200 font-bold print-border">
                        <td colspan="4" class="py-1.5 px-3 text-right uppercase tracking-wider text-[10px] text-slate-600">Grand Total Amount:</td>
                        <td class="py-1.5 px-3 text-right font-mono font-extrabold text-sm text-emerald-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer Signatures & Term Notes -->
        <div class="flex justify-between items-end pt-2 border-t border-slate-200 print-border">
            <div class="text-[9px] text-slate-400 space-y-0.5">
                <p class="font-bold text-slate-600 uppercase tracking-wider">Thank you for choosing SGPM Service Center!</p>
                <p>Mechanic: <strong class="text-slate-700">{{ $record['mechanic_name'] ?? 'Assigned Specialist' }}</strong></p>
            </div>
            <div class="flex gap-12 text-center text-[10px]">
                <div>
                    <span class="text-slate-400 block text-[9px] uppercase font-bold">Customer Signature</span>
                    <div class="border-b border-slate-300 w-28 mt-7 mb-1 print-border"></div>
                    <span class="font-semibold text-slate-800 block text-[10px]">{{ $record['customer_name'] ?? 'Customer' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[9px] uppercase font-bold">Authorized Cashier</span>
                    <div class="border-b border-slate-300 w-28 mt-7 mb-1 print-border"></div>
                    <span class="font-semibold text-slate-800 block text-[10px]">{{ $record['cashier_name'] ?? (auth()->user()->display_name ?? auth()->user()->name ?? 'Authorized Cashier') }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
