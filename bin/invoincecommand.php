<?php

// Define ranges
$accountNumbers = range(1, 6);
$years = range(2022, 2024);
$currentYear = date("Y");
$currentMonth = date("n");

// Loop through each account number
foreach ($accountNumbers as $accountNumber) {
    // Loop through each year
    foreach ($years as $year) {
        // Determine the range of months based on the year
        $startMonth = ($year == 2022) ? 1 : (($year == $currentYear) ? 1 : 12);
        $endMonth = ($year == $currentYear) ? $currentMonth : 12;

        // Loop through each month
        for ($month = $startMonth; $month <= $endMonth; $month++) {
            // Print the command
            echo "bin/cake Invoice -a $accountNumber -m $month -y $year\n";
        }
    }
}

