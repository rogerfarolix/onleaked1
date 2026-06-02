<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsvController extends Controller
{
    public function leakCheck(Request $request)
    {
        $request->validate([
            'email'   => ['required', 'email', 'max:254'],
            'results' => ['required', 'array'],
        ]);

        $email    = $request->input('email');
        $breaches = $request->input('results.breaches', []);
        $date     = now()->format('Y-m-d');

        $output  = fopen('php://temp', 'rw');
        fputcsv($output, ['Source', 'Date', 'Password Exposed', 'Data Exposed', 'Domain']);

        foreach ($breaches as $b) {
            fputcsv($output, [
                $b['source']   ?? '',
                $b['date']     ?? '',
                ($b['password_leaked'] ?? false) ? 'Yes' : 'No',
                $b['description'] ?? '',
                $b['domain']   ?? '',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"onleaked-breach-report-{$date}.csv\"",
        ]);
    }

    public function domainAnalysis(Request $request)
    {
        $request->validate([
            'domain'  => ['required', 'string', 'max:253'],
            'results' => ['required', 'array'],
        ]);

        $domain  = $request->input('domain');
        $results = $request->input('results');
        $date    = now()->format('Y-m-d');

        $output = fopen('php://temp', 'rw');

        // DNS Records section
        fputcsv($output, ['=== DNS RECORDS ===']);
        fputcsv($output, ['Type', 'Value']);
        foreach ($results['dns'] ?? [] as $type => $records) {
            if (is_array($records)) {
                foreach ($records as $record) {
                    fputcsv($output, [$type, is_array($record) ? implode(' ', $record) : $record]);
                }
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['=== EMAIL SECURITY ===']);
        fputcsv($output, ['Check', 'Status', 'Value']);
        $email = $results['email_config'] ?? [];
        fputcsv($output, ['MX Records', ($email['has_mx'] ?? false) ? 'Configured' : 'Missing', '']);
        fputcsv($output, ['SPF', ($email['has_spf'] ?? false) ? 'Configured' : 'Missing', $email['spf_record'] ?? '']);
        fputcsv($output, ['DMARC', ($email['has_dmarc'] ?? false) ? 'Configured' : 'Missing', $email['dmarc_record'] ?? '']);

        fputcsv($output, []);
        fputcsv($output, ['=== SUBDOMAINS ===']);
        fputcsv($output, ['Subdomain']);
        foreach ($results['subdomains'] ?? [] as $sub) {
            fputcsv($output, [$sub]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"onleaked-domain-{$domain}-{$date}.csv\"",
        ]);
    }
}
