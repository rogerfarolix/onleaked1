import os
import uuid
import datetime
from dotenv import load_dotenv
import psycopg2
from psycopg2.extras import DictCursor
from openai import OpenAI

load_dotenv(dotenv_path='../.env')

# Use OpenAI client (configured for Groq or Mistral if using a different base_url)
# The user wants to use Groq/Mistral/Llama via OpenAI compatible API.
client = OpenAI(
    api_key=os.getenv("OPENAI_API_KEY", "your-api-key"),
    # base_url="https://api.groq.com/openai/v1" # Uncomment if using Groq
)

def get_db_connection():
    return psycopg2.connect(
        host=os.getenv('DB_HOST', '127.0.0.1'),
        database=os.getenv('DB_DATABASE', 'onleaked_bd'),
        user=os.getenv('DB_USERNAME', 'postgres'),
        password=os.getenv('DB_PASSWORD', '62461501'),
        port=os.getenv('DB_PORT', '5432')
    )

def generate_ai_recommendation(cve_title, cve_desc):
    prompt = f"Analyze this vulnerability and provide a brief, actionable mitigation recommendation for developers.\n\nTitle: {cve_title}\nDescription: {cve_desc}"
    try:
        response = client.chat.completions.create(
            model="gpt-3.5-turbo", # Replace with 'llama3-8b-8192' if using Groq
            messages=[
                {"role": "system", "content": "You are a senior cybersecurity analyst. Keep recommendations under 3 sentences."},
                {"role": "user", "content": prompt}
            ],
            max_tokens=150
        )
        return response.choices[0].message.content.strip()
    except Exception as e:
        print(f"AI Generation Error: {e}")
        return "Review the official advisory and apply patches immediately."

def fetch_and_store_cves():
    print("Starting Vulnerability Scraper...")
    conn = get_db_connection()
    cursor = conn.cursor(cursor_factory=DictCursor)
    
    # 1. Get all trackable technologies
    cursor.execute("SELECT id, name FROM technologies")
    technologies = cursor.fetchall()
    
    # Simulate scraping with Scrappling
    # In a real-world scenario, we would scrape NVD or GitHub Advisories based on the tech name.
    # Here we simulate fetching 1 recent CVE for 'Laravel' if it exists.
    fetcher = Fetcher(auto_match=False)
    # response = fetcher.get("https://nvd.nist.gov/vuln/search/results?form_type=Basic&results_type=overview&query=laravel")
    # For MVP, we'll mock the data for Laravel to trigger the email alert.
    
    mock_cves = [
        {
            "tech_name": "Laravel",
            "cve_id": "CVE-2026-99999",
            "title": "SQL Injection in Laravel Query Builder",
            "description": "A vulnerability in Laravel 11.x allows remote attackers to execute arbitrary SQL commands via crafted input in the query builder.",
            "severity": "High"
        }
    ]

    for tech in technologies:
        for mock in mock_cves:
            if tech['name'].lower() == mock['tech_name'].lower():
                # Check if CVE already exists
                cursor.execute("SELECT id FROM vulnerabilities WHERE cve_id = %s", (mock['cve_id'],))
                if not cursor.fetchone():
                    print(f"New CVE found for {tech['name']}: {mock['cve_id']}")
                    ai_rec = generate_ai_recommendation(mock['title'], mock['description'])
                    
                    cursor.execute("""
                        INSERT INTO vulnerabilities (id, technology_id, cve_id, title, description, severity, ai_recommendation, published_at, created_at, updated_at)
                        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                    """, (
                        str(uuid.uuid4()), tech['id'], mock['cve_id'], mock['title'], mock['description'], mock['severity'], ai_rec, datetime.datetime.now(), datetime.datetime.now(), datetime.datetime.now()
                    ))
                    conn.commit()
                    print(f"Stored CVE and AI Recommendation in database.")
                else:
                    print(f"CVE {mock['cve_id']} already exists in DB.")

    cursor.close()
    conn.close()
    print("Scraping completed.")

if __name__ == "__main__":
    fetch_and_store_cves()
