<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Technology;
use App\Models\Vulnerability;
use App\Services\NvdService;
use Carbon\Carbon;

#[Signature('onleaked:fetch-cves')]
#[Description('Fetch recent CVEs from NIST NVD for all tracked technologies and store them in the database')]
class FetchCves extends Command
{
    public function handle(NvdService $nvd): void
    {
        $since        = Carbon::now()->subDay();
        $technologies = Technology::all();
        $newCount     = 0;

        foreach ($technologies as $technology) {
            $this->info("Checking NVD for: {$technology->name}");

            try {
                $cves = $nvd->fetchRecent($technology->name, $since);
            } catch (\Exception $e) {
                $this->warn("NVD request failed for {$technology->name}: {$e->getMessage()}");
                continue;
            }

            // Throttle: NVD free key allows 50 req/30s
            sleep(1);

            foreach ($cves as $cve) {
                if (Vulnerability::where('cve_id', $cve['cveId'])->exists()) {
                    continue;
                }

                Vulnerability::create([
                    'technology_id'    => $technology->id,
                    'cve_id'           => $cve['cveId'],
                    'title'            => $cve['title'],
                    'description'      => $cve['description'],
                    'severity'         => $cve['severity'],
                    'ai_recommendation'=> NvdService::aiRecommendation($cve['severity'], $cve['cveId']),
                    'published_at'     => $cve['published'] ? Carbon::parse($cve['published']) : now(),
                ]);

                $newCount++;
                $this->line("  + {$cve['cveId']} ({$cve['severity']})");
            }
        }

        $this->info("Done. {$newCount} new CVE(s) stored.");
    }
}
