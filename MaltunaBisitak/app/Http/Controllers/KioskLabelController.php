<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The kiosk browser saves this file in C:\Etiquetas, where print_label_html_pdf.ps1 prints it (PW003, PW004).
 */
class KioskLabelController extends Controller
{
    /**
     * A short-lived signed link, so labels can only be downloaded right after a kiosk registration.
     */
    public static function urlFor(Visitor $visitor, ?string $origin): string
    {
        return URL::temporarySignedRoute('kiosk.label', now()->addMinutes(10), [
            'visitor' => $visitor,
            'nondik' => $origin ?? $visitor->origin,
        ]);
    }

    public function __invoke(Request $request, Visitor $visitor): StreamedResponse
    {
        // The script builds HTML from these values, and Windows PowerShell 5 reads files without a BOM
        // as ANSI: plain text and \u escapes (json_encode's default) keep "ñ" and accents intact.
        $label = json_encode([
            'Izena' => strip_tags($visitor->first_name),
            'Abizena' => strip_tags($visitor->last_name),
            'Nondik' => strip_tags((string) $request->query('nondik')),
        ], JSON_THROW_ON_ERROR);

        return response()->streamDownload(
            function () use ($label): void {
                echo $label;
            },
            "etiketa-{$visitor->id}-".now()->format('YmdHis').'.txt',
            ['Content-Type' => 'text/plain'],
        );
    }
}
