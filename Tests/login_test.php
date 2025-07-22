<?php
require __DIR__ . '/vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$host = 'http://localhost:63328'; // your ChromeDriver port
$loginUrl = 'http://localhost/ecommerce%20ws/frontend/login.html'; // your login page URL (note the space encoded as %20)

try {
    $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());

    // Open the login page
    $driver->get($loginUrl);

    // Fill username and password fields by their IDs
    $driver->findElement(WebDriverBy::id('username'))->sendKeys('testuser3');
    $driver->findElement(WebDriverBy::id('password'))->sendKeys('testuser');

    // Click the Login button
    $driver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();

    // Wait up to 5 seconds for either:
    // - URL to change to home.html (indicating success)
    // - or presence of error message in #msg element

    $driver->wait(10)->until(
        WebDriverExpectedCondition::or(
            WebDriverExpectedCondition::urlContains('home.html'),
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('msg'))
        )
    );

    // Check if redirected to home.html (login success)
    $currentUrl = $driver->getCurrentURL();
    if (strpos($currentUrl, 'home.html') !== false) {
        echo "✅ Login successful, redirected to home.html\n";
    } else {
        // If not redirected, check error message text
        $msg = $driver->findElement(WebDriverBy::id('msg'))->getText();
        echo "❌ Login failed, message: " . $msg . "\n";
    }

    $driver->quit();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
