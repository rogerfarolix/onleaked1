import sys
import os
import subprocess
import re
import json

# Maigret account discovery by username (derived from the email local-part).
# Outputs a JSON list of {"name": ..., "url": ...} for claimed profiles.
# Defensive by design: any failure yields an empty list so the pipeline never breaks.

ANSI = re.compile(r'\x1b\[[0-9;]*m')


def main():
    if len(sys.argv) < 2:
        print(json.dumps([]))
        return

    username = sys.argv[1].strip()

    # Only run on a plausible username to avoid noise / abuse.
    if not re.fullmatch(r'[A-Za-z0-9._-]{3,40}', username):
        print(json.dumps([]))
        return

    maigret_bin = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'venv', 'bin', 'maigret')
    if not os.path.exists(maigret_bin):
        print(json.dumps([]))
        return

    try:
        result = subprocess.run(
            [
                maigret_bin, username,
                '--no-progressbar',
                '--print-found',
                '--timeout', '7',
                '--folderoutput', '/tmp/maigret_reports',
            ],
            capture_output=True,
            text=True,
            timeout=50,
        )

        found = []
        seen = set()
        for raw in (result.stdout or '').split('\n'):
            line = ANSI.sub('', raw).strip()
            if not line.startswith('[+]'):
                continue
            body = line[3:].strip()
            if ':' not in body:
                continue
            name, url = body.split(':', 1)
            name = name.strip()
            url = url.strip()
            if not url.startswith('http'):
                continue
            if len(url) > 300 or url in seen:
                continue
            seen.add(url)
            found.append({'name': name[:60], 'url': url})
            if len(found) >= 300:
                break

        print(json.dumps(found))

    except Exception:
        print(json.dumps([]))


if __name__ == '__main__':
    main()
