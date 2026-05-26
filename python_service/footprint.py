import sys
import subprocess
import re
import json

def main():
    if len(sys.argv) < 2:
        print(json.dumps([]))
        sys.exit(1)

    email = sys.argv[1]
    
    # We call holehe CLI directly
    # --only-used ensures it only prints sites where the email is registered
    # --no-color removes ANSI color codes
    # --no-clear prevents clearing the screen
    try:
        # Run holehe and capture both stdout and stderr
        # It takes ~10-15 seconds for 120+ sites
        result = subprocess.run(
            ["python_service/venv/bin/holehe", email, "--only-used", "--no-color", "--no-clear"],
            capture_output=True,
            text=True,
            timeout=45
        )
        
        output = result.stdout
        
        # Parse output for lines like "[+] SiteName"
        # Since we used --only-used, all [+] lines are positive matches
        sites = []
        for line in output.split('\n'):
            line = line.strip()
            if line.startswith('[+]') and 'Email used' not in line:
                # Extract site name
                site_name = line.replace('[+]', '').strip()
                if site_name:
                    sites.append(site_name)
                    
        print(json.dumps(sites))
        
    except Exception as e:
        print(json.dumps([]))

if __name__ == "__main__":
    main()
