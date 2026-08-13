<?php

namespace App\Exports;

use App\Models\MaintenanceRecord;
use Carbon\Carbon;

class BranchMaintenanceExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'Job Code',
            'Created At (Date)',
            'License Plate',
            'Customer Name',
            'Customer Phone',
            'Repair Category',
            'Customer Status',
            'Make & Model',
            'Model',
            'Vehicle Color',
            'VIN / Frame No',
            'Engine No',
            'Controller Code',
            'Description',
            'Part SKU',
            'Part Name',
            'Units',
            'Part Qty Used',
            'Part Price at Use',
            'Service Name',
            'Service Labor Fee',
            'Other Expense SKU',
            'Other Expense Name',
            'Other Expense Fee',
            'Mechanic Name',
            'Start Time (Date)',
            'Start Time (Time)',
            'End Time (Time)',
        ];
    }

    public function query()
    {
        $builder = MaintenanceRecord::with(['branch', 'mechanic', 'vehicle.customer', 'parts.inventory']);

        if (!empty($this->filters['branch_id'])) {
            $builder->where('branch_id', $this->filters['branch_id']);
        }

        if (!empty($this->filters['status'])) {
            $builder->where('status', strtolower($this->filters['status']));
        }

        if (!empty($this->filters['search'])) {
            $search = strtolower(trim($this->filters['search']));
            $builder->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('job_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('license_plate', 'like', "%{$search}%")
                        ->orWhere('vin', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        if (!empty($this->filters['date_from'])) {
            $builder->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $builder->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $builder->orderBy('created_at', 'desc')->cursor();
    }

    public function generateRows(): array
    {
        $records = $this->query();
        $exportRows = [];

        foreach ($records as $r) {
            // Eager collections
            $spareParts = $r->parts ?? collect();

            // Service Options list (virtualized from service_sku, service_name, labor_fee)
            $serviceOptions = collect();
            if (!empty($r->service_name)) {
                $splitNames = array_map('trim', explode(',', $r->service_name));
                $splitServiceSkus = !empty($r->service_sku) ? array_map('trim', explode(',', $r->service_sku)) : [];
                foreach ($splitNames as $idx => $sName) {
                    if (!empty($sName)) {
                        $sku = $splitServiceSkus[$idx] ?? (!empty($r->service_sku) ? $r->service_sku : ('SVC-JOB-' . sprintf('%02d', $idx + 1)));
                        $serviceOptions->push((object)[
                            'service_sku' => $sku,
                            'service_name' => $sName,
                            'labor_fee' => (float)($r->labor_fee ?? 0),
                        ]);
                    }
                }
            } elseif (!empty($r->service_sku) || (float)$r->labor_fee > 0) {
                $serviceOptions->push((object)[
                    'service_sku' => !empty($r->service_sku) ? $r->service_sku : 'SVC-JOB-01',
                    'service_name' => 'Labor Service',
                    'labor_fee' => (float)($r->labor_fee ?? 0),
                ]);
            }

            // Other Services list (virtualized from other_expenses_category, other_expenses_fee)
            $otherServices = collect();
            if (!empty($r->other_expenses_category)) {
                $splitCategories = array_map('trim', explode(',', $r->other_expenses_category));
                $splitOtherSkus = !empty($r->other_expenses_sku) ? array_map('trim', explode(',', $r->other_expenses_sku)) : [];
                foreach ($splitCategories as $idx => $oCat) {
                    if (!empty($oCat)) {
                        $sku = $splitOtherSkus[$idx] ?? (!empty($r->other_expenses_sku) ? $r->other_expenses_sku : ('JS' . sprintf('%03d', $idx + 1)));
                        $otherServices->push((object)[
                            'service_sku' => $sku,
                            'category' => $oCat,
                            'fee' => (float)($r->other_expenses_fee ?? 0),
                        ]);
                    }
                }
            } elseif ((float)$r->other_expenses_fee > 0) {
                $otherServices->push((object)[
                    'service_sku' => !empty($r->other_expenses_sku) ? $r->other_expenses_sku : 'JS001',
                    'category' => 'Other Fee',
                    'fee' => (float)($r->other_expenses_fee ?? 0),
                ]);
            }

            // Row Explosion Logic: 1-to-1 index mapping per record
            $maxRows = max($spareParts->count(), $serviceOptions->count(), $otherServices->count(), 1);

            for ($i = 0; $i < $maxRows; $i++) {
                $partItem = $spareParts->get($i);
                $serviceItem = $serviceOptions->get($i);
                $otherItem = $otherServices->get($i);

                $createdAtDate = $r->created_at ? Carbon::parse($r->created_at)->format('Y-m-d') : '';
                $startDate = $r->start_time ? Carbon::parse($r->start_time)->format('Y-m-d') : '';
                $startTime = $r->start_time ? Carbon::parse($r->start_time)->format('H:i:s') : '';
                $endTime = $r->end_time ? Carbon::parse($r->end_time)->format('H:i:s') : '';

                $makeAndModel = trim(($r->vehicle->make ?? '') . ' ' . ($r->vehicle->model ?? ''));
                if (empty($makeAndModel)) {
                    $makeAndModel = 'N/A';
                }

                $rawCustStatus = $r->vehicle->customer->customer_status ?? 'Active';
                $rowCustStatus = $rawCustStatus;
                if (strtolower(trim($rawCustStatus)) === 'gomolis') {
                    if ($partItem) {
                        $isPartCharged = isset($partItem->is_charged) 
                            ? (bool)$partItem->is_charged 
                            : ((float)($partItem->price_at_use ?? 0) > 0);
                        $rowCustStatus = $isPartCharged ? 'Gomolis-B' : 'Gomolis';
                    } else {
                        $rowCustStatus = 'Gomolis';
                    }
                }
                if ($partItem) {
                    $rowRepairCategory = !empty($partItem->is_claimed) ? 'Claim' : 'Stock';
                } else {
                    $rowRepairCategory = 'Repair';
                }

                $partPriceAtUse = 0.0;
                if ($partItem) {
                    $partPriceAtUse = !empty($partItem->is_claimed) ? 0.0 : (float)($partItem->price_at_use ?? 0);
                }

                $exportRows[] = [
                    $r->job_id ?? ('JOB-' . $r->id),
                    $createdAtDate,
                    $r->vehicle->license_plate ?? 'N/A',
                    $r->vehicle->customer->name ?? 'Unbound Customer',
                    $r->vehicle->customer->phone ?? 'N/A',
                    $rowRepairCategory,
                    $rowCustStatus,
                    $makeAndModel,
                    $r->vehicle->model ?? 'N/A',
                    $r->vehicle->color ?? 'N/A',
                    $r->vehicle->vin ?? $r->frame_number ?? 'N/A',
                    $r->vehicle->engine_number ?? 'N/A',
                    $r->vehicle->controller_number ?? 'N/A',
                    $r->description ?? '',
                    $partItem && $partItem->inventory ? $partItem->inventory->sku : '',
                    $partItem ? ($partItem->inventory->part_name ?? 'Spare Part') : '',
                    $partItem && $partItem->inventory ? ($partItem->inventory->unit ?? 'pcs') : '',
                    $partItem ? (int)($partItem->quantity_used ?? 0) : '',
                    $partItem ? $partPriceAtUse : '',
                    $serviceItem ? ($serviceItem->service_name ?? '') : '',
                    $serviceItem ? (float)($serviceItem->labor_fee ?? 0) : '',
                    $otherItem ? ($otherItem->service_sku ?? '') : '',
                    $otherItem ? ($otherItem->category ?? '') : '',
                    $otherItem ? (float)($otherItem->fee ?? 0) : '',
                    $r->mechanic->display_name ?? $r->mechanic->username ?? 'Unassigned',
                    $startDate,
                    $startTime,
                    $endTime,
                ];
            }
        }

        return $exportRows;
    }

    public function getJsonData(): array
    {
        $headings = $this->headings();
        $rows = $this->generateRows();
        $formattedData = [];

        foreach ($rows as $row) {
            $assoc = [];
            foreach ($headings as $index => $heading) {
                $assoc[$heading] = $row[$index] ?? '';
            }
            $formattedData[] = $assoc;
        }

        return $formattedData;
    }

    public function downloadCsv()
    {
        $filename = 'branch_maintenance_records_' . now()->format('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, $this->headings());

        foreach ($this->generateRows() as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
