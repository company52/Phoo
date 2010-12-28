Phoo Ooyala API PHP Client Library
==================================
Developed by Company52


Requirements
------------
Phoo requires PHP 5.3.1 or greater to run.  


Step 1
------
Require and register the Phoo class autoloader:

    require __DIR__ . '/lib/Phoo/Autoloader.php'; # Your path may vary - replace __DIR__ with correct path
    $autoloader = new \Phoo\Autoloader();
    $autoloader->register();

Step 2
------
Create a new instance of Phoo\Backlot to use in your code, and pass in your Oomyala partner code and secret code.

    $backlot = new \Phoo\Backlot($partnerCode, $secretCode);
