<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\ContractorShift;
use App\Models\Meeting;
use App\Models\MeetingRegistration;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitAttendance;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Filtered listings by date, origin and host, and their Excel export (AP006).
 *
 * Every report is a set of column headers, a filtered query and a function turning one record into a row,
 * so the on-screen table and the spreadsheet always contain the same data.
 */
class ReportController extends Controller
{
    public const array TYPES = [
        'bisitariak' => 'Bisitariak (sarrerak)',
        'bisitak' => 'Bisita eskaerak',
        'bilerak' => 'Bileretako partaideak',
        'kanpo-langileak' => 'Kanpo langileak',
    ];

    private const int PER_PAGE = 50;

    public function index(ReportRequest $request): View
    {
        $report = $this->report($request);
        $records = $report['query']->paginate(self::PER_PAGE)->withQueryString();

        return view('reports.index', [
            'types' => self::TYPES,
            'type' => $request->type(),
            'from' => $request->from(),
            'until' => $request->until(),
            'columns' => $report['columns'],
            'records' => $records,
            'rows' => $records->getCollection()->map($report['row']),
            'hosts' => User::orderBy('name')->pluck('name', 'id'),
            'filters' => Arr::except($request->query(), 'page'),
        ]);
    }

    public function export(ReportRequest $request): StreamedResponse
    {
        $report = $this->report($request);

        $spreadsheet = new Spreadsheet;
        // Names come from public forms: text such as "=HYPERLINK(...)" must stay text, never become a formula.
        $spreadsheet->setValueBinder((new StringValueBinder)->setNumericConversion(false));

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(Str::limit(self::TYPES[$request->type()], 31, ''));
        $sheet->fromArray($report['columns']);
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $lastColumn = Coordinate::stringFromColumnIndex(count($report['columns']));
        $rowNumber = 2;

        foreach ($report['query']->lazy(500) as $record) {
            $cells = $report['row']($record);
            $imagePath = isset($report['image']) ? $report['image']($record) : null;

            if ($imagePath) {
                $cells[array_key_last($cells)] = null;

                $drawing = new Drawing;
                $drawing->setPath($imagePath);
                $drawing->setHeight(36);
                $drawing->setCoordinates("{$lastColumn}{$rowNumber}");
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($rowNumber)->setRowHeight(30);
            }

            $sheet->fromArray($cells, null, "A{$rowNumber}");
            $rowNumber++;
        }

        foreach (range(1, count($report['columns'])) as $column) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setAutoSize(true);
        }

        $fileName = sprintf('%s-%s-%s.xlsx', $request->type(), $request->from()->format('Y-m-d'), $request->until()->format('Y-m-d'));

        return response()->streamDownload(
            function () use ($spreadsheet): void {
                (new Xlsx($spreadsheet))->save('php://output');
            },
            $fileName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    /**
     * @return array{columns: list<string>, query: Builder, row: Closure, image?: Closure}
     */
    private function report(ReportRequest $request): array
    {
        $range = [$request->from()->startOfDay(), $request->until()->endOfDay()];
        $origin = $request->validated('jatorria');
        $hostId = $request->validated('ostalaria');

        return match ($request->type()) {
            'bisitariak' => $this->attendanceReport($range, $origin, $hostId),
            'bisitak' => $this->visitReport($range, $origin, $hostId),
            'bilerak' => $this->meetingReport($range, $origin),
            'kanpo-langileak' => $this->contractorReport($range, $origin),
        };
    }

    /**
     * Who actually came in: one row per check-in.
     *
     * @param  array{CarbonInterface, CarbonInterface}  $range
     * @return array{columns: list<string>, query: Builder, row: Closure}
     */
    private function attendanceReport(array $range, ?string $origin, ?int $hostId): array
    {
        return [
            'columns' => ['Data', 'Sarrera', 'Irteera', 'Izena', 'Abizena(k)', 'NAN', 'Jatorria', 'Nori', 'Mota / arrazoia', 'Txartela', 'Emaila', 'Telefonoa'],
            'query' => VisitAttendance::query()
                ->with(['visitor', 'visit.host', 'visit.types'])
                ->whereBetween('checked_in_at', $range)
                ->when($origin, fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->whereHas('visit', fn (Builder $query) => $query->where('origin', 'like', "%{$origin}%"))
                    ->orWhereHas('visitor', fn (Builder $query) => $query->withTrashed()->where('origin', 'like', "%{$origin}%"))))
                ->when($hostId, fn (Builder $query) => $query->whereHas('visit', fn (Builder $query) => $query->where('host_id', $hostId)))
                ->orderBy('checked_in_at'),
            'row' => fn (VisitAttendance $attendance) => [
                $attendance->checked_in_at->format('Y/m/d'),
                $attendance->checked_in_at->format('H:i'),
                $attendance->checked_out_at?->format('H:i'),
                $attendance->visitor->first_name,
                $attendance->visitor->last_name,
                $attendance->visitor->document_number,
                $attendance->visit->origin ?? $attendance->visitor->origin,
                $attendance->visit->host?->name,
                $attendance->visit->typeNames('eu') ?: $attendance->visit->notes,
                $attendance->badge_number,
                $attendance->visitor->email,
                $attendance->visitor->phone,
            ],
        ];
    }

    /**
     * Requests and visits by their date, whatever their status.
     *
     * @param  array{CarbonInterface, CarbonInterface}  $range
     * @return array{columns: list<string>, query: Builder, row: Closure}
     */
    private function visitReport(array $range, ?string $origin, ?int $hostId): array
    {
        return [
            'columns' => ['Data', 'Jatorria', 'Egoera', 'Mota', 'Modua', 'Pertsonak', 'Nori', 'Hizkuntza', 'Bisitariak', 'Eskatua', 'Erabakia'],
            'query' => Visit::query()
                ->with(['host', 'reviewer', 'types', 'visitors'])
                ->whereBetween('visit_date', $range)
                ->when($origin, fn (Builder $query) => $query->where('origin', 'like', "%{$origin}%"))
                ->when($hostId, fn (Builder $query) => $query->where('host_id', $hostId))
                ->orderBy('visit_date'),
            'row' => fn (Visit $visit) => [
                $visit->visit_date->format('Y/m/d'),
                $visit->origin,
                $visit->status->label(),
                $visit->typeNames('eu'),
                $visit->is_group ? 'Taldea' : 'Bakarka',
                $visit->is_group ? $visit->group_size : $visit->visitors->count(),
                $visit->host?->name,
                strtoupper($visit->locale),
                $visit->visitors->map->full_name->join(', '),
                $visit->created_at->format('Y/m/d H:i'),
                $visit->reviewed_at ? "{$visit->reviewed_at->format('Y/m/d H:i')} · {$visit->reviewer?->name}" : null,
            ],
        ];
    }

    /**
     * One row per person signed up for a meeting held in the range.
     *
     * @param  array{CarbonInterface, CarbonInterface}  $range
     * @return array{columns: list<string>, query: Builder, row: Closure}
     */
    private function meetingReport(array $range, ?string $origin): array
    {
        return [
            'columns' => ['Data', 'Ordua', 'Bilera', 'Lekua', 'Izena', 'Abizena(k)', 'Jatorria', 'Emaila', 'Izena emanda'],
            'query' => MeetingRegistration::query()
                ->with(['meeting', 'visitor'])
                ->whereHas('meeting', fn (Builder $query) => $query->whereBetween('date', $range))
                ->when($origin, fn (Builder $query) => $query->whereHas('visitor', fn (Builder $query) => $query
                    ->withTrashed()
                    ->where('origin', 'like', "%{$origin}%")))
                ->orderBy(Meeting::select('date')->whereColumn('meetings.id', 'meeting_visitor.meeting_id'))
                ->orderBy('meeting_visitor.id'),
            'row' => fn (MeetingRegistration $registration) => [
                $registration->meeting->date->format('Y/m/d'),
                $registration->meeting->starts_at,
                $registration->meeting->title,
                $registration->meeting->location,
                $registration->visitor->first_name,
                $registration->visitor->last_name,
                $registration->visitor->origin,
                $registration->visitor->email,
                $registration->created_at?->format('Y/m/d H:i'),
            ],
        ];
    }

    /**
     * Contractor shifts; the export embeds each signature as an image in the last column.
     *
     * @param  array{CarbonInterface, CarbonInterface}  $range
     * @return array{columns: list<string>, query: Builder, row: Closure, image: Closure}
     */
    private function contractorReport(array $range, ?string $company): array
    {
        return [
            'columns' => ['Data', 'Sarrera', 'Irteera', 'Izena', 'Abizena(k)', 'Enpresa', 'Telefonoa', 'Giltza', 'Sinadura'],
            'query' => ContractorShift::query()
                ->with('contractor')
                ->whereBetween('checked_in_at', $range)
                ->when($company, fn (Builder $query) => $query->whereHas('contractor', fn (Builder $query) => $query
                    ->where('company', 'like', "%{$company}%")))
                ->orderBy('checked_in_at'),
            'row' => fn (ContractorShift $shift) => [
                $shift->checked_in_at->format('Y/m/d'),
                $shift->checked_in_at->format('H:i'),
                $shift->checked_out_at?->format('H:i'),
                $shift->contractor->first_name,
                $shift->contractor->last_name,
                $shift->contractor->company,
                $shift->contractor->phone,
                match (true) {
                    ! $shift->needs_key => 'Ez',
                    $shift->key_returned === true => 'Itzulita',
                    $shift->checked_out_at !== null => 'Itzuli gabe',
                    default => 'Lanean, giltzarekin',
                },
                $shift->signature_path ? 'Bai' : 'Ez',
            ],
            'image' => fn (ContractorShift $shift) => $shift->signature_path && Storage::disk('local')->exists($shift->signature_path)
                ? Storage::disk('local')->path($shift->signature_path)
                : null,
        ];
    }
}
