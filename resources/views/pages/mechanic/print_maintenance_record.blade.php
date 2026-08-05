<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Record - {{ $record['job_id'] ?? $record['id'] }}</title>
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
        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border-color: #000000 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 p-6 sm:p-10">

    <!-- Print Action Button (Hidden on print) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-end gap-3 no-print">
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Print Record
        </button>
        <button onclick="window.close()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-xmark"></i> Close Tab
        </button>
    </div>

    <!-- Printable Invoice Page -->
    <div class="max-w-4xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-8 print-border">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-200 pb-6 mb-6 print-border">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-1.5">
                        <i class="fa-solid fa-gears text-blue-600"></i> SGPM MECHANIC
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Service & Intake Record Invoice</p>
            </div>
            <div class="mt-4 sm:mt-0 text-left sm:text-right space-y-1 font-mono text-xs">
                <div><span class="text-slate-500">Job ID:</span> <strong class="text-slate-900">{{ $record['job_id'] ?? $record['id'] }}</strong></div>
                <div><span class="text-slate-500">Date Logged:</span> <span class="text-slate-800">{{ $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('M d, Y h:i A') : 'N/A' }}</span></div>
            </div>
        </div>

        <!-- Meta Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100 mb-8 print-border">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Operational Outlet</span>
                <strong class="text-slate-800 block text-sm mt-0.5">{{ $record['branch_name'] ?? 'N/A' }}</strong>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Assigned Mechanic</span>
                <strong class="text-slate-800 block text-sm mt-0.5">{{ $record['mechanic_name'] ?? 'Unassigned' }}</strong>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Job Status</span>
                <strong class="text-slate-800 block text-sm mt-0.5">{{ strtoupper($record['status'] ?? 'N/A') }}</strong>
            </div>
        </div>

        <!-- Customer & Vehicle Specifications -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Customer Panel -->
            <div class="border border-slate-200 rounded-xl p-5 print-border">
                <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-user text-blue-500"></i> Customer Info
                </h4>
                <table class="w-full text-xs text-left">
                    <tr class="border-b border-slate-100 last:border-0"><td class="py-2 text-slate-400">Name</td><td class="py-2 font-bold text-slate-850">{{ $record['customer_name'] ?? 'N/A' }}</td></tr>
                    <tr class="border-b border-slate-100 last:border-0"><td class="py-2 text-slate-400">Phone</td><td class="py-2 font-mono text-slate-800">{{ $record['customer_phone'] ?? 'N/A' }}</td></tr>
                    <tr class="border-b border-slate-100 last:border-0"><td class="py-2 text-slate-400">ID Card</td><td class="py-2 font-mono text-slate-800">{{ $record['customer_idcard'] ?? 'N/A' }}</td></tr>
                </table>
            </div>

            <!-- Vehicle Panel -->
            <div class="border border-slate-200 rounded-xl p-5 print-border">
                <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-motorcycle text-emerald-500"></i> Vehicle Specs
                </h4>
                <table class="w-full text-xs text-left">
                    <tr class="border-b border-slate-100 last:border-0"><td class="py-2 text-slate-400">Model</td><td class="py-2 font-bold text-slate-850">{{ $record['make'] ?? '' }} {{ $record['model'] ?? '' }}</td></tr>
                    <tr class="border-b border-slate-100 last:border-0"><td class="py-2 text-slate-400">License Plate</td><td class="py-2 font-mono font-bold text-blue-600">{{ $record['license_plate'] ?? 'N/A' }}</td></tr>
                    <tr class="border-b border-slate-100 last:border-0"><td class="py-2 text-slate-400">Frame Number (VIN)</td><td class="py-2 font-mono text-slate-800">{{ $record['vin'] ?? 'N/A' }}</td></tr>
                    <tr class="border-b border-slate-100 last:border-0"><td class="py-2 text-slate-400">Odometer</td><td class="py-2 text-slate-800">{{ $record['km_reached'] ? number_format($record['km_reached']) . ' KM' : '0 KM' }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Issue & Diagnostic Checklists -->
        <div class="border border-slate-200 rounded-xl p-5 mb-8 print-border space-y-4">
            <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-clipboard-list text-purple-500"></i> Job Intake Diagnostics & Checklists
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold mb-1">Common Issues Reported</span>
                    @if(!empty($record['common_issues']))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(explode(',', $record['common_issues']) as $issue)
                                <span class="px-2.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-100 font-semibold text-[10px]">{{ trim($issue) }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-slate-400 italic">None reported</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold mb-1">General Intake / Repair Notes</span>
                    <p class="text-slate-700 font-medium leading-relaxed">{{ $record['description'] ?? 'General Intake Repair Service' }}</p>
                </div>
            </div>
        </div>

        <!-- Billing & Invoice breakdown -->
        <div class="border border-slate-200 rounded-xl overflow-hidden mb-8 print-border">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px] print-border">
                        <th class="p-3">Billing Item</th>
                        <th class="p-3">SKU / Code</th>
                        <th class="p-3 text-center">Qty</th>
                        <th class="p-3 text-right">Unit Price</th>
                        <th class="p-3 text-right">Total Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    <!-- Parts Used -->
                    @if(!empty($record['parts']) && count($record['parts']) > 0)
                        @foreach($record['parts'] as $part)
                            @php
                                $partQty = $part['quantity_used'] ?? 1;
                                $partPrice = $part['price_at_use'] ?? 0;
                                $partTotal = $partQty * $partPrice;
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-semibold text-slate-800">{{ $part['part_name'] }}</td>
                                <td class="p-3 font-mono text-slate-400">{{ $part['sku'] ?? 'N/A' }}</td>
                                <td class="p-3 text-center font-bold">{{ $partQty }}</td>
                                <td class="p-3 text-right font-mono text-slate-650">Rp{{ number_format($partPrice, 0, ',', '.') }}</td>
                                <td class="p-3 text-right font-mono font-bold text-slate-800">Rp{{ number_format($partTotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Labor Fee -->
                    @if(!empty($record['labor_fee']) && $record['labor_fee'] > 0)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 font-semibold text-slate-800">🔧 Service Labor: {{ $record['service_name'] ?? 'Labor Service' }}</td>
                            <td class="p-3 font-mono text-slate-400">{{ $record['service_sku'] ?? 'SERVICE-LABOR' }}</td>
                            <td class="p-3 text-center font-bold">1</td>
                            <td class="p-3 text-right font-mono text-slate-650">Rp{{ number_format($record['labor_fee'], 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-mono font-bold text-slate-800">Rp{{ number_format($record['labor_fee'], 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    <!-- Other Expenses -->
                    @if(!empty($record['other_expenses_fee']) && $record['other_expenses_fee'] > 0)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 font-semibold text-slate-800">🏷️ Other Fees: {{ $record['other_expenses_category'] ?? 'Other Expenses' }}</td>
                            <td class="p-3 font-mono text-slate-400">FEES-MISC</td>
                            <td class="p-3 text-center font-bold">1</td>
                            <td class="p-3 text-right font-mono text-slate-650">Rp{{ number_format($record['other_expenses_fee'], 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-mono font-bold text-slate-800">Rp{{ number_format($record['other_expenses_fee'], 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    @if((empty($record['parts']) || count($record['parts']) === 0) && (empty($record['labor_fee']) || $record['labor_fee'] == 0) && (empty($record['other_expenses_fee']) || $record['other_expenses_fee'] == 0))
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400 italic">No billed items for this record.</td>
                        </tr>
                    @endif

                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t border-slate-200 print-border">
                        <td colspan="4" class="p-3 text-right font-bold text-slate-500 uppercase tracking-wider text-[10px]">Grand Total Billing:</td>
                        <td class="p-3 text-right font-mono font-extrabold text-sm text-emerald-600">Rp{{ number_format($record['grand_total'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="grid grid-cols-2 gap-8 mt-12 pt-8 border-t border-slate-100 print-border">
            <div class="text-center space-y-12">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Customer Signature</span>
                <div class="border-b border-slate-350 mx-auto w-40 print-border"></div>
                <strong class="text-slate-700 text-xs block">{{ $record['customer_name'] ?? 'Customer' }}</strong>
            </div>
            <div class="text-center space-y-12">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Authorized Mechanic</span>
                <div class="border-b border-slate-350 mx-auto w-40 print-border"></div>
                <strong class="text-slate-700 text-xs block">{{ $record['mechanic_name'] ?? 'Mechanic' }}</strong>
            </div>
        </div>

    </div>

    <!-- Trigger browser print automatically -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
