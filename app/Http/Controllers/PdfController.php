<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function leakCheck(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'email'    => ['required', 'email', 'max:254'],
            'results'  => ['required', 'array'],
        ]);

        $email    = strtolower($request->input('email'));
        $results  = $request->input('results');
        $date     = now()->format('Y-m-d H:i') . ' UTC';

        $pdf = Pdf::loadView('pdf.leak-check', compact('email', 'results', 'date'))
            ->setPaper('a4')
            ->setOptions(['dpi' => 120, 'isHtml5ParserEnabled' => true]);

        $filename = 'onleaked-breach-report-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function domainAnalysis(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'domain'  => ['required', 'string', 'max:253'],
            'results' => ['required', 'array'],
        ]);

        $domain  = $request->input('domain');
        $results = $request->input('results');
        $date    = now()->format('Y-m-d H:i') . ' UTC';

        $pdf = Pdf::loadView('pdf.domain-analysis', compact('domain', 'results', 'date'))
            ->setPaper('a4')
            ->setOptions(['dpi' => 120, 'isHtml5ParserEnabled' => true]);

        $filename = 'onleaked-domain-report-' . $domain . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
