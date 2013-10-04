<?php
/**
 * ImageController class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-imagemanager.controllers
 */

/**
 * Controller class for image actions.
 */
class ImageController extends CController
{
    /**
     * @var string the image manager component id.
     */
    public $componentID = 'imageManager';

    /** @var ImageManager */
    private $_imageManager;

    /**
     * Creates a new image preset from an existing image.
     * @param string $name the preset name.
     * @param integer $fileId the model id.
     * @param string $format the image format.
     */
    public function actionPreset($name, $fileId, $format)
    {
        $image = $this->getImageManager()->loadModelByFileId($fileId);
        $preset = $this->getImageManager()->createPresetImage($name, $image, $format);
        $preset->show($format);
        Yii::app()->end();
    }

    /**
     * Creates a new placeholder image preset.
     * @param string $name the placeholder name.
     * @param string $preset the preset name.
     * @param string $format the image format.
     */
    public function actionHolder($name, $preset, $format = Image::FORMAT_PNG)
    {
        $image = $this->getImageManager()->createPresetHolder($preset, $name, $format);
        $image->show($format);
        Yii::app()->end();

    }

    /**
     * Creates a new image by filtering an existing image.
     * @param integer $id the model id.
     * @param string $format the image format.
     * @throws CException if a required parameters is missing.
     */
    public function actionFilter($id, $format)
    {
        if (!isset($_GET['config'])) {
            throw new CException('You have to provide a "config" parameter.');
        }
        $model = $this->getImageManager()->loadModel($id);
        $image = $model->openImage();
        $preset = ImagePreset::create(array('filters' => $_GET['config']));
        $image = $preset->applyFilters($image);
        $image->show($format);
    }

    /**
     * Returns the image manager component.
     * @return ImageManager the component.
     * @throws CException if the component is not found.
     */
    protected function getImageManager()
    {
        if ($this->_imageManager !== null) {
            return $this->_imageManager;
        } else {
            if (($imageManager = Yii::app()->getComponent($this->componentID)) == null) {
                throw new CException(sprintf(
                    'Failed to get the image manager component. Application component "%" does not exist.',
                    $this->componentID
                ));
            }
            return $this->_imageManager = $imageManager;
        }
    }
}