<?php

require_once 'vendor/autoload.php';

use Config\Database;
use App\Models\AccountModel;

// Test database connection
try {
    $db = Database::connect();
    
    echo "Testing database connection...\n";
    
    // Test basic query
    $query = $db->query("SELECT COUNT(*) as count FROM user_accounts");
    $result = $query->getRow();
    echo "Total accounts in database: " . $result->count . "\n";
    
    // Test AccountModel
    $accountModel = new AccountModel();
    echo "Testing AccountModel...\n";
    
    // Get first account
    $accounts = $accountModel->findAll(1);
    if (!empty($accounts)) {
        echo "First account found: " . json_encode($accounts[0]) . "\n";
    } else {
        echo "No accounts found in database\n";
    }
    
    // Test column structure
    $query = $db->query("DESCRIBE user_accounts");
    $columns = $query->getResult();
    echo "Table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
