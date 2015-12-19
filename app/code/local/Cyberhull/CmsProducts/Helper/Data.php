<?php
class Cyberhull_CmsProducts_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function setRelatedProductsParameters()
    {
        return array(
            'label'     => $this->__('Related Products'),
            'title'     => $this->__('Related Products'),
            'url'       => Mage::getModel('core/url')->getUrl('*/*/productstab', array('_current' => true)),
            'class'     => 'ajax'
        );
    }

    public function getRelatedProductsCollection()
    {
        $page = Mage::getSingleton('cms/page');
        return Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('entity_id', array('in'=>$page->getProductId()))->addAttributeToSelect('*');
    }
}