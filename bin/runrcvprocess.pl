#!/usr/bin/perl

use strict;
use warnings;
use POSIX qw(strftime);
# Check for the required command-line arguments
if (@ARGV < 4) {
    die "Usage: script.pl -i <IDs> -k <API_KEY>\n";
}

my $ids;  # Declare $ids variable outside the loop
my $api_key;

# Parse the command-line arguments
while (my $arg = shift @ARGV) {
    if ($arg eq '-i') {
        $ids = shift @ARGV;  # Assign to the existing $ids variable
    } elsif ($arg eq '-k') {
        $api_key = shift @ARGV;
    }
}

# Check if the API key and IDs are provided
if (!defined $api_key || !defined $ids) {
    die "API key and IDs are required.\n";
}

my $curl_command = "curl --location 'http://localhost/jobs/runjob/$ids' " .
       "--header 'X-Api-Key: $api_key' " .
        "--form \"qid=$ids\" " .
        "--form \"type=receive\"";

print $curl_command;
# Get the current timestamp
my $timestamp = strftime("%Y-%m-%d %H:%M:%S", localtime);

# Run the CURL command and capture HTTP return code
my $cmd = "$curl_command -w '%{http_code}' -o /dev/null";
my $return_code = qx($cmd);

# Log the timestamp, HTTP return code, and command
my $log_file = '/var/www/html/logs/process.log';
open(my $log, '>>', $log_file) or die "Cannot open log file: $!";
print $log "$timestamp - HTTP Return Code: $return_code\n";
print $log "$timestamp - Command Executed: $curl_command\n";
close($log);

print "CURL commands have been executed for the $ids\n";

exit;