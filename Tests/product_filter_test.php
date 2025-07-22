<?php
require 'vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

// Connect to ChromeDriver
$host = 'http://localhost:63328';  // your chromedriver URL
$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

// Open your ecommerce homepage
$driver->get('http://localhost/ecommerce%20ws/frontend/home.html');

// Helper function to wait for filtering to happen (adjust as needed)
function waitForFilterResults($driver, $timeout = 5) {
    $end = time() + $timeout;
    while (time() < $end) {
        // you can improve this with a better wait on changes if needed
        sleep(1);
    }
}

// Test 1: Search for a valid product (e.g., "laptop")
$searchBox = $driver->findElement(WebDriverBy::id('searchInput'));
$searchBox->clear();
$searchBox->sendKeys('laptop');
waitForFilterResults($driver);

$productCards = $driver->findElements(WebDriverBy::cssSelector('.product-card'));
if (count($productCards) > 0) {
    echo "Valid search test passed: Products found.\n";
} else {
    echo "Valid search test failed: No products found.\n";
}

// Test 2: Search for invalid product (expecting no results)
$searchBox->clear();
$searchBox->sendKeys('invalidproductname12345');
waitForFilterResults($driver);

try {
    $noResultsPara = $driver->findElement(WebDriverBy::xpath("//div[@id='grid']/p[contains(text(),'No products found')]"));
    if ($noResultsPara->isDisplayed()) {
        echo "Invalid search test passed: 'No products found.' message displayed.\n";
    } else {
        echo "Invalid search test failed: 'No products found.' message not displayed.\n";
    }
} catch (Exception $e) {
    echo "Invalid search test failed: 'No products found.' element not found.\n";
}

// Test 3: Test category filter with valid category 'Electronics'
// Clear search box before applying category filter to avoid conflicts
$searchBox->clear();

$categoryDropdown = $driver->findElement(WebDriverBy::id('categoryFilter'));
$categoryDropdown->click();
$categoryDropdown->findElement(WebDriverBy::xpath("//option[@value='Electronics']"))->click();

waitForFilterResults($driver);

$productCards = $driver->findElements(WebDriverBy::cssSelector('.product-card'));
if (count($productCards) > 0) {
    echo "Category filter test passed: Products found for Electronics.\n";
} else {
    echo "Category filter test failed: No products found for Electronics.\n";
}

// Test 4: Category filter with invalid category (empty) and invalid search text
$searchBox->clear();
$searchBox->sendKeys('invalidproductname12345');

$categoryDropdown->click();
$categoryDropdown->findElement(WebDriverBy::xpath("//option[@value='']"))->click();

waitForFilterResults($driver);

try {
    $noResultsPara = $driver->findElement(WebDriverBy::xpath("//div[@id='grid']/p[contains(text(),'No products found')]"));
    if ($noResultsPara->isDisplayed()) {
        echo "Combined filter test passed: 'No products found.' message displayed.\n";
    } else {
        echo "Combined filter test failed: 'No products found.' message not displayed.\n";
    }
} catch (Exception $e) {
    echo "Combined filter test failed: 'No products found.' element not found.\n";
}

$driver->quit();
