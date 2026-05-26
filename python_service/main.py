import os
from dotenv import load_dotenv
import psycopg2
from openai import OpenAI

load_dotenv()

# We will implement Scrappling to fetch CVEs
# And OpenAI to generate AI recommendations
# For now this is just a placeholder

def get_db_connection():
    return psycopg2.connect(
        host=os.getenv('DB_HOST', '127.0.0.1'),
        database=os.getenv('DB_DATABASE', 'onleaked_bd'),
        user=os.getenv('DB_USERNAME', 'postgres'),
        password=os.getenv('DB_PASSWORD', '62461501'),
        port=os.getenv('DB_PORT', '5432')
    )

def main():
    print("Initializing Onleaked Python Service...")
    # Initialize OpenAI client (could use Groq, Mistral by changing base_url)
    client = OpenAI(
        api_key=os.getenv("OPENAI_API_KEY", "your-api-key"),
        # base_url="https://api.groq.com/openai/v1" # if using Groq
    )
    
    # Check DB connection
    try:
        conn = get_db_connection()
        print("Connected to PostgreSQL successfully.")
        conn.close()
    except Exception as e:
        print(f"Error connecting to DB: {e}")

if __name__ == "__main__":
    main()
