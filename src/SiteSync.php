<?php
/**
 * Site Sync plugin for Craft CMS 3.x
 *
 * Sync content to other sites on element save.
 *
 * @link      https://github.com/timkelty
 * @copyright Copyright (c) 2018 Tim Kelty
 */

namespace timkelty\craft\sitesync;

use timkelty\craft\sitesync\models\Settings;
use timkelty\craft\sitesync\fields\SiteSyncField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\events\ModelEvent;

use yii\base\Event;

class SiteSync extends Plugin
{
    public function init()
    {
        parent::init();

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'timkelty\craft\sitesync\console\controllers';
        }

        $this->setComponents([
            'content' => \timkelty\craft\sitesync\services\Content::class,
        ]);

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = SiteSyncField::class;
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        ModelEvent::on(
            Element::class,
            Element::EVENT_BEFORE_SAVE,
            [new listeners\BeforeElementSave, 'handle']
        );

    }

    // protected function createSettingsModel()
    // {
    //     return new Settings();
    // }
    //
    // protected function settingsHtml(): string
    // {
    //     return Craft::$app->view->renderTemplate(
    //         'site-sync/settings',
    //         [
    //             'settings' => $this->getSettings()
    //         ]
    //     );
    // }
}