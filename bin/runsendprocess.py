#!/usr/bin/env python

import sys
import subprocess
from datetime import datetime

# Check for the required command-line arguments
if len(sys.argv) < 5 or '-i' not in sys.argv or '-k' not in sys.argv:
    print("Usage: script.py -i <IDs> -k <API_KEY>")
    sys.exit(1)

# Parse the command-line arguments
ids = None
api_key = None

for i in range(len(sys.argv)):
    if sys.argv[i] == '-i':
        ids = sys.argv[i + 1]
    elif sys.argv[i] == '-k':
        api_key = sys.argv[i + 1]

# Check if the API key and IDs are provided
if not api_key or not ids:
    print("API key and IDs are required.")
    sys.exit(1)

curl_command = f"curl --location 'http://localhost/jobs/runjob/{ids}' " \
               f"--header 'X-Api-Key: {api_key}' " \
               f"--form 'qid={ids}'" \
               f"--form 'type=send'" \


# Get the current timestamp
timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

# Run the CURL command and capture the HTTP return code
try:
    cmd = [curl_command, '-w', '%{http_code}', '-o', '/dev/null']
    return_code = subprocess.check_output(cmd).decode().strip()
except subprocess.CalledProcessError as e:
    return_code = e.returncode

# Log the timestamp, HTTP return code, and command
log_file = '/var/www/html/logs/send-proccess.log'
with open(log_file, 'a') as log:
    log.write(f"{timestamp} - HTTP Return Code: {return_code}\n")
    log.write(f"{timestamp} - Command Executed: {curl_command}\n")

print(f"CURL commands have been executed for the {ids}")

