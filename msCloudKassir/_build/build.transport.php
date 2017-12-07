<?php
/**
 * msCloudKassir build script
 *
 * @package    mscloudkassir
 * @subpackage build
 */
$mtime  = microtime();
$mtime  = explode(' ', $mtime);
$mtime  = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
define('PKG_NAME', 'msCloudKassir');
define('PKG_NAME_LOWER', strtolower(PKG_NAME));
define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'pl');

/* define sources */
$root    = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root'          => $root,
    'docs'          => $root . 'docs/',
    'build'         => $root . '_build/',
    'data'          => $root . '_build/data/',
    'resolvers'     => $root . '_build/resolvers/',
    'chunks'        => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/',
    'snippets'      => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'plugins'       => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'source_assets' => $root . 'assets/components/' . PKG_NAME_LOWER,
    'source_core'   => $root . 'core/components/' . PKG_NAME_LOWER,
);

if (!file_exists($sources['build'] . '/build.config.php')) {
    die('Copy build.config.example.php to build.config.php and configure.');
}
/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once $sources['build'] . '/includes/functions.php';

$modx = new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');
$modx->log(modX::LOG_LEVEL_INFO, 'Created Transport Package and Namespace.');

/* load system settings */
$settings = include $sources['data'] . 'transport.settings.php';
if (!is_array($settings)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in settings.');
} else {
    $attributes = array(
        xPDOTransport::UNIQUE_KEY    => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => BUILD_SETTING_UPDATE,
    );
    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting, $attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' System Settings.');
}
unset($settings, $setting, $attributes);

// Create category
$modx->log(xPDO::LOG_LEVEL_INFO, 'Created category.');
/** @var modCategory $category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);

// Add plugins
$plugins = include $sources['data'] . 'transport.plugins.php';
if (!is_array($plugins)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in plugins.');
} else {
    $category->addMany($plugins);
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($plugins) . ' plugins.');
}

// Create category vehicle
$attr    = array(
    xPDOTransport::UNIQUE_KEY                => 'category',
    xPDOTransport::PRESERVE_KEYS             => false,
    xPDOTransport::UPDATE_OBJECT             => true,
    xPDOTransport::RELATED_OBJECTS           => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Plugins'      => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => BUILD_PLUGIN_UPDATE,
            xPDOTransport::UNIQUE_KEY    => 'name',
        ),
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => BUILD_EVENT_UPDATE,
            xPDOTransport::UNIQUE_KEY    => array('pluginid', 'event'),
        ),
    ),
);

$vehicle = $builder->createVehicle($category, $attr);

// Now pack in resolvers
$modx->log(modX::LOG_LEVEL_INFO, 'Adding file resolvers...');
// Now pack in resolvers
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

$modx->log(modX::LOG_LEVEL_INFO, 'Adding in PHP resolvers...');

$builder->putVehicle($vehicle);
unset($file, $attributes);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'changelog'     => file_get_contents($sources['docs'] . 'changelog.txt'),
    'license'       => file_get_contents($sources['docs'] . 'license.txt'),
    'readme'        => file_get_contents($sources['docs'] . 'readme.txt'),
));
$modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options.');

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
$builder->pack();

$mtime     = microtime();
$mtime     = explode(" ", $mtime);
$mtime     = $mtime[1] + $mtime[0];
$tend      = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, "\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();