<?php

namespace App\Services;

class SimplePdfBuilder
{
    public function generateInvoicePdf(array $record): string
    {
        $jobId = $record['job_id'] ?? ('JOB-' . ($record['id'] ?? '000'));
        $dateStr = !empty($record['created_at']) ? date('d/m/Y H:i', strtotime($record['created_at'])) : date('d/m/Y H:i');
        $custName = $record['customer_name'] ?? 'N/A';
        $custPhone = $record['customer_phone'] ?? 'N/A';
        $custStatus = $record['customer_status'] ?? 'Retail';
        $custAddress = $record['customer_address'] ?? 'N/A';

        $plate = $record['license_plate'] ?? 'N/A';
        $make = $record['make'] ?? '';
        $vehType = trim($make . ' ' . ($record['vehicle_type'] ?? ''));
        if (empty($vehType)) $vehType = 'EV';
        $kmStr = !empty($record['km_reached']) ? number_format($record['km_reached']) . ' KM' : '0 KM';
        $vin = $record['vin'] ?? $record['frame_number'] ?? 'N/A';

        $branchName = $record['branch_name'] ?? 'Main Branch';
        $mechanicName = $record['mechanic_name'] ?? 'Assigned Specialist';
        $cashierName = $record['cashier_name'] ?? 'Authorized Cashier';

        // Calculate Totals
        $parts = $record['parts'] ?? [];
        $partsSum = 0;
        foreach ($parts as $p) {
            $partsSum += (float)($p['price_at_use'] ?? 0) * (int)($p['quantity_used'] ?? 1);
        }

        $laborFee = floatval($record['labor_fee'] ?? 0);
        $otherFee = floatval($record['other_expenses_fee'] ?? 0);
        $laborSum = $laborFee + $otherFee;
        $grandTotal = $record['grand_total'] ?? ($partsSum + $laborSum);

        $s = "";

        // Page container: A4 Portrait (595 x 842 pt)
        // Outer Container Box
        $s .= "0.95 0.96 0.98 rg 0 0 595 842 re f\n"; // Page background
        $s .= "1 1 1 rg 20 20 555 802 re f\n"; // White card container
        $s .= "0.85 0.88 0.92 RG 1 w 20 20 555 802 re S\n"; // Outer border

        // 1. Header Row
        $s .= "BT /F2 14 Tf 0.06 0.09 0.16 rg 35 798 Td (SGPM SERVICE CENTER) Tj ET\n";
        
        // Official Invoice Badge
        $s .= "0.85 0.92 1.0 rg 205 795 90 14 re f\n";
        $s .= "0.7 0.8 0.95 RG 0.5 w 205 795 90 14 re S\n";
        $s .= "BT /F2 7.5 Tf 0.11 0.25 0.68 rg 212 799 Td (OFFICIAL INVOICE) Tj ET\n";

        // Branch Subtitle
        $s .= "BT /F1 8.5 Tf 0.4 0.45 0.55 rg 35 784 Td (Outlet: " . $this->pdfEscape($branchName) . ") Tj ET\n";

        // Invoice No & Date Right Aligned
        $s .= "BT /F1 8.5 Tf 0.5 0.5 0.5 rg 420 798 Td (Invoice No:) Tj ET\n";
        $s .= "BT /F2 9.5 Tf 0.06 0.09 0.16 rg 472 798 Td (" . $this->pdfEscape($jobId) . ") Tj ET\n";
        $s .= "BT /F1 8.5 Tf 0.5 0.5 0.5 rg 420 784 Td (Date:) Tj ET\n";
        $s .= "BT /F1 8.5 Tf 0.2 0.2 0.2 rg 472 784 Td (" . $this->pdfEscape($dateStr) . ") Tj ET\n";

        // Divider Line
        $s .= "0.85 0.88 0.92 RG 0.75 w 35 774 525 0 re S\n";

        // 2. 2-Column Info Grid: Customer & Vehicle Information
        // Left Box: Customer Details
        $s .= "0.97 0.98 0.99 rg 35 684 255 80 re f\n";
        $s .= "0.85 0.88 0.92 RG 0.75 w 35 684 255 80 re S\n";
        $s .= "BT /F2 8 Tf 0.4 0.45 0.55 rg 42 752 Td (CUSTOMER DETAILS) Tj ET\n";
        $s .= "0.85 0.88 0.92 RG 0.5 w 42 746 241 0 re S\n";

        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 42 732 Td (Name:) Tj ET\n";
        $s .= "BT /F2 8 Tf 0.06 0.09 0.16 rg 75 732 Td (" . $this->pdfEscape(mb_strimwidth($custName, 0, 26, '...')) . ") Tj ET\n";
        
        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 42 718 Td (Phone:) Tj ET\n";
        $s .= "BT /F1 8 Tf 0.2 0.2 0.2 rg 75 718 Td (" . $this->pdfEscape($custPhone) . ") Tj ET\n";

        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 42 704 Td (Status:) Tj ET\n";
        $s .= "BT /F2 8 Tf 0.2 0.2 0.2 rg 75 704 Td (" . $this->pdfEscape($custStatus) . ") Tj ET\n";

        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 42 690 Td (Address:) Tj ET\n";
        $s .= "BT /F1 8 Tf 0.2 0.2 0.2 rg 80 690 Td (" . $this->pdfEscape(mb_strimwidth($custAddress, 0, 32, '...')) . ") Tj ET\n";

        // Right Box: Vehicle Specifications
        $s .= "0.97 0.98 0.99 rg 305 684 255 80 re f\n";
        $s .= "0.85 0.88 0.92 RG 0.75 w 305 684 255 80 re S\n";
        $s .= "BT /F2 8 Tf 0.4 0.45 0.55 rg 312 752 Td (VEHICLE SPECIFICATIONS) Tj ET\n";
        $s .= "0.85 0.88 0.92 RG 0.5 w 312 746 241 0 re S\n";

        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 312 732 Td (Plate No:) Tj ET\n";
        $s .= "BT /F2 8.5 Tf 0.14 0.38 0.92 rg 365 732 Td (" . $this->pdfEscape($plate) . ") Tj ET\n";

        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 312 718 Td (Type/Model:) Tj ET\n";
        $s .= "BT /F2 8 Tf 0.2 0.2 0.2 rg 365 718 Td (" . $this->pdfEscape(mb_strimwidth($vehType, 0, 24, '...')) . ") Tj ET\n";

        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 312 704 Td (Odometer:) Tj ET\n";
        $s .= "BT /F1 8 Tf 0.2 0.2 0.2 rg 365 704 Td (" . $this->pdfEscape($kmStr) . ") Tj ET\n";

        $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 312 690 Td (VIN/Frame:) Tj ET\n";
        $s .= "BT /F1 8 Tf 0.2 0.2 0.2 rg 365 690 Td (" . $this->pdfEscape(mb_strimwidth($vin, 0, 24, '...')) . ") Tj ET\n";

        // 3. Items & Services Table
        $y = 665;

        // Table Header
        $s .= "0.94 0.96 0.98 rg 35 " . ($y - 18) . " 525 18 re f\n";
        $s .= "0.85 0.88 0.92 RG 0.75 w 35 " . ($y - 18) . " 525 18 re S\n";

        $s .= "BT /F2 7.5 Tf 0.3 0.35 0.45 rg\n";
        $s .= "42 " . ($y - 12) . " Td (DESCRIPTION / ITEM NAME) Tj\n";
        $s .= "275 " . ($y - 12) . " Td (SKU CODE) Tj\n";
        $s .= "370 " . ($y - 12) . " Td (QTY) Tj\n";
        $s .= "420 " . ($y - 12) . " Td (UNIT PRICE) Tj\n";
        $s .= "505 " . ($y - 12) . " Td (TOTAL (IDR)) Tj\n";
        $s .= "ET\n";
        $y -= 18;

        // Render Spare Parts Rows
        if (count($parts) > 0) {
            foreach ($parts as $p) {
                $pName = mb_strimwidth($p['part_name'] ?? 'Sparepart', 0, 42, '...');
                $sku = $p['sku'] ?? '-';
                $qty = (int)($p['quantity_used'] ?? 1);
                $price = (float)($p['price_at_use'] ?? 0);
                $total = $qty * $price;

                $s .= "0.93 0.94 0.96 RG 0.4 w 35 " . ($y - 16) . " 525 16 re S\n";
                $s .= "BT /F1 8 Tf 0.1 0.1 0.1 rg 42 " . ($y - 11) . " Td (" . $this->pdfEscape($pName) . ") Tj ET\n";
                $s .= "BT /F1 7.5 Tf 0.5 0.5 0.5 rg 275 " . ($y - 11) . " Td (" . $this->pdfEscape($sku) . ") Tj ET\n";
                $s .= "BT /F2 8 Tf 0.1 0.1 0.1 rg 375 " . ($y - 11) . " Td (" . $qty . ") Tj ET\n";
                $s .= "BT /F1 8 Tf 0.3 0.3 0.3 rg 420 " . ($y - 11) . " Td (Rp " . number_format($price, 0, ',', '.') . ") Tj ET\n";
                $s .= "BT /F2 8 Tf 0.05 0.05 0.05 rg 505 " . ($y - 11) . " Td (Rp " . number_format($total, 0, ',', '.') . ") Tj ET\n";

                $y -= 16;
            }
        }

        // Render Service Fees Rows
        if ($laborFee > 0) {
            $sName = !empty($record['service_name']) ? mb_strimwidth($record['service_name'], 0, 42, '...') : 'Labor / Work Service Fee';
            $s .= "0.93 0.94 0.96 RG 0.4 w 35 " . ($y - 16) . " 525 16 re S\n";
            $s .= "BT /F1 8 Tf 0.1 0.1 0.1 rg 42 " . ($y - 11) . " Td (" . $this->pdfEscape($sName) . ") Tj ET\n";
            $s .= "BT /F1 7.5 Tf 0.5 0.5 0.5 rg 275 " . ($y - 11) . " Td (SVC-LABOR) Tj ET\n";
            $s .= "BT /F2 8 Tf 0.1 0.1 0.1 rg 375 " . ($y - 11) . " Td (1) Tj ET\n";
            $s .= "BT /F1 8 Tf 0.3 0.3 0.3 rg 420 " . ($y - 11) . " Td (Rp " . number_format($laborFee, 0, ',', '.') . ") Tj ET\n";
            $s .= "BT /F2 8 Tf 0.05 0.05 0.05 rg 505 " . ($y - 11) . " Td (Rp " . number_format($laborFee, 0, ',', '.') . ") Tj ET\n";
            $y -= 16;
        }

        if ($otherFee > 0) {
            $oCat = 'Additional Service: ' . (!empty($record['other_expenses_category']) ? mb_strimwidth($record['other_expenses_category'], 0, 32, '...') : 'Other Fee');
            $oSku = $record['other_expenses_sku'] ?? $record['service_sku'] ?? 'SVC-MISC';
            $s .= "0.93 0.94 0.96 RG 0.4 w 35 " . ($y - 16) . " 525 16 re S\n";
            $s .= "BT /F1 8 Tf 0.1 0.1 0.1 rg 42 " . ($y - 11) . " Td (" . $this->pdfEscape($oCat) . ") Tj ET\n";
            $s .= "BT /F1 7.5 Tf 0.5 0.5 0.5 rg 275 " . ($y - 11) . " Td (" . $this->pdfEscape($oSku) . ") Tj ET\n";
            $s .= "BT /F2 8 Tf 0.1 0.1 0.1 rg 375 " . ($y - 11) . " Td (1) Tj ET\n";
            $s .= "BT /F1 8 Tf 0.3 0.3 0.3 rg 420 " . ($y - 11) . " Td (Rp " . number_format($otherFee, 0, ',', '.') . ") Tj ET\n";
            $s .= "BT /F2 8 Tf 0.05 0.05 0.05 rg 505 " . ($y - 11) . " Td (Rp " . number_format($otherFee, 0, ',', '.') . ") Tj ET\n";
            $y -= 16;
        }

        if (count($parts) === 0 && $laborFee == 0 && $otherFee == 0) {
            $s .= "0.93 0.94 0.96 RG 0.4 w 35 " . ($y - 16) . " 525 16 re S\n";
            $s .= "BT /F1 8 Tf 0.5 0.5 0.5 rg 42 " . ($y - 11) . " Td (No billable items or services recorded.) Tj ET\n";
            $y -= 16;
        }

        // Table Footer: Grand Total Row
        $s .= "0.97 0.98 0.99 rg 35 " . ($y - 20) . " 525 20 re f\n";
        $s .= "0.85 0.88 0.92 RG 0.75 w 35 " . ($y - 20) . " 525 20 re S\n";
        $s .= "BT /F2 8 Tf 0.3 0.35 0.45 rg 320 " . ($y - 13) . " Td (GRAND TOTAL AMOUNT:) Tj ET\n";
        $s .= "BT /F2 10.5 Tf 0.01 0.47 0.34 rg 460 " . ($y - 13) . " Td (Rp " . number_format($grandTotal, 0, ',', '.') . ") Tj ET\n";

        // 4. Footer Signatures & Term Notes
        $y -= 50;
        $s .= "0.85 0.88 0.92 RG 0.75 w 35 " . $y . " 525 0 re S\n";

        // Left Note
        $s .= "BT /F2 7.5 Tf 0.3 0.35 0.45 rg 35 " . ($y - 14) . " Td (Thank you for choosing SGPM Service Center!) Tj ET\n";
        $s .= "BT /F1 7.5 Tf 0.4 0.4 0.4 rg 35 " . ($y - 26) . " Td (Mechanic: " . $this->pdfEscape($mechanicName) . ") Tj ET\n";

        // Right Signatures: Customer Signature & Authorized Cashier
        // Customer Sign Line
        $s .= "0.8 0.83 0.88 RG 0.5 w 360 " . ($y - 45) . " 85 0 re S\n";
        $s .= "BT /F2 7 Tf 0.5 0.5 0.5 rg 365 " . ($y - 14) . " Td (CUSTOMER SIGNATURE) Tj ET\n";
        $s .= "BT /F2 7.5 Tf 0.1 0.1 0.1 rg 360 " . ($y - 56) . " Td (" . $this->pdfEscape(mb_strimwidth($custName, 0, 18, '...')) . ") Tj ET\n";

        // Cashier Sign Line
        $s .= "0.8 0.83 0.88 RG 0.5 w 470 " . ($y - 45) . " 85 0 re S\n";
        $s .= "BT /F2 7 Tf 0.5 0.5 0.5 rg 475 " . ($y - 14) . " Td (AUTHORIZED CASHIER) Tj ET\n";
        $s .= "BT /F2 7.5 Tf 0.1 0.1 0.1 rg 470 " . ($y - 56) . " Td (" . $this->pdfEscape(mb_strimwidth($cashierName, 0, 18, '...')) . ") Tj ET\n";

        return $this->buildPdfFromStream($s);
    }

    protected function pdfEscape(string $str): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $str);
    }

    protected function buildPdfFromStream(string $streamContent): string
    {
        $obj1 = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $obj2 = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $obj3 = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>\nendobj\n";
        $obj4 = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $obj5 = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n";
        $obj6 = "6 0 obj\n<< /Length " . strlen($streamContent) . " >>\nstream\n" . $streamContent . "endstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $o1 = strlen($pdf); $pdf .= $obj1;
        $o2 = strlen($pdf); $pdf .= $obj2;
        $o3 = strlen($pdf); $pdf .= $obj3;
        $o4 = strlen($pdf); $pdf .= $obj4;
        $o5 = strlen($pdf); $pdf .= $obj5;
        $o6 = strlen($pdf); $pdf .= $obj6;

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 7\n";
        $pdf .= "0000000000 65535 f \n";
        $pdf .= sprintf("%010d 0000 n \n", $o1);
        $pdf .= sprintf("%010d 0000 n \n", $o2);
        $pdf .= sprintf("%010d 0000 n \n", $o3);
        $pdf .= sprintf("%010d 0000 n \n", $o4);
        $pdf .= sprintf("%010d 0000 n \n", $o5);
        $pdf .= sprintf("%010d 0000 n \n", $o6);
        $pdf .= "trailer\n<< /Size 7 /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";

        return $pdf;
    }
}
