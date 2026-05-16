<?php

namespace App\Services\Sources;

class DnsIntelService
{
    public function lookup(string $email): array
    {
        $domain = substr(strrchr($email, '@'), 1);

        $mx  = dns_get_record($domain, DNS_MX)  ?: [];
        $a   = dns_get_record($domain, DNS_A)   ?: [];
        $txt = dns_get_record($domain, DNS_TXT) ?: [];

        // Detect SPF / DMARC / DKIM from TXT
        $spf   = null;
        $dmarc = null;
        foreach ($txt as $record) {
            $val = $record['txt'] ?? '';
            if (str_starts_with($val, 'v=spf1')) $spf   = $val;
            if (str_starts_with($val, 'v=DMARC1')) $dmarc = $val;
        }

        $whois = $this->getWhois($domain);

        return [
            'domain'      => $domain,
            'mx_records'  => array_map(fn($r) => ['host' => $r['host'], 'pri' => $r['pri']], $mx),
            'a_records'   => array_map(fn($r) => $r['ip'], $a),
            'spf'         => $spf,
            'dmarc'       => $dmarc,
            'whois'       => $whois,
            'is_free'     => $this->isFreeProvider($domain),
            'is_disposable' => $this->isDisposable($domain),
        ];
    }

    private function getWhois(string $domain): array
    {
        try {
            if (!class_exists(\Iodev\Whois\Factory::class)) return [];
            $whois = \Iodev\Whois\Factory::get()->createWhois();
            $info  = $whois->loadDomainInfo($domain);
            return [
                'registrar'  => $info?->registrar,
                'created_at' => $info?->creationDate ? date('Y-m-d', (int)$info->creationDate) : null,
                'expires_at' => $info?->expirationDate ? date('Y-m-d', (int)$info->expirationDate) : null,
                'updated_at' => $info?->updatedDate ? date('Y-m-d', (int)$info->updatedDate) : null,
            ];
        } catch (\Throwable) {
            return [];
        }
    }

    private function isFreeProvider(string $domain): bool
    {
        $free = ['gmail.com','yahoo.com','hotmail.com','outlook.com','live.com',
                 'icloud.com','protonmail.com','tutanota.com','yandex.com',
                 'mail.com','zoho.com','gmx.com','aol.com','msn.com'];
        return in_array(strtolower($domain), $free);
    }

    private function isDisposable(string $domain): bool
    {
        $disposable = [
            // Classiques
            'mailinator.com', 'guerrillamail.com', '10minutemail.com',
            'throwam.com', 'yopmail.com', 'tempmail.com', 'fakeinbox.com',
            'sharklasers.com', 'guerrillamailblock.com', 'grr.la',
            'dispostable.com', 'trashmail.com', 'mailnull.com',
            // Très utilisés
            'temp-mail.org', 'getnada.com', 'mailsac.com', 'spamgourmet.com',
            'spamgourmet.net', 'spamgourmet.org', 'trashmail.at', 'trashmail.io',
            'trashmail.me', 'trashmail.net', 'throwam.com', 'throwaway.email',
            'emailondeck.com', 'emailtemp.org', 'spambox.us', 'spam4.me',
            'discard.email', 'drdrb.net', 'maildrop.cc', 'spamfree24.org',
            'mailexpire.com', 'e4ward.com', 'spamgob.com', 'filzmail.com',
            'mt2015.com', 'mt2016.com', 'mt2017.com',
            // Guerrilla Mail variants
            'grr.la', 'guerrillamail.info', 'guerrillamail.biz', 'guerrillamail.de',
            'guerrillamail.net', 'guerrillamail.org', 'spam4.me', 'trashmail.at',
            // One-click temps
            'tempr.email', 'tempinbox.com', 'tempinbox.co.uk',
            'spamgourmet.com', 'incognitemail.com', 'klzlk.com',
            'anonymbox.com', 'notmailinator.com', 'sofimail.com',
            'crap.la', 'wh4f.org', 'trashmail.org', 'trashmail.com',
            // Russes/Asiatiques connus
            'mailforspam.com', 'spamthis.co.uk', 'jetable.fr.nf',
            'nospam.ze.tc', 'nomail.xl.cx', 'mega.zik.dj',
            // Récents
            'mohmal.com', 'yandex-team.ru', 'haltospam.com',
            'mailnesia.com', 'mailnull.com', 'rcpt.at',
            '33mail.com', 'binkmail.com', 'bobmail.info',
            'chapliemail.com', 'deadaddress.com', 'deadletter.ga',
        ];

        return in_array(strtolower($domain), $disposable);
    }
}
