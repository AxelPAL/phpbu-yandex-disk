<?php
namespace AxelPAL\Backup\Sync;

use Mackey\Yandex\Client;
use Mackey\Yandex\Disk;
use Mackey\Yandex\Exception\NotFoundException;
use phpbu\App\Backup\Sync\Exception;
use phpbu\App\Result;
use phpbu\App\Backup\Sync;
use phpbu\App\Backup\Target;
use phpbu\App\Util\Arr;
use phpbu\App\Util\Str;

/**
 * Yandex.Disk
 *
 * @package    phpbu
 * @subpackage Backup
 * @author     AxelPAL <axelpal@gmail.com>
 * @copyright  AxelPAL <axelpal@gmail.com>
 * @license    https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link       http://axelpal.ru
 * @since      Class available since Release 1.1.1
 */
class YandexDisk implements Sync
{
    /**
     * API access token
     * Goto https://oauth.yandex.ru/client/new
     * create your app
     *  - Check all Disks permissions
     *  - generate access token:
     * 1) Goto https://oauth.yandex.ru/authorize?response_type=token&client_id=APP_ID (replace APP_ID with ID giving to you)
     * 2) Then you should get token parameter from GET-parameters of opened page
     *
     * @var  string
     */
    protected $token;

    /**
     * Remote path
     *
     * @var string
     */
    protected $path;

    /**
     * @var Disk
     */
    protected $disk;

    /**
     * (non-PHPDoc)
     *
     * @see    \phpbu\App\Backup\Sync::setup()
     * @param  array $config
     * @throws \phpbu\App\Backup\Sync\Exception
     */
    public function setup(array $config)
    {
        if (!class_exists('\\Mackey\\Yandex\\Client')) {
            throw new Exception('Yandex.Disk sdk not loaded: use composer to install "arhitector/yandex"');
        }
        if (!Arr::isSetAndNotEmptyString($config, 'token')) {
            throw new Exception('API access token is mandatory');
        }
        if (!Arr::isSetAndNotEmptyString($config, 'path')) {
            throw new Exception('yandex.disk path is mandatory');
        }
        $this->token = $config['token'];
        $this->path  = Str::withTrailingSlash(Str::replaceDatePlaceholders($config['path']));
    }

    /**
     * (non-PHPDoc)
     *
     * @see    \phpbu\App\Backup\Sync::sync()
     * @param  \phpbu\App\Backup\Target $target
     * @param  \phpbu\App\Result        $result
     * @throws \phpbu\App\Backup\Sync\Exception
     */
    public function sync(Target $target, Result $result)
    {
        $sourcePath  = $target->getPathname();
        $yandexDiskPath = $this->path . $target->getFilename();
        $this->disk = new Disk($this->token);

        $size = null;
        if (stream_is_local($sourcePath)) {
            $size = filesize($sourcePath);
        }

        $file = $this->disk->resource();
        $this->createFolders();
        try{
            $file = $this->disk->resource($yandexDiskPath);
            $file->getContents();
        } catch (NotFoundException $exc){
            $file->upload($sourcePath, true);
        }
        if($file->has()){
            $result->debug('upload: done  (' . $size . ')');
        } else {
            $result->debug('upload: error while uploading file');
        }
    }

    private function createFolders()
    {
        $folderPaths = explode("/", $this->path);
        if($folderPaths){
            foreach ($folderPaths as $folderPath) {
                $file = $this->disk->resource();
                try{
                    $file = $this->disk->resource($folderPath);
                    $file->getContents();
                } catch (NotFoundException $exc){
                    $file->create();
                }
            }
        }
    }
}
