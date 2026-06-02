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

    public function passwordCheck(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'count'      => ['required', 'integer', 'min:0'],
            'risk'       => ['required', 'string', 'in:safe,low,medium,high'],
            'risk_label' => ['required', 'string', 'max:200'],
        ]);

        $count      = $request->input('count');
        $risk       = $request->input('risk');
        $risk_label = $request->input('risk_label');

        $pdf = Pdf::loadView('pdf.password-check', compact('count', 'risk', 'risk_label'))
            ->setPaper('a4')
            ->setOptions(['dpi' => 120, 'isHtml5ParserEnabled' => true]);

        return $pdf->download('onleaked-password-report-' . now()->format('Ymd') . '.pdf');
    }

    public function sslAnalysis(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'domain'  => ['required', 'string', 'max:253'],
            'ssl'     => ['required', 'array'],
        ]);

        $domain = $request->input('domain');
        $ssl    = $request->input('ssl');

        $pdf = Pdf::loadView('pdf.ssl-check', compact('domain', 'ssl'))
            ->setPaper('a4')
            ->setOptions(['dpi' => 120, 'isHtml5ParserEnabled' => true]);

        return $pdf->download('onleaked-ssl-report-' . $domain . '-' . now()->format('Ymd') . '.pdf');
    }
}
