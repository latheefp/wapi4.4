<?php

// Define ranges
$accountNumbers = range(1, 6);
$years = range(2022, 2024);
$currentYear = date("Y");
$currentMonth = date("n");

// Loop through each account number
foreach ($accountNumbers as $accountNumber) {
    foreach ($years as $year) {
        if ($year == $currentYear) {
            $startMonth = 1;
            $endMonth = $currentMonth;
        } elseif ($year == 2022) {
            $startMonth = 1;
            $endMonth = 12;
        } else {
            $startMonth = 1; // For year 2023
            $endMonth = 12;  // For year 2023
        }

        for ($month = $startMonth; $month <= $endMonth; $month++) {
            echo "bin/cake Invoice -a $accountNumber -m $month -y $year\n";
        }
    }
}
?>

