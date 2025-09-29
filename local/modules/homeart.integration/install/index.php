<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Homeart\Integration\Table\IntegrationModuleMainTable;

Loc::loadMessages(__FILE__);

/**
 * Class homeart.integration
 * Класс, подключающий дополнительный функционал для портала песочница интеграций
 */
class homeart_integration extends CModule
{
    /** @var string - id модуля */
    public $MODULE_ID;
    /** @var string - название модуля */
    public $MODULE_NAME;
    /** @var string - описание модуля */
    public $MODULE_DESCRIPTION;
    /** @var string - версия модуля */
    public $MODULE_VERSION;
    /** @var string - дата текущей версии модуля */
    public $MODULE_VERSION_DATE;
    /** @var string - название разработчика модуля */
    public $PARTNER_NAME;
    /** @var string - код сайта разработчика модуля */
    public $PARTNER_URI;

    /**
     * Заполнение информации о модуле и разработчике
     */
    public function __construct()
    {
        //заполнение данных о модуле
        $this->MODULE_ID = strtolower(basename(dirname(__DIR__)));
        $this->MODULE_NAME = Loc::getMessage($this->MODULE_ID . '_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage($this->MODULE_ID . '_MODULE_DESCRIPTION');
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        //заполнение данных о разработчике
        $this->PARTNER_NAME = Loc::getMessage($this->MODULE_ID . '_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage($this->MODULE_ID . '_PARTNER_URI');
    }

    /**
     * Проверка версии ядра системы
     *
     * @return bool
     */
    protected function isVersionD7()
    {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    /**
     * Установка модуля
     */
    public function DoInstall()
    {
        $connection = Application::getConnection();
        if ($this->isVersionD7()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallComponents();
            $this->InstallFiles();
        } else {
            $connection->ThrowException(Loc::getMessage($this->MODULE_ID . "_INSTALL_ERROR_WRONG_VERSION"));
        }
    }

    /**
     * Удаление модуля
     */
    public function DoUnInstall()
    {
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        global $APPLICATION;

        // проверяем какой сейчас шаг, если он не существует или меньше 2, то выводим первый шаг удаления
        if (!isset($request["step"]) || $request["step"] < 2) {
            // подключаем скрипт с административным прологом и эпилогом
            $APPLICATION->IncludeAdminFile(
                (Loc::getMessage($this->MODULE_ID . "_UNINSTALL_TITLE")),
                __DIR__ . '/unstep1.php'
            );
        } elseif ((int)$request->get('step') === 2) {
            if ($request->get("savedata") !== "Y") {
                $this->UnInstallDB();
            }
            $this->UnInstallComponents();
            $this->UnInstallFiles();
            ModuleManager::unRegisterModule($this->MODULE_ID);
        }
    }

    /**
     * Добавляет таблицы модуля в БД.
     * @return bool|void
     */
    public function InstallDB()
    {
        global $APPLICATION;
        // Проверяем подключение модуля
        if (!Loader::includeModule($this->MODULE_ID)) {
            return false;
        }
        // Выводим сообщение об ошибке если установить не удалось
        try {
            // Проверяем существование таблицы и создаем если такой нет
            $integrationModuleTable = IntegrationModuleMainTable::getEntity();
            $connection = Application::getConnection();

            if (!$connection->isTableExists(IntegrationModuleMainTable::getTableName())) {
                $integrationModuleTable->createDbTable();
            }
            return true;
        } catch (Exception $e) {
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }
    }

    /**
     * Удаляет таблицы модуля из БД.
     * @return bool|void
     */
    public function UnInstallDB()
    {
        // Проверяем подключение модуля
        if (!Loader::includeModule($this->MODULE_ID)) {
            return false;
        }

        $connection = Application::getConnection();
        if ($connection->isTableExists(IntegrationModuleMainTable::getTableName())) {
            $connection->dropTable(IntegrationModuleMainTable::getTableName());
        }
        return true;
    }

    /**
     * Копирует файлы административного интерфейса
     * @return bool
     */
    public function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . "/admin",
            Application::getDocumentRoot() . "/bitrix/admin",
            true,
            true
        );
        return true;
    }

    /**
     * Удаляет файлы административного интерфейса
     * @return bool
     */
    public function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/admin/my_integration.php");
        DeleteDirFilesEx("/bitrix/admin/my_item_integration.php");
        return true;
    }

    /**
     * Копирует компоненты модуля
     * @return bool
     * @throws \Bitrix\Main\SystemException
     */
    public function InstallComponents()
    {
        $documentRoot = Application::getDocumentRoot();

        // Путь к компонентам в модуле
        $moduleComponentsPath = __DIR__ . '/components';

        // Проверяем существование папки с компонентами
        if (!\Bitrix\Main\IO\Directory::isDirectoryExists($moduleComponentsPath)) {
            // Если папки нет - это не ошибка, просто нет компонентов для установки
            return true;
        }

        try {
            // Используем стандартный метод Bitrix для установки компонентов
            $this->installModuleComponents($moduleComponentsPath);
            return true;
        } catch (Exception $e) {
            $this->__InstallComponentsRollback();
            throw new \Bitrix\Main\SystemException(
                'Component installation failed: ' . $e->getMessage()
            );
        }
    }

    /**
     * Удаляет компоненты модуля
     * @return bool
     */
    public function UnInstallComponents()
    {
        $documentRoot = Application::getDocumentRoot();
        $componentsToRemove = [
            '/local/components/homeart/homeart.component'
        ];

        foreach ($componentsToRemove as $componentPath) {
            $fullPath = $documentRoot . $componentPath;
            if (\Bitrix\Main\IO\Directory::isDirectoryExists($fullPath)) {
                \Bitrix\Main\IO\Directory::delete($fullPath);
            }
        }

        // Удаляем пустую папку пространства имен если она пуста
        $namespacePath = $documentRoot . '/local/components/homeart';
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($namespacePath)) {
            $dir = new \Bitrix\Main\IO\Directory($namespacePath);
            if (count($dir->getChildren()) === 0) {
                \Bitrix\Main\IO\Directory::delete($namespacePath);
            }
        }

        return true;
    }

    /**
     * Установка компонентов модуля
     * @param string $sourcePath Путь к компонентам в модуле
     */
    private function installModuleComponents($sourcePath)
    {
        $documentRoot = Application::getDocumentRoot();
        $targetPath = $documentRoot . '/local/components';

        // Создаем целевую директорию если не существует
        if (!\Bitrix\Main\IO\Directory::isDirectoryExists($targetPath)) {
            \Bitrix\Main\IO\Directory::createDirectory($targetPath);
        }

        // Рекурсивно копируем все компоненты
        $dir = new \Bitrix\Main\IO\Directory($sourcePath);
        $children = $dir->getChildren();

        foreach ($children as $child) {
            if ($child->isDirectory()) {
                $componentNamespace = $child->getName(); // homeart
                $namespacePath = $targetPath . '/' . $componentNamespace;

                // Создаем папку пространства имен если не существует
                if (!\Bitrix\Main\IO\Directory::isDirectoryExists($namespacePath)) {
                    \Bitrix\Main\IO\Directory::createDirectory($namespacePath);
                }

                // Копируем компоненты этого пространства имен
                $componentDirs = $child->getChildren();
                foreach ($componentDirs as $componentDir) {
                    if ($componentDir->isDirectory()) {
                        $componentName = $componentDir->getName(); // homeart.component
                        $targetComponentPath = $namespacePath . '/' . $componentName;

                        // Копируем компонент
                        CopyDirFiles(
                            $componentDir->getPath(),
                            $targetComponentPath,
                            true,
                            true,
                            false // не останавливаться на ошибках
                        );
                    }
                }
            }
        }
    }

    /**
     * Откат установки компонентов при ошибке
     */
    private function __InstallComponentsRollback()
    {
        $this->UnInstallComponents();
    }
}
