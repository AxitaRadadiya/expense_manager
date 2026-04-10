<?php

namespace App\Exports\Reports;

use App\Models\Credit;
use App\Models\Expense;
use App\Models\Transfer;
use App\Models\User;
use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ReportExport implements FromArray, ShouldAutoSize, WithEvents, WithTitle
{
	protected ?User $authUser;
	protected array $filters;
	protected ReportService $reportService;
	protected array $projects;
	protected array $users;
	protected $projectSummary;
	protected $userSummary;
	protected $timeline;
	protected array $totals;

	public function __construct(
		?User $authUser,
		array $filters,
		ReportService $reportService
	) {
		$this->authUser = $authUser;
		$this->filters = $filters;
		$this->reportService = $reportService;
	}

	public function array(): array
	{
		$this->loadData();
		$rows = [];

		$rows[] = $this->getTitleRow();
		$rows[] = $this->getSummaryTilesRow();
		$rows[] = [];

		$rows = array_merge($rows, $this->getProjectSummaryRows());
		$rows[] = [];

		$rows = array_merge($rows, $this->getUserSummaryRows());
		$rows[] = [];

		$rows = array_merge($rows, $this->getTransactionRows());

		return $rows;
	}

	protected function loadData(): void
	{
		$this->projects = $this->reportService->getVisibleProjectsForUser($this->authUser)->keyBy('id')->toArray();
		$this->users = $this->reportService->getVisibleUsersForUser($this->authUser)->keyBy('id')->toArray();
		$this->projectSummary = $this->reportService->getProjectWiseSummary($this->authUser, $this->filters);
		$this->userSummary = $this->reportService->getUserWiseSummary($this->authUser, $this->filters);
		$this->timeline = $this->reportService->getTransactionTimeline($this->authUser, $this->filters);
		$this->totals = $this->reportService->getTotals($this->projectSummary, $this->userSummary);
	}

	protected function getTitleRow(): array
	{
		return ['EXPENSE MANAGER'];
	}

	protected function getSummaryTilesRow(): array
	{
		return [
			'TOTAL CREDIT', (float) ($this->totals['project_total_credit'] ?? 0),
			'TOTAL EXPENSE', (float) ($this->totals['project_total_expense'] ?? 0),
			'NET BALANCE', (float) (($this->totals['project_net_amount'] ?? 0) + ($this->totals['user_net_amount'] ?? 0)),
			'TOTAL TRANSFERS', 0,
			'TRANSACTIONS', count($this->timeline),
		];
	}

	protected function getProjectSummaryRows(): array
	{
		$rows = [];
		$rows[] = ['PROJECT SUMMARY'];
		$rows[] = ['Project', 'Credit (₹)', 'Expense (₹)', 'Transfers (₹)', 'Net (₹)', 'Status'];

		foreach ($this->projectSummary as $item) {
			$rows[] = [
				$item->project_name,
				(float) $item->total_credit,
				(float) $item->total_expense,
				0,
				(float) $item->net_amount,
				'',
			];
		}

		return $rows;
	}

	protected function getUserSummaryRows(): array
	{
		$rows = [];
		$rows[] = ['USER SUMMARY'];
		$rows[] = ['User', 'Credit (₹)', 'Expense (₹)', 'Transferred (₹)', 'Net (₹)', 'TXNS'];

		foreach ($this->userSummary as $item) {
			$rows[] = [
				$item->user_name,
				(float) $item->total_credit,
				(float) $item->total_expense,
				0,
				(float) $item->net_amount,
				(int) ($item->expenses_count ?? 0) + (int) ($item->credits_count ?? 0),
			];
		}

		return $rows;
	}

	protected function getTransactionRows(): array
	{
		$rows = [];
		$rows[] = ['Sr No.', 'Date', 'Project', 'User / Person', 'Type', 'Category', 'Description', 'Credit ₹', 'Expense ₹', 'Transfer To', 'Transfer ₹'];

		$i = 1;
		$totalCredit = 0;
		$totalExpense = 0;
		$totalTransfer = 0;

		foreach ($this->timeline as $item) {
			// Show transfers only when user filter is applied
			if ($item->type === 'transfer' && empty($this->filters['users_id'])) {
				continue;
			}

			$credit = $item->type === 'credit' ? (float) $item->amount : 0;
			$expense = $item->type === 'expense' ? (float) $item->amount : 0;
			$transferAmt = $item->type === 'transfer' ? (float) $item->amount : 0;

			if ($item->type === 'credit') {
				$totalCredit += $credit;
			} elseif ($item->type === 'expense') {
				$totalExpense += $expense;
			} elseif ($item->type === 'transfer') {
				$totalTransfer += $transferAmt;
			}

			$rows[] = [
				$i++,
				optional($item->timeline_at)?->format('d-m-Y') ?? '-',
				$item->project_name ?? '-',
				$item->user_name ?? '-',
				ucfirst($item->type),
				'-',
				'-',
				$item->type === 'credit' ? $credit : '',
				$item->type === 'expense' ? $expense : '',
				$item->type === 'transfer' ? $item->user_name : '',
				$item->type === 'transfer' ? $transferAmt : '',
			];
		}

		$rows[] = [];
		$rows[] = ['TOTALS', '', '', '', '', '', '', $totalCredit, $totalExpense, '', $totalTransfer];

		return $rows;
	}

	public function title(): string
	{
		return 'Report';
	}

	public function registerEvents(): array
	{
		return [
			AfterSheet::class => function (AfterSheet $event): void {
				$sheet = $event->sheet->getDelegate();
				$highestRow = (int) $sheet->getHighestRow();
				$highestColumn = $sheet->getHighestColumn();
				$maxCol = max(Coordinate::columnIndexFromString($highestColumn), 11);
				$lastCol = Coordinate::stringFromColumnIndex($maxCol);

				$this->styleTitleRow($sheet, $lastCol);
				$this->styleSummaryTiles($sheet);
				$this->styleSectionHeaders($sheet, $highestRow, $lastCol);
				$this->styleTableHeaders($sheet, $highestRow, $lastCol);
				$this->styleTransactionHeaderRow($sheet, $highestRow, $lastCol);
				$this->setColumnWidths($sheet, $maxCol);
				$this->applyCurrencyFormatting($sheet, $highestRow);
				$this->applyBorders($sheet, $highestRow, $lastCol);
				$this->freezePane($sheet);
			},
		];
	}

	protected function styleTitleRow($sheet, $lastCol): void
	{
		$sheet->mergeCells('A1:' . $lastCol . '1');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getRowDimension(1)->setRowHeight(22);
	}

	protected function styleSummaryTiles($sheet): void
	{
		$sheet->getStyle('A2:J2')->getFont()->setBold(true)->setSize(10);
		$sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
	}

	protected function styleSectionHeaders($sheet, $highestRow, $lastCol): void
	{
		$sections = ['PROJECT SUMMARY','USER SUMMARY','EXPENSE DETAILS','CREDIT DETAILS','TIMELINE','TOTALS'];
		for ($r = 1; $r <= $highestRow; $r++) {
			$val = trim((string) $sheet->getCell('A' . $r)->getValue());
			if (in_array(mb_strtoupper($val), $sections, true)) {
				$sheet->mergeCells('A' . $r . ':' . $lastCol . $r);
				$sheet->getStyle('A' . $r)->getFont()->setBold(true)->setSize(11);
				$sheet->getStyle('A' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
				$sheet->getRowDimension($r)->setRowHeight(18);
			}
		}
	}

	protected function styleTableHeaders($sheet, $highestRow, $lastCol): void
	{
		$sections = ['PROJECT SUMMARY','USER SUMMARY','EXPENSE DETAILS','CREDIT DETAILS','TIMELINE','TOTALS'];
		for ($r = 1; $r <= $highestRow; $r++) {
			$val = trim((string) $sheet->getCell('A' . $r)->getValue());
			if (in_array(mb_strtoupper($val), $sections, true)) {
				$hdr = $r + 1;
				$hdrRange = 'A' . $hdr . ':' . $lastCol . $hdr;
				$sheet->getStyle($hdrRange)->getFont()->setBold(true)->setSize(10);
				$sheet->getStyle($hdrRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
			}
		}
	}

	protected function styleTransactionHeaderRow($sheet, $highestRow, $lastCol): void
	{
		for ($r = 1; $r <= $highestRow; $r++) {
			if (trim((string) $sheet->getCell('A' . $r)->getValue()) === 'Sr No.') {
				$sheet->getStyle('A' . $r . ':' . $lastCol . $r)->getFont()->setBold(true)->setSize(10);
				$sheet->getStyle('A' . $r . ':' . $lastCol . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
				break;
			}
		}
	}

	protected function setColumnWidths($sheet, $maxCol): void
	{
		$widths = [1=>5,2=>12,3=>18,4=>20,5=>12,6=>15,7=>40,8=>14,9=>14,10=>18,11=>14];
		for ($c = 1; $c <= $maxCol; $c++) {
			$col = Coordinate::stringFromColumnIndex($c);
			if (isset($widths[$c])) {
				$sheet->getColumnDimension($col)->setWidth($widths[$c]);
			} else {
				$sheet->getColumnDimension($col)->setAutoSize(true);
			}
		}
	}

	protected function applyCurrencyFormatting($sheet, $highestRow): void
	{
		$sheet->getStyle('H3:H' . $highestRow)->getNumberFormat()->setFormatCode('"₹"#,##0.00');
		$sheet->getStyle('I3:I' . $highestRow)->getNumberFormat()->setFormatCode('"₹"#,##0.00');
		$sheet->getStyle('K3:K' . $highestRow)->getNumberFormat()->setFormatCode('"₹"#,##0.00');
	}

	protected function applyBorders($sheet, $highestRow, $lastCol): void
	{
		$sheet->getStyle('A1:' . $lastCol . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
	}

	protected function freezePane($sheet): void
	{
		$sheet->freezePane('A6');
	}
}


