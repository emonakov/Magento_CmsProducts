<?php
$pageController = Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Cms'.DS.'PageController.php';
require_once($pageController);
class Cyberhull_CmsProducts_Adminhtml_Cms_PageController extends Mage_Adminhtml_Cms_PageController
{
    public function productsTabAction()
    {
        $this->_initPage();
        $this->loadLayout()
            ->getLayout()
            ->getBlock('cms_page_products_grid');

        $this->renderLayout();
    }

    protected function _initPage()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('page_id');
        $model = Mage::getModel('cms/page');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('cms')->__('This page no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Page'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_page', $model);
        return $this;
    }
    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            //init model and set data
            $model = Mage::getModel('cms/page');

            if ($id = $this->getRequest()->getParam('page_id')) {
                $model->load($id);
            }
            if (isset($data['product_id'])) {
                $data['product_id'] = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['product_id']);
            }
            $model->setData($data);
            Mage::dispatchEvent('cms_page_prepare_save', array('page' => $model, 'request' => $this->getRequest()));

            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('page_id' => $model->getId(), '_current' => true));
                return;
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cms')->__('The page has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('page_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('cms')->__('An error occurred while saving the page.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    public function productsGridAction()
    {
        $this->_initPage();
        $this->loadLayout();
        $this->getLayout()->getBlock('cms_page_products_grid');
        $this->renderLayout();
    }
}