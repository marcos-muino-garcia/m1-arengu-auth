<?php

require __DIR__ . '/magento/app/bootstrap.php';
require __DIR__ . '/magento/app/Mage.php';

if($argc !== 2) {
    echo "This script must be used like this:\nphp create_xml.php <version>\n";
    die(-1);
}

$package = new Mage_Connect_Package();

$package
    ->setName('Arengu_Auth')
    ->setVersion($argv[1])
    ->setStability('stable')
    ->setLicense('Apache-2.0', 'https://www.apache.org/licenses/LICENSE-2.0')
    ->setChannel('community')
    ->setSummary('Arengu Auth')
    ->setDescription('Enables custom signup, login and passwordless endpoints to interact with Magento\'s authentication system from Arengu flows.')
    ->setAuthors([['name' => 'Arengu', 'user' => 'arengu', 'email' => 'support@arengu.com']])
    ->setDate(gmdate('Y-m-d'))
    ->setTime(gmdate('H:i:s'))
    ->setDependencyPhpVersion('5.3.0', '99.0.0')
    ->addContent('modules/Arengu_Auth.xml', 'mageetc')
    ->addContentDir('magecommunity', 'Arengu/Auth')
;

$package->save('.');
