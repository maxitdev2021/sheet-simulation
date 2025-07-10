<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Symfony\Component\HttpFoundation\StreamedResponse;


class SimulationController extends Controller
{
    public function index()
    {
        return Inertia::render('Simulation');
    }
   public function export(Request $request)
    {
        $data = $request->input('data'); // [["Month", "プランA", "プランB"], ["Jan", 1000, 1200], ...]

        if (!$data || count($data) < 2) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Write data
        foreach ($data as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $cell = chr(65 + $colIndex) . ($rowIndex + 1);
                $sheet->setCellValue($cell, $value);
            }
        }

        $columnCount = count($data[0]) - 1;
        $rowCount = count($data) - 1;

        // Categories (X-axis)
        $categories = [
            new DataSeriesValues('String', "Sheet1!A2:A" . ($rowCount + 1), null, $rowCount)
        ];

        // Values (Y-axis, supports multiple series)
        $values = [];
        for ($i = 0; $i < $columnCount; $i++) {
            $colLetter = chr(66 + $i); // B, C, ...
            $values[] = new DataSeriesValues(
                'Number',
                "Sheet1!{$colLetter}2:{$colLetter}" . ($rowCount + 1),
                null,
                $rowCount
            );
        }

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, $columnCount - 1),
            [],
            $categories,
            $values
        );

        $plotArea = new PlotArea(null, [$series]);
        $chart = new Chart(
            'simulation_chart',
            new Title('データシミュレーション'),
            new Legend(Legend::POSITION_BOTTOM, null, false),
            $plotArea
        );

        $chart->setTopLeftPosition('E2');
        $chart->setBottomRightPosition('M20');

        $sheet->addChart($chart);

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'simulation_' . now()->format('Ymd_His') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

        return $response;
    }
}
