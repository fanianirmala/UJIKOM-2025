<?php
namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransactionExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping, WithEvents
{
    protected $rows;
    protected $mergeMap = [];

    public function collection()
    {
        $transactions = Transaction::with(['user', 'customer', 'detailTransactions.product'])->get();
        $this->rows = [];
        $this->mergeMap = [];

        $rowIndex = 2;

        foreach ($transactions as $transaction) {
            $startRow = $rowIndex;

            foreach ($transaction->detailTransactions as $item) {
                $product = $item->product;

                $this->rows[] = (object)[
                    'id' => $transaction->id,
                    'user_name' => $transaction->user->name ?? '-',
                    'customer_name' => $transaction->customer->name ?? '-',
                    'point_used' => $transaction->point_used ?? 0,
                    'total_price' => $transaction->total_price ?? 0,
                    'created_at' => $transaction->created_at,
                    'change' => $transaction->change ?? 0,
                    'discount_price' => $transaction->discount_price ?? 0,
                    'total_payment' => $transaction->total_payment ?? 0,
                    'phone_number' => $transaction->customer->phone_number ?? '-',
                    'customer_points' => $transaction->customer->points ?? 0,
                    'product_name' => $product->product_name ?? '-',
                    'qty' => $item->qty ?? 0,
                    'subtotal' => ($product->price ?? 0) * ($item->qty ?? 0),
                ];

                $rowIndex++;
            }

            $endRow = $rowIndex - 1;

            if ($endRow > $startRow) {
                $this->mergeMap[] = [
                    'start' => $startRow,
                    'end' => $endRow,
                ];
            }
        }

        return collect($this->rows);
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Nama Petugas',
            'Nama Customer',
            'Point Digunakan',
            'Total Harga',
            'Tanggal Pembelian',
            'Total Kembalian',
            'Total Discount Point',
            'Total Bayar',
            'No HP Customer',
            'Point Customer',
            'Nama Produk',
            'Qty',
            'Subtotal'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->user_name,
            $row->customer_name,
            $row->point_used,
            'Rp ' . number_format($row->total_price, 0, ',', '.'),
            $row->created_at ? $row->created_at->format('d-m-Y') : '-',
            'Rp ' . number_format($row->change, 0, ',', '.'),
            'Rp ' . number_format($row->discount_price, 0, ',', '.'),
            'Rp ' . number_format($row->total_payment, 0, ',', '.'),
            $row->phone_number,
            $row->customer_points,
            $row->product_name,
            $row->qty,
            'Rp ' . number_format($row->subtotal, 0, ',', '.')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        // Heading row dengan latar belakang biru dan teks putih
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'], // Biru untuk header
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        
        $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        
        return [];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI');

                $highestColumn = $sheet->getHighestColumn();
                $sheet->mergeCells("A1:{$highestColumn}1");

                $sheet->getStyle("A1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                foreach ($this->mergeMap as $range) {
                    for ($col = 'A'; $col <= 'K'; $col++) {
                        $start = $range['start'] + 1;
                        $end = $range['end'] + 1;
                        $sheet->mergeCells("{$col}{$start}:{$col}{$end}");
                        $sheet->getStyle("{$col}{$start}")->getAlignment()->setVertical('center');
                    }
                }
            },
        ];
    }
}
