#!/usr/bin/perl

use strict;
use warnings;
use POSIX qw(strftime);
# Check for the required command-line arguments
if (@ARGV < 4) {
    die "Usage: script.pl -i <IDs> -k <API_KEY>\n";
}

my $sched_id;  # Declare $ids variable outside the loop
my $api_key;

# Parse the command-line arguments
while (my $arg = shift @ARGV) {
    if ($arg eq '-i') {
        $sched_id = shift @ARGV;  # Assign to the existing $ids variable
    } elsif ($arg eq '-k') {
        $api_key = shift @ARGV;
    }
}

# Check if the API key and IDs are provided
if (!defined $api_key || !defined $sched_id) {
    die "API key and IDs are required.\n";
}

my $curl_command = "curl --location 'http://localhost/jobs/sendcamp' " .
       "--header 'X-Api-Key: $api_key' " .
        "--form \"sched_id=$sched_id\" " ;
      

print $curl_command;
# Get the current timestamp
my $timestamp = strftime("%Y-%m-%d %H:%M:%S", localtime);

# Run the CURL command and capture HTTP return code
my $cmd = "$curl_command -w '%{http_code}' -o /dev/null";
my $return_code = qx($cmd);

# Log the timestamp, HTTP return code, and command
my $log_file = '/var/www/html/logs/process.log';
open(my $log, '>>', $log_file) or die "Cannot open log file: $!";
print $log "$timestamp - schedule: HTTP Return Code: $return_code\n";
print $log "$timestamp - schedule: Command Executed: $curl_command\n";
close($log);

print "CURL commands have been executed for the $sched_id\n";

exit;