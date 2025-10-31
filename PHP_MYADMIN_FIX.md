# Fix phpMyAdmin Deprecation Warnings

These warnings are from phpMyAdmin, not your project code.

## Quick Fix: Modify MAMP's php.ini

1. Navigate to: `C:\MAMP\bin\php\phpX.X.X\php.ini` (where X.X.X is your PHP version)
2. Find the line: `error_reporting = E_ALL`
3. Change it to: `error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT`
4. Save and restart MAMP

## Alternative: Fix phpMyAdmin Config

1. Navigate to: `C:\MAMP\bin\phpMyAdmin5\`
2. Edit or create `config.inc.php`
3. Add at the top (after `<?php`):
   ```php
   error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
   ```

## Best Solution: Update phpMyAdmin

Download the latest phpMyAdmin from https://www.phpmyadmin.net/downloads/
Extract to `C:\MAMP\bin\phpMyAdmin5\` (backup your config.inc.php first)

## Note

These warnings don't affect functionality - they're just deprecation notices for syntax that will be removed in future PHP versions.

