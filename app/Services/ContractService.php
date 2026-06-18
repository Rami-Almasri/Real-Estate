<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\House;
use App\Models\Office;
use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Generates legally-formatted rent/sale contracts as Arabic PDF documents and
 * tracks their maturity dates. dompdf has no Arabic shaping engine, so we run
 * every Arabic string through ar-php's glyph joiner first (pure PHP, no GD),
 * then render with an embedded Amiri font.
 */
class ContractService
{
    private Arabic $arabic;

    public function __construct()
    {
        $this->arabic = new Arabic();
    }

    /** Create a contract record, compute its due date, and render its PDF. */
    public function create(Office $office, array $data): Contract
    {
        $house = House::findOrFail($data['house_id']);

        $start   = Carbon::parse($data['start_date']);
        $cycle   = $data['payment_cycle'] ?? 'monthly';
        $end     = ! empty($data['end_date']) ? Carbon::parse($data['end_date']) : $this->defaultEnd($start, $data['type'], $cycle);
        $dueDate = $this->nextDueDate($start, $cycle);

        $contract = $office->contracts()->create([
            'house_id'          => $house->id,
            'type'              => $data['type'],
            'party_name'        => $data['party_name'],
            'party_phone'       => $data['party_phone'] ?? null,
            'party_national_id' => $data['party_national_id'] ?? null,
            'amount'            => $data['amount'],
            'payment_cycle'     => $cycle,
            'start_date'        => $start->toDateString(),
            'end_date'          => $end?->toDateString(),
            'due_date'          => $dueDate?->toDateString(),
            'status'            => 'active',
            'notes'             => $data['notes'] ?? null,
            'reference'         => $this->reference(),
        ]);

        // Mark the unit as occupied + stamp the closed deal for analytics.
        $house->update(['status' => 'occupied', 'closed_at' => now()]);

        $contract->update(['pdf_path' => $this->render($contract->fresh(['house.district.city', 'office.provider']))]);

        return $contract->fresh();
    }

    /** Render (or re-render) the PDF and return its public storage path. */
    public function render(Contract $contract): string
    {
        $contract->loadMissing(['house.district.city', 'office.provider']);

        $ar = fn ($text) => $this->arabic->utf8Glyphs((string) $text);

        $pdf = Pdf::loadView('contracts.pdf', [
            'contract' => $contract,
            'ar'       => $ar,
        ])->setPaper('a4');

        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('chroot', [storage_path('fonts'), public_path(), storage_path()]);

        $relative = 'contracts/contract-' . $contract->id . '.pdf';
        Storage::disk('public')->put($relative, $pdf->output());

        return $relative;
    }

    public function download(Contract $contract)
    {
        if (! $contract->pdf_path || ! Storage::disk('public')->exists($contract->pdf_path)) {
            $contract->update(['pdf_path' => $this->render($contract)]);
        }

        return Storage::disk('public')->download(
            $contract->pdf_path,
            'contract-' . ($contract->reference ?: $contract->id) . '.pdf'
        );
    }

    /** Active contracts maturing within $days days, for the alerts panel. */
    public function dueAlerts(?Office $office = null, int $days = 14)
    {
        return Contract::query()
            ->dueWithin($days)
            ->when($office, fn ($q) => $q->where('office_id', $office->id))
            ->with(['house.district', 'office.provider'])
            ->orderBy('due_date')
            ->get();
    }

    private function nextDueDate(Carbon $start, string $cycle): ?Carbon
    {
        return match ($cycle) {
            'monthly'   => (clone $start)->addMonth(),
            'quarterly' => (clone $start)->addMonths(3),
            'yearly'    => (clone $start)->addYear(),
            default     => null, // one-off (sale) has no recurring due date
        };
    }

    private function defaultEnd(Carbon $start, string $type, string $cycle): ?Carbon
    {
        if ($type === 'sale') {
            return null;
        }
        return (clone $start)->addYear();
    }

    private function reference(): string
    {
        return 'SY-' . now()->format('Ymd') . '-' . str_pad((string) (Contract::max('id') + 1), 4, '0', STR_PAD_LEFT);
    }
}
